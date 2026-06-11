<?php

declare(strict_types=1);

use App\Contracts\CRM\DealServiceInterface;
use App\Contracts\CRM\SequenceServiceInterface;
use App\Exceptions\CRM\AlreadyEnrolledException;
use App\Exceptions\CRM\InvalidSegmentConditionException;
use App\Exceptions\CRM\RoomUnavailableException;
use App\Exceptions\CRM\SelfReferralException;
use App\Exceptions\CRM\SlotTakenException;
use App\Models\Company;
use App\Models\CRM\Account;
use App\Models\CRM\Activity;
use App\Models\CRM\Contact;
use App\Models\CRM\CrmEmail;
use App\Models\CRM\Deal;
use App\Models\CRM\EmailConnection;
use App\Models\CRM\MeetingType;
use App\Models\CRM\PipelineStage;
use App\Models\CRM\PriceBook;
use App\Models\CRM\Product;
use App\Models\CRM\Quota;
use App\Models\CRM\ReferralProgram;
use App\Models\CRM\Segment;
use App\Models\CRM\Sequence;
use App\Models\CRM\SequenceEnrolment;
use App\Models\CRM\VolumeDiscount;
use App\Models\CRM\WinLoss;
use App\Models\User;
use App\Services\CRM\ContractService;
use App\Services\CRM\DealHealthService;
use App\Services\CRM\DealRoomService;
use App\Services\CRM\EmailSyncService;
use App\Services\CRM\PricingService;
use App\Services\CRM\ReferralService;
use App\Services\CRM\SalesForecastService;
use App\Services\CRM\SchedulingService;
use App\Services\CRM\SegmentService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->setCompany($this->company);
    $this->user = User::factory()->forCompany($this->company)->create();
    $this->actingAs($this->user, 'web');
});

function makeDeal($test, int $cents = 100000, ?string $contactId = null): Deal
{
    $stage = PipelineStage::factory()->forCompany($test->company)->create();

    return Deal::create([
        'company_id' => $test->company->id, 'name' => 'Test Deal '.fake()->uuid(),
        'stage_id' => $stage->id, 'owner_id' => $test->user->id,
        'contact_id' => $contactId, 'value_cents' => $cents, 'probability' => 50,
        'stage_entered_at' => now(),
    ])->refresh();
}

// --- crm.forecasting ---
it('rolls up forecast categories, attainment and coverage per quota', function () {
    Quota::create([
        'company_id' => $this->company->id, 'owner_id' => $this->user->id,
        'period' => '2026-Q2', 'quota_cents' => 1000000,
    ]);
    $svc = app(SalesForecastService::class);

    $open = makeDeal($this, 400000);
    $svc->setCategory($open->id, 'commit');
    $won = makeDeal($this, 600000);
    app(DealServiceInterface::class)->win($won->id);

    $forecast = $svc->forecast('2026-Q2', $this->user->id);
    expect($forecast['categories']['commit'])->toBe(400000)
        ->and($forecast['categories']['closed'])->toBe(600000)
        ->and($forecast['attainment'])->toBe(60.0);

    // closed deal can't be re-categorised
    expect(fn () => $svc->setCategory($won->id, 'commit'))
        ->toThrow(ValidationException::class);

    // snapshots idempotent within a week
    expect($svc->captureSnapshots('2026-Q2'))->toBe(1)
        ->and($svc->captureSnapshots('2026-Q2'))->toBe(0);
});

// --- crm.segments ---
it('resolves dynamic segments query-time, validates conditions, handles static lists', function () {
    $svc = app(SegmentService::class);
    Contact::factory()->forCompany($this->company)->create(['lifecycle_stage' => 'customer']);
    Contact::factory()->forCompany($this->company)->create(['lifecycle_stage' => 'lead']);

    $segment = Segment::create([
        'company_id' => $this->company->id, 'name' => 'Customers', 'type' => 'dynamic',
        'conditions' => ['logic' => 'and', 'rules' => [['field' => 'lifecycle_stage', 'operator' => 'equals', 'value' => 'customer']]],
    ]);

    expect($svc->contacts($segment->id)->count())->toBe(1);

    // membership reflects changes immediately — query-time
    Contact::factory()->forCompany($this->company)->create(['lifecycle_stage' => 'customer']);
    expect($svc->contacts($segment->id)->count())->toBe(2)
        ->and($svc->preview($segment->conditions))->toBe(2);

    expect(fn () => $svc->validateConditions(['rules' => [['field' => 'password', 'operator' => 'equals']]]))
        ->toThrow(InvalidSegmentConditionException::class);

    // static list
    $static = Segment::create(['company_id' => $this->company->id, 'name' => 'VIPs', 'type' => 'static']);
    $vip = Contact::factory()->forCompany($this->company)->create();
    $static->members()->create(['company_id' => $this->company->id, 'contact_id' => $vip->id]);
    expect($svc->contacts($static->id)->count())->toBe(1);
});

