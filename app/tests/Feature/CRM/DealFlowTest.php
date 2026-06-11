<?php

declare(strict_types=1);

use App\Contracts\CRM\ContactServiceInterface;
use App\Contracts\CRM\DealServiceInterface;
use App\Contracts\Finance\ExpenseServiceInterface;
use App\Data\Finance\SubmitExpenseData;
use App\Events\CRM\DealLost;
use App\Events\CRM\DealWon;
use App\Events\Finance\ExpenseApproved;
use App\Exceptions\CRM\ClosedDealImmutableException;
use App\Exceptions\Finance\CannotApproveOwnExpenseException;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\CRM\Contact;
use App\Models\CRM\Deal;
use App\Models\CRM\PipelineStage;
use App\Models\Finance\ExpenseCategory;
use App\Models\Finance\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->setCompany($this->company);
    $this->user = User::factory()->forCompany($this->company)->create();
    $this->actingAs($this->user, 'web');
    $this->deals = app(DealServiceInterface::class);
    $this->stageA = PipelineStage::factory()->forCompany($this->company)->create(['order' => 1, 'probability_default' => 20]);
    $this->stageB = PipelineStage::factory()->forCompany($this->company)->create(['order' => 2, 'probability_default' => 60]);
});

it('creates a deal with stage-default probability and moves stages', function () {
    $deal = $this->deals->create('Big Deal', $this->stageA->id, 500000);

    expect($deal->probability)->toBe(20.0)
        ->and((string) $deal->status)->toBe('open');

    $deal = $this->deals->moveToStage($deal->id, $this->stageB->id);
    expect($deal->probability)->toBe(60.0)
        ->and($deal->stage_id)->toBe($this->stageB->id);
});

it('weighted pipeline value = sum(value × probability)', function () {
    $this->deals->create('A', $this->stageA->id, 100000); // 20% → 20000
    $this->deals->create('B', $this->stageB->id, 200000); // 60% → 120000

    expect($this->deals->weightedPipelineValue()->getMinorAmount()->toInt())->toBe(140000);
});

it('winning a deal fires DealWon and creates a draft invoice stub (cross-domain)', function () {
    // Listener no-ops unless finance.invoicing is active (event-bus contract).
    CompanyModuleSubscription::factory()
        ->forCompany($this->company)->module('finance.invoicing')->create();

    $contact = Contact::factory()->forCompany($this->company)->create(['email' => 'buyer@client.test']);
    $deal = $this->deals->create('Won Deal', $this->stageA->id, 250000, contactId: $contact->id);

    $deal = $this->deals->win($deal->id);

    expect((string) $deal->status)->toBe('won')
        ->and($deal->probability)->toBe(100.0);

    // Sync queue: CreateInvoiceStubListener ran — draft invoice, never auto-sent.
    $invoice = Invoice::query()->where('source_deal_id', $deal->id)->first();
    expect($invoice)->not->toBeNull()
        ->and((string) $invoice->status)->toBe('draft')
        ->and($invoice->total_cents)->toBe(250000)
        ->and($invoice->invoice_number)->toBeNull();
});

it('losing requires a reason and fires DealLost', function () {
    Event::fake([DealLost::class]);
    $deal = $this->deals->create('Lost Deal', $this->stageA->id, 100000);

    $deal = $this->deals->lose($deal->id, 'Went with competitor');

    expect((string) $deal->status)->toBe('lost')
        ->and($deal->lost_reason)->toBe('Went with competitor');
    Event::assertDispatched(DealLost::class, fn ($e) => $e->lost_reason === 'Went with competitor');
});

it('closed deals cannot change stage', function () {
    Event::fake([DealWon::class]);
    $deal = $this->deals->create('Closed', $this->stageA->id, 1000);
    $this->deals->win($deal->id);

    $this->deals->moveToStage($deal->id, $this->stageB->id);
})->throws(ClosedDealImmutableException::class);

it('findOrCreateByEmail is idempotent', function () {
    $contacts = app(ContactServiceInterface::class);

    $first = $contacts->findOrCreateByEmail('dup@client.test', ['first_name' => 'Dana']);
    $second = $contacts->findOrCreateByEmail('dup@client.test');

    expect($second->id)->toBe($first->id)
        ->and(Contact::count())->toBe(1);
});

it('expense approval fires ExpenseApproved and blocks self-approval', function () {
    Event::fake([ExpenseApproved::class]);
    $category = ExpenseCategory::factory()->forCompany($this->company)->create([
        'limit_per_transaction_cents' => 10000,
    ]);

    $expense = app(ExpenseServiceInterface::class)->submit(new SubmitExpenseData(
        category_id: $category->id, amount_cents: 15000,
        expense_date: now()->toDateString(), merchant: 'Train BV',
    ));

    expect($expense->is_over_limit)->toBeTrue(); // over the €100 category limit

    // Self-approval blocked
    try {
        app(ExpenseServiceInterface::class)->approve($expense->id);
        $this->fail('Expected CannotApproveOwnExpenseException');
    } catch (CannotApproveOwnExpenseException) {
    }

    $this->actingAs(User::factory()->forCompany($this->company)->create(), 'web');
    $approved = app(ExpenseServiceInterface::class)->approve($expense->id);

    expect((string) $approved->status)->toBe('approved');
    Event::assertDispatched(ExpenseApproved::class, fn ($e) => $e->amount_cents === 15000);
});

it('isolates deals between companies', function () {
    $this->deals->create('Mine', $this->stageA->id, 1000);

    $this->setCompany(Company::factory()->create());
    expect(Deal::count())->toBe(0);
});
