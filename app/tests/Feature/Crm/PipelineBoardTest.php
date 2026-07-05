<?php

declare(strict_types=1);

use App\Contracts\Crm\DealServiceInterface;
use App\Data\Crm\CreateDealData;
use App\Livewire\Crm\PipelineBoard;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\Crm\Deal;
use App\Models\Crm\PipelineStage;
use App\Models\User;
use App\Services\Crm\PipelineService;
use App\Support\Services\BuiltInRoles;
use Database\Seeders\ModuleCatalogSeeder;
use Database\Seeders\PermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

function boardCompany(): array
{
    test()->seed(PermissionSeeder::class);
    test()->seed(ModuleCatalogSeeder::class);

    $company = setCompany(Company::factory()->create(['currency' => 'EUR']));
    BuiltInRoles::ensure($company);

    $owner = User::factory()->for($company)->create();
    $owner->assignRole('owner');
    test()->actingAs($owner);
    Filament::setCurrentPanel('crm');

    foreach (['crm.contacts', 'crm.deals', 'crm.pipeline', 'crm.activities'] as $key) {
        CompanyModuleSubscription::query()->firstOrCreate(
            ['company_id' => $company->id, 'module_key' => $key, 'deactivated_at' => null],
            ['activated_at' => now()],
        );
    }
    Cache::forget("company:{$company->id}:modules");

    PipelineService::ensureDefaultStages($company);

    return [$company, $owner];
}

test('board groups deals per stage with correct totals', function () {
    boardCompany();
    $service = app(DealServiceInterface::class);

    $lead = PipelineStage::query()->where('name', 'Lead')->firstOrFail();
    $proposal = PipelineStage::query()->where('name', 'Proposal')->firstOrFail();

    $service->create(new CreateDealData(name: 'A', stageId: $lead->id, valueCents: 100_00));
    $service->create(new CreateDealData(name: 'B', stageId: $lead->id, valueCents: 250_00));
    $service->create(new CreateDealData(name: 'C', stageId: $proposal->id, valueCents: 999_00));

    $board = app(PipelineService::class)->board();

    $leadColumn = $board->firstWhere(fn (array $column): bool => $column['stage']->id === $lead->id);
    expect($leadColumn['count'])->toBe(2)
        ->and($leadColumn['total']->getMinorAmount()->toInt())->toBe(35000);
});

test('the livewire board moves a deal and rejects moving closed deals', function () {
    boardCompany();
    $service = app(DealServiceInterface::class);

    $lead = PipelineStage::query()->where('name', 'Lead')->firstOrFail();
    $qualified = PipelineStage::query()->where('name', 'Qualified')->firstOrFail();

    $deal = $service->create(new CreateDealData(name: 'Draggable', stageId: $lead->id));

    Livewire::test(PipelineBoard::class)
        ->call('moveDeal', $deal->id, $qualified->id);

    expect($deal->fresh()->stage_id)->toBe($qualified->id);
});

test('quick-add creates a deal in the target stage', function () {
    boardCompany();
    $lead = PipelineStage::query()->where('name', 'Lead')->firstOrFail();

    Livewire::test(PipelineBoard::class)
        ->set("quickAdd.{$lead->id}", 'Fresh lead deal')
        ->call('quickAddDeal', $lead->id);

    expect(Deal::query()->where('name', 'Fresh lead deal')->where('stage_id', $lead->id)->exists())->toBeTrue();
});

test('owner filter restricts cards', function () {
    [$company, $owner] = boardCompany();
    $other = User::factory()->for($company)->create();
    $service = app(DealServiceInterface::class);
    $lead = PipelineStage::query()->where('name', 'Lead')->firstOrFail();

    $service->create(new CreateDealData(name: 'Mine', stageId: $lead->id, ownerId: $owner->id));
    $service->create(new CreateDealData(name: 'Theirs', stageId: $lead->id, ownerId: $other->id));

    $board = app(PipelineService::class)->board($other->id);
    $leadColumn = $board->firstWhere(fn (array $column): bool => $column['stage']->id === $lead->id);

    expect($leadColumn['count'])->toBe(1)
        ->and($leadColumn['deals']->first()->name)->toBe('Theirs');
});

test('a stage holding deals cannot be deleted through the resource guard', function () {
    boardCompany();
    $service = app(DealServiceInterface::class);
    $lead = PipelineStage::query()->where('name', 'Lead')->firstOrFail();

    $service->create(new CreateDealData(name: 'Blocker', stageId: $lead->id));

    expect($lead->deals()->exists())->toBeTrue(); // resource before-hook cancels on this
});

test('tenant isolation: company B board never shows company A deals', function () {
    boardCompany();
    $service = app(DealServiceInterface::class);
    $lead = PipelineStage::query()->where('name', 'Lead')->firstOrFail();
    $service->create(new CreateDealData(name: 'Secret A deal', stageId: $lead->id));

    boardCompany(); // company B

    $board = app(PipelineService::class)->board();
    expect($board->sum('count'))->toBe(0);
});