// --- crm.scheduling ---
it('lists slots inside working hours and rejects double-booking', function () {
    $type = MeetingType::create([
        'company_id' => $this->company->id, 'owner_id' => $this->user->id,
        'name' => 'Intro call', 'slug' => 'intro-call', 'duration_minutes' => 30, 'buffer_minutes' => 15,
    ]);
    $svc = app(SchedulingService::class);

    $monday = CarbonImmutable::parse('next monday');
    $slots = $svc->slots('intro-call', $monday);
    expect($slots)->not->toBeEmpty()
        ->and($slots[0])->toContain('09:00');

    $booking = $svc->book('intro-call', $slots[0], 'prospect@lead.test', 'Pro', 'Spect');
    expect($booking->status)->toBe('confirmed')
        ->and(Contact::query()->where('email', 'prospect@lead.test')->exists())->toBeTrue();

    // same slot again → taken
    $svc->book('intro-call', $slots[0], 'other@lead.test', 'Ot', 'Her');
})->throws(SlotTakenException::class);

// --- crm.deal-rooms ---
it('serves deal rooms by token, tracks views, blocks revoked rooms, buyer-side toggles only', function () {
    $deal = makeDeal($this);
    $svc = app(DealRoomService::class);
    $room = $svc->create($deal->id);

    $room->documents()->create(['company_id' => $this->company->id, 'name' => 'Proposal', 'path' => 'x.pdf']);
    $buyerItem = $room->actionItems()->create(['company_id' => $this->company->id, 'description' => 'Review proposal', 'owner_side' => 'buyer']);
    $sellerItem = $room->actionItems()->create(['company_id' => $this->company->id, 'description' => 'Send contract', 'owner_side' => 'seller']);

    $public = $svc->publicView($room->access_token);
    expect($public->documents)->toHaveCount(1);

    $svc->trackDocumentView($room->access_token, $room->documents->first()->id);
    expect($room->documents()->first()->view_count)->toBe(1);

    $svc->toggleActionItem($room->access_token, $buyerItem->id, 'buyer');
    expect($buyerItem->fresh()->status)->toBe('done');
    expect(fn () => $svc->toggleActionItem($room->access_token, $sellerItem->id, 'buyer'))
        ->toThrow(ModelNotFoundException::class);

    $svc->revoke($room->id);
    expect(fn () => $svc->publicView($room->access_token))->toThrow(RoomUnavailableException::class);

    // one room per deal
    expect(fn () => $svc->create($deal->id))->toThrow(QueryException::class);
});

// --- crm.contracts ---
it('runs the contract lifecycle: from deal, sign with PDF, auto-renew, alerts once, MRR math', function () {
    Storage::fake();
    $account = Account::factory()->forCompany($this->company)->create();
    $deal = makeDeal($this, 1200000);
    $deal->update(['account_id' => $account->id]);

    $svc = app(ContractService::class);
    $contract = $svc->createFromDeal($deal->id, 'yearly');
    expect($contract->account_id)->toBe($account->id)
        ->and($contract->value_cents)->toBe(1200000);

    $svc->send($contract->id);
    $contract = $svc->markSigned($contract->id, UploadedFile::fake()->create('signed.pdf', 100, 'application/pdf'));
    expect((string) $contract->status)->toBe('active')
        ->and($contract->signed_pdf_path)->toContain("companies/{$this->company->id}/contracts");

    // yearly 12000 → monthly 1000
    expect($svc->recurringRevenue()->getMinorAmount()->toInt())->toBe(100000);

    // expiry within 30d → alert fires once per level
    $contract->update(['end_date' => now()->addDays(20)->toDateString()]);
    expect($svc->runLifecycle()['alerted'])->toBe(2) // 90 + 30 both within window
        ->and($svc->runLifecycle()['alerted'])->toBe(0);

    // past end + auto_renew → renewed
    $contract->update(['end_date' => now()->subDay()->toDateString(), 'auto_renew' => true]);
    expect($svc->runLifecycle()['renewed'])->toBe(1)
        ->and(CarbonImmutable::parse($contract->fresh()->end_date->toDateString())->isFuture())->toBeTrue();
});

