<?php

use App\Enums\Crm\DealStatus;
use App\Enums\Crm\TicketPriority;
use App\Enums\Crm\TicketStatus;
use App\Events\Crm\DealLost;
use App\Events\Crm\DealWon;
use App\Events\Crm\TicketResolved;
use App\Listeners\Crm\LogDealLost;
use App\Listeners\Crm\LogDealWon;
use App\Listeners\Crm\NotifyTicketResolved;
use App\Models\Crm\CrmContact;
use App\Models\Crm\Deal;
use App\Models\Crm\DealStage;
use App\Models\Crm\Pipeline;
use App\Models\Crm\Ticket;
use Illuminate\Support\Facades\Event;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    $this->contact = CrmContact::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'first_name' => 'Jane',
        'last_name'  => 'Doe',
        'email'      => 'jane@example.com',
        'type'       => 'customer',
    ]);

    $this->pipeline = Pipeline::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'name'       => 'Sales Pipeline',
        'is_default' => true,
    ]);

    $this->stage = DealStage::withoutGlobalScopes()->create([
        'company_id'         => $this->company->id,
        'pipeline_id'        => $this->pipeline->id,
        'name'               => 'Proposal',
        'sort_order'         => 1,
        'probability_percent'=> 50,
    ]);
});

// ---------- Deal Events ----------

it('DealWon event has correct listeners registered', function () {
    Event::fake();

    $deal = Deal::withoutGlobalScopes()->create([
        'company_id'     => $this->company->id,
        'crm_contact_id' => $this->contact->id,
        'pipeline_id'    => $this->pipeline->id,
        'deal_stage_id'  => $this->stage->id,
        'title'          => 'Enterprise Deal',
        'value'          => '50000.00',
        'currency'       => 'EUR',
        'status'         => DealStatus::Won->value,
        'closed_at'      => now(),
    ]);

    event(new DealWon($deal));

    Event::assertDispatched(DealWon::class);
    Event::assertListening(DealWon::class, LogDealWon::class);
});

it('DealLost event has correct listeners registered', function () {
    Event::fake();

    $deal = Deal::withoutGlobalScopes()->create([
        'company_id'     => $this->company->id,
        'crm_contact_id' => $this->contact->id,
        'pipeline_id'    => $this->pipeline->id,
        'deal_stage_id'  => $this->stage->id,
        'title'          => 'Lost Deal',
        'value'          => '10000.00',
        'currency'       => 'EUR',
        'status'         => DealStatus::Lost->value,
        'lost_reason'    => 'Too expensive',
        'closed_at'      => now(),
    ]);

    event(new DealLost($deal));

    Event::assertDispatched(DealLost::class);
    Event::assertListening(DealLost::class, LogDealLost::class);
});

// ---------- Ticket Events ----------

it('TicketResolved event has correct listeners registered', function () {
    Event::fake();

    $ticket = Ticket::withoutGlobalScopes()->create([
        'company_id'     => $this->company->id,
        'crm_contact_id' => $this->contact->id,
        'subject'        => 'Login issue',
        'status'         => TicketStatus::Resolved->value,
        'priority'       => TicketPriority::High->value,
        'assigned_to'    => $this->tenant->id,
        'resolved_at'    => now(),
    ]);

    event(new TicketResolved($ticket));

    Event::assertDispatched(TicketResolved::class);
    Event::assertListening(TicketResolved::class, NotifyTicketResolved::class);
});

// ---------- Listener implements ShouldQueue ----------

it('all CRM listeners implement ShouldQueue', function () {
    $listeners = [
        LogDealWon::class,
        LogDealLost::class,
        NotifyTicketResolved::class,
    ];

    foreach ($listeners as $listener) {
        expect(is_a($listener, \Illuminate\Contracts\Queue\ShouldQueue::class, true))
            ->toBeTrue("$listener does not implement ShouldQueue");
    }
});
