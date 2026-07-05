<?php

declare(strict_types=1);

use App\Contracts\Crm\DealServiceInterface;
use App\Data\Crm\CloseDealData;
use App\Data\Crm\CreateDealData;
use App\Events\Crm\DealLost;
use App\Events\Crm\DealStageChanged;
use App\Events\Crm\DealWon;
use App\Exceptions\Crm\ClosedDealImmutableException;
use App\Filament\Crm\Resources\DealResource;
use App\Models\Company;
use App\Models\Crm\DealProduct;
use App\Models\Crm\PipelineStage;
use App\Models\User;
use App\Services\Crm\PipelineService;
use App\Support\Services\BuiltInRoles;
use Database\Seeders\ModuleCatalogSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;

function dealCompany(): array
{
    test()->seed(PermissionSeeder::class);
    test()->seed(ModuleCatalogSeeder::class);

    $company = setCompany(Company::factory()->create(['currency' => 'EUR']));
    BuiltInRoles::ensure($company);

    $owner = User::factory()->for($company)->create();
    $owner->assignRole('owner');
    test()->actingAs($owner);

    $pipeline = PipelineService::ensureDefaultStages($company);

    return [$company, $owner, $pipeline];
}

function firstStage(): PipelineStage
{
    return PipelineStage::query()->orderBy('order')->firstOrFail();
}

test('default stages seed exactly once with one won and one lost stage', function () {
    [$company] = dealCompany();
    PipelineService::ensureDefaultStages($company); // second call = no-op

    $stages = PipelineStage::query()->get();

    expect($stages)->toHaveCount(5)
        ->and($stages->where('is_won', true))->toHaveCount(1)
        ->and($stages->where('is_lost', true))->toHaveCount(1);
});

test('creating a deal applies the stage default probability and stamps stage_entered_at', function () {
    dealCompany();

    $deal = app(DealServiceInterface::class)->create(new CreateDealData(
        name: 'Big rollout', stageId: firstStage()->id, valueCents: 250_000,
    ));

    expect((float) $deal->probability)->toBe((float) firstStage()->probability_default)
        ->and($deal->stage_entered_at)->not->toBeNull()
        ->and((string) $deal->status)->toBe('open');
});

test('stage move resets entered-at, applies stage probability and broadcasts', function () {
    Event::fake([DealStageChanged::class]);
    dealCompany();

    $service = app(DealServiceInterface::class);
    $deal = $service->create(new CreateDealData(name: 'Move me', stageId: firstStage()->id));

    $target = PipelineStage::query()->where('name', 'Proposal')->firstOrFail();
    $moved = $service->moveToStage($deal->id, $target->id);

    expect($moved->stage_id)->toBe($target->id)
        ->and((float) $moved->probability)->toBe(60.0);

    Event::assertDispatched(DealStageChanged::class, fn (DealStageChanged $event): bool => $event->deal_id === $deal->id && $event->to_stage_id === $target->id);
});

test('close as won fires DealWon with the contract payload', function () {
    Event::fake([DealWon::class]);
    dealCompany();

    $service = app(DealServiceInterface::class);
    $deal = $service->create(new CreateDealData(name: 'Winner', stageId: firstStage()->id, valueCents: 990_00));

    $closed = $service->close(new CloseDealData(dealId: $deal->id, outcome: 'won'));

    expect((string) $closed->status)->toBe('won')
        ->and($closed->actual_close_date)->not->toBeNull();

    Event::assertDispatched(DealWon::class, fn (DealWon $event): bool => $event->deal_id === $deal->id
        && $event->company_id === $deal->company_id
        && $event->value_cents === 99000
        && $event->currency === 'EUR');
});

test('close as lost requires a reason and fires DealLost', function () {
    Event::fake([DealLost::class]);
    dealCompany();

    $service = app(DealServiceInterface::class);
    $deal = $service->create(new CreateDealData(name: 'Loser', stageId: firstStage()->id));

    expect(fn () => $service->close(new CloseDealData(dealId: $deal->id, outcome: 'lost')))
        ->toThrow(ValidationException::class);

    $service->close(new CloseDealData(dealId: $deal->id, outcome: 'lost', lostReason: 'Budget cut', lostTo: 'CompetitorX'));

    Event::assertDispatched(DealLost::class, fn (DealLost $event): bool => $event->lost_reason === 'Budget cut');
});

test('a closed deal can neither move stage nor close again', function () {
    dealCompany();
    $service = app(DealServiceInterface::class);

    $deal = $service->create(new CreateDealData(name: 'Done deal', stageId: firstStage()->id));
    $service->close(new CloseDealData(dealId: $deal->id, outcome: 'won'));

    expect(fn () => $service->moveToStage($deal->id, firstStage()->id))
        ->toThrow(ClosedDealImmutableException::class);
    expect(fn () => $service->close(new CloseDealData(dealId: $deal->id, outcome: 'lost', lostReason: 'x')))
        ->toThrow(ClosedDealImmutableException::class);
});

test('dragging into a won or lost stage closes the deal through the same path', function () {
    Event::fake([DealWon::class, DealStageChanged::class]);
    dealCompany();
    $service = app(DealServiceInterface::class);

    $deal = $service->create(new CreateDealData(name: 'Board close', stageId: firstStage()->id));
    $wonStage = PipelineStage::query()->where('is_won', true)->firstOrFail();

    $moved = $service->moveToStage($deal->id, $wonStage->id);

    expect((string) $moved->status)->toBe('won');
    Event::assertDispatched(DealWon::class);
});

test('weighted pipeline value uses integer money math over open deals only', function () {
    dealCompany();
    $service = app(DealServiceInterface::class);

    $stage = firstStage(); // 10% default
    $service->create(new CreateDealData(name: 'A', stageId: $stage->id, valueCents: 100_000)); // 10k weighted
    $won = $service->create(new CreateDealData(name: 'B', stageId: $stage->id, valueCents: 500_000));
    $service->close(new CloseDealData(dealId: $won->id, outcome: 'won')); // excluded

    expect($service->weightedPipelineValue()->getMinorAmount()->toInt())->toBe(10_000);
});

test('duplicate copies contacts and products and resets status to the first open stage', function () {
    dealCompany();
    $service = app(DealServiceInterface::class);

    $deal = $service->create(new CreateDealData(name: 'Original', stageId: firstStage()->id, valueCents: 42_00));
    DealProduct::query()->create([
        'company_id' => $deal->company_id, 'deal_id' => $deal->id,
        'description' => 'Licences', 'quantity' => 3, 'unit_price_cents' => 14_00,
    ]);
    $service->close(new CloseDealData(dealId: $deal->id, outcome: 'won'));

    $copy = $service->duplicate($deal->id);

    expect((string) $copy->status)->toBe('open')
        ->and($copy->name)->toBe('Original (copy)')
        ->and($copy->products()->count())->toBe(1)
        ->and($copy->value_cents)->toBe(4200);
});

test('module gating hides the deal resource when crm.deals is inactive', function () {
    dealCompany();

    expect(DealResource::canAccess())->toBeFalse();
});