// --- crm.email ---
it('syncs mail with dedupe, encrypts tokens, matches contacts, hides private mail', function () {
    $contact = Contact::factory()->forCompany($this->company)->create(['email' => 'klant@acme.test']);
    $connection = EmailConnection::create([
        'company_id' => $this->company->id, 'user_id' => $this->user->id,
        'provider' => 'gmail', 'oauth_token' => 'secret-token-abc', 'email_address' => 'rep@flowflex.test',
    ]);

    expect(DB::table('crm_email_connections')->value('oauth_token'))->not->toContain('secret-token-abc');

    Http::fake([
        'gmail.googleapis.com/*' => Http::response(['messages' => [
            ['message_id' => 'm1', 'direction' => 'inbound', 'from' => 'klant@acme.test', 'to' => 'rep@flowflex.test',
                'subject' => 'Re: offer', 'body' => '<p>Sounds good</p><script>alert(1)</script>', 'sent_at' => now()->toIso8601String()],
        ]]),
    ]);

    $svc = app(EmailSyncService::class);
    expect($svc->sync($connection->id)['synced'])->toBe(1);
    expect($svc->sync($connection->id)['skipped'])->toBe(1); // dedupe on message_id

    $email = CrmEmail::query()->firstOrFail();
    expect($email->contact_id)->toBe($contact->id)
        ->and($email->body)->not->toContain('<script>'); // purified

    // private visibility scope
    $email->update(['visibility' => 'private']);
    $other = User::factory()->forCompany($this->company)->create();
    expect(CrmEmail::query()->visibleTo($other->id)->count())->toBe(0)
        ->and(CrmEmail::query()->visibleTo($this->user->id)->count())->toBe(1);
});

// --- crm.sequences ---
it('enrols once, advances steps in order, pauses on reply, triggers from DealWon', function () {
    $contact = Contact::factory()->forCompany($this->company)->create();
    $sequence = Sequence::create([
        'company_id' => $this->company->id, 'name' => 'Outbound', 'trigger_type' => 'manual',
    ]);
    $sequence->steps()->createMany([
        ['company_id' => $this->company->id, 'order' => 1, 'type' => 'email', 'config' => ['subject' => 'Hi'], 'wait_days' => 0],
        ['company_id' => $this->company->id, 'order' => 2, 'type' => 'call', 'config' => ['text' => 'Call them'], 'wait_days' => 2],
    ]);

    $svc = app(SequenceServiceInterface::class);
    $enrolment = $svc->enrol($sequence->id, $contact->id);

    expect(fn () => $svc->enrol($sequence->id, $contact->id))->toThrow(AlreadyEnrolledException::class);

    $result = $svc->advanceDue();
    expect($result['advanced'])->toBe(1)
        ->and($enrolment->fresh()->current_step)->toBe(1)
        ->and($svc->advanceDue()['advanced'])->toBe(0); // step 2 waits 2 days — idempotent in window

    $svc->pauseOnReply($contact->id);
    expect($enrolment->fresh()->status)->toBe('paused');

    // deal-won trigger
    $winSeq = Sequence::create([
        'company_id' => $this->company->id, 'name' => 'Success', 'trigger_type' => 'deal-won',
    ]);
    $winSeq->steps()->create(['company_id' => $this->company->id, 'order' => 1, 'type' => 'task', 'config' => ['text' => 'Send welcome pack']]);

    $deal = makeDeal($this, 50000, $contact->id);
    app(DealServiceInterface::class)->win($deal->id); // listener queued sync in tests

    expect(SequenceEnrolment::query()->where('sequence_id', $winSeq->id)->where('contact_id', $contact->id)->exists())->toBeTrue();
});

// --- crm.pricing ---
it('resolves price by book order, volume tier and margin guard', function () {
    $svc = app(PricingService::class);
    $product = Product::create([
        'company_id' => $this->company->id, 'name' => 'Licence', 'sku' => 'LIC-1',
        'standard_price_cents' => 10000, 'cost_cents' => 6000,
    ]);
    VolumeDiscount::create(['company_id' => $this->company->id, 'product_id' => $product->id, 'min_quantity' => 10, 'discount_percent' => 10]);
    VolumeDiscount::create(['company_id' => $this->company->id, 'product_id' => $product->id, 'min_quantity' => 50, 'discount_percent' => 40]);

    // standard price, no tier
    expect($svc->resolve($product->id, null, 1)['unit_price_cents'])->toBe(10000);

    // highest qualifying tier (50+ → 40%) → 6000 < cost+10% → margin flag
    $bulk = $svc->resolve($product->id, null, 60);
    expect($bulk['unit_price_cents'])->toBe(6000)
        ->and($bulk['below_margin'])->toBeTrue();

    // account book beats default
    $book = PriceBook::create(['company_id' => $this->company->id, 'name' => 'Partner', 'is_default' => false]);
    $book->entries()->create(['company_id' => $this->company->id, 'product_id' => $product->id, 'price_cents' => 8000]);
    $account = Account::factory()->forCompany($this->company)->create();
    $account->forceFill(['price_book_id' => $book->id])->save();

    expect($svc->resolve($product->id, $account->id, 1)['source'])->toBe('account-book')
        ->and($svc->resolve($product->id, $account->id, 1)['unit_price_cents'])->toBe(8000);

    // duplicate SKU rejected
    expect(fn () => Product::create([
        'company_id' => $this->company->id, 'name' => 'Dup', 'sku' => 'LIC-1', 'standard_price_cents' => 1,
    ]))->toThrow(QueryException::class);
});

// --- crm.referrals ---
it('blocks self-referral and duplicates, tracks status to rewarded, ranks leaderboard', function () {
    $program = ReferralProgram::create(['company_id' => $this->company->id, 'name' => 'Refer & earn', 'is_active' => true]);
    $referrer = Contact::factory()->forCompany($this->company)->create(['email' => 'referrer@acme.test']);

    $svc = app(ReferralService::class);
    $code = $svc->codeFor($referrer->id, $program->id);
    expect($svc->codeFor($referrer->id, $program->id))->toBe($code); // generate-or-return

    expect(fn () => $svc->register($code, 'referrer@acme.test'))->toThrow(SelfReferralException::class);

    $referral = $svc->register($code, 'friend@new.test');
    $svc->qualify($referral->id);
    $svc->markRewarded($referral->id);
    expect($referral->fresh()->status)->toBe('rewarded');

    expect(fn () => $svc->register($code, 'friend@new.test'))
        ->toThrow(ValidationException::class); // duplicate referee

    expect($svc->leaderboard($program->id)->first()->referral_count)->toBe(1);
});

// --- crm.revenue-intelligence ---
it('scores deal health deterministically, surfaces at-risk deals, records win/loss on close', function () {
    $healthy = makeDeal($this);
    Activity::create([
        'company_id' => $this->company->id, 'type' => 'call', 'subject' => 'Demo call',
        'deal_id' => $healthy->id, 'owner_id' => $this->user->id, 'completed_at' => now(),
    ]);

    $stalled = makeDeal($this);
    $stalled->forceFill(['created_at' => now()->subDays(120), 'stage_entered_at' => now()->subDays(60)])->save();

    $svc = app(DealHealthService::class);
    $result = $svc->recalculate();
    expect($result['scored'])->toBe(2)
        ->and($svc->recalculate()['scored'])->toBe(2); // idempotent upsert

    $atRisk = $svc->atRisk();
    expect($atRisk->pluck('deal_id'))->toContain($stalled->id)
        ->and($atRisk->pluck('deal_id'))->not->toContain($healthy->id);

    // win/loss row from close path
    app(DealServiceInterface::class)->lose($stalled->id, 'price');
    $row = WinLoss::query()->where('deal_id', $stalled->id)->firstOrFail();
    expect($row->outcome)->toBe('lost')->and($row->reason)->toBe('price');
});
