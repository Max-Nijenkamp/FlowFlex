<?php

use App\Enums\Crm\ContactType;
use App\Enums\Crm\DealStatus;
use App\Enums\Crm\TicketPriority;
use App\Enums\Crm\TicketStatus;
use App\Models\Crm\CannedResponse;
use App\Models\Crm\ChatbotRule;
use App\Models\Crm\CrmActivity;
use App\Models\Crm\CrmCompany;
use App\Models\Crm\CrmContact;
use App\Models\Crm\CrmContactCustomField;
use App\Models\Crm\CrmContactCustomFieldValue;
use App\Models\Crm\CsatResponse;
use App\Models\Crm\CsatSurvey;
use App\Models\Crm\Deal;
use App\Models\Crm\DealNote;
use App\Models\Crm\DealStage;
use App\Models\Crm\InboxEmail;
use App\Models\Crm\Pipeline;
use App\Models\Crm\SharedInbox;
use App\Models\Crm\Ticket;
use App\Models\Crm\TicketSlaRule;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);
});

// ---------- CrmContact ----------

it('can create a crm contact with ULID key', function () {
    $contact = CrmContact::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'first_name' => 'John',
        'last_name'  => 'Doe',
        'email'      => 'john.doe@example.com',
        'type'       => ContactType::Lead->value,
    ]);

    expect($contact->exists)->toBeTrue();
    expect($contact->id)->toHaveLength(26);
    expect($contact->type)->toBe(ContactType::Lead);
    expect($contact->full_name)->toBe('John Doe');
});

it('crm contact supports soft deletes', function () {
    $contact = CrmContact::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'first_name' => 'Jane',
        'last_name'  => 'Smith',
        'type'       => ContactType::Customer->value,
    ]);

    $contact->delete();
    expect($contact->trashed())->toBeTrue();
    expect(CrmContact::withTrashed()->withoutGlobalScopes()->find($contact->id))->not->toBeNull();
});

it('crm contact has company scope', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);

    $contact = CrmContact::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'first_name' => 'Bob',
        'last_name'  => 'Jones',
        'type'       => ContactType::Lead->value,
    ]);

    $this->actingAs($otherTenant, 'tenant');
    expect(CrmContact::all()->pluck('id'))->not->toContain($contact->id);
});

// ---------- CrmCompany ----------

it('can create a crm company', function () {
    $crmCompany = CrmCompany::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'name'       => 'Acme Corp',
        'website'    => 'https://acme.com',
        'industry'   => 'Technology',
    ]);

    expect($crmCompany->exists)->toBeTrue();
    expect($crmCompany->id)->toHaveLength(26);
    expect($crmCompany->name)->toBe('Acme Corp');
});

it('crm company supports soft deletes', function () {
    $crmCompany = CrmCompany::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'name'       => 'Test Corp',
    ]);

    $crmCompany->delete();
    expect($crmCompany->trashed())->toBeTrue();
});

// ---------- Pipeline ----------

it('can create a pipeline', function () {
    $pipeline = Pipeline::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'name'       => 'Sales Pipeline',
        'is_default' => true,
    ]);

    expect($pipeline->exists)->toBeTrue();
    expect($pipeline->is_default)->toBeTrue();
});

// ---------- DealStage ----------

it('can create a deal stage', function () {
    $pipeline = Pipeline::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'name'       => 'Default',
        'is_default' => true,
    ]);

    $stage = DealStage::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'pipeline_id' => $pipeline->id,
        'name'        => 'Proposal',
        'sort_order'  => 1,
        'probability' => 50,
    ]);

    expect($stage->exists)->toBeTrue();
    expect($stage->probability)->toBe(50);
});

// ---------- Deal ----------

it('can create a deal with correct enum cast', function () {
    $deal = Deal::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'title'      => 'New Software Deal',
        'value'      => '5000.00',
        'currency'   => 'EUR',
        'status'     => DealStatus::Open->value,
    ]);

    expect($deal->exists)->toBeTrue();
    expect($deal->status)->toBe(DealStatus::Open);
    expect($deal->id)->toHaveLength(26);
});

it('deal supports soft deletes', function () {
    $deal = Deal::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'title'      => 'Deal to delete',
        'status'     => DealStatus::Open->value,
    ]);

    $deal->delete();
    expect($deal->trashed())->toBeTrue();
});

// ---------- Ticket ----------

it('can create a ticket with enum casts', function () {
    $ticket = Ticket::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'subject'    => 'Login issue',
        'status'     => TicketStatus::Open->value,
        'priority'   => TicketPriority::High->value,
    ]);

    expect($ticket->exists)->toBeTrue();
    expect($ticket->status)->toBe(TicketStatus::Open);
    expect($ticket->priority)->toBe(TicketPriority::High);
});

it('ticket supports soft deletes', function () {
    $ticket = Ticket::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'subject'    => 'Bug report',
        'status'     => TicketStatus::Open->value,
        'priority'   => TicketPriority::Normal->value,
    ]);

    $ticket->delete();
    expect($ticket->trashed())->toBeTrue();
});

// ---------- CannedResponse ----------

it('can create a canned response', function () {
    $response = CannedResponse::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'title'      => 'Thanks for reaching out',
        'body'       => 'Thank you for your message. We will respond shortly.',
        'is_shared'  => true,
    ]);

    expect($response->exists)->toBeTrue();
    expect($response->is_shared)->toBeTrue();
});

// ---------- TicketSlaRule ----------

it('can create a ticket SLA rule', function () {
    $rule = TicketSlaRule::withoutGlobalScopes()->create([
        'company_id'           => $this->company->id,
        'name'                 => 'Normal SLA',
        'priority'             => TicketPriority::Normal->value,
        'first_response_hours' => 8,
        'resolution_hours'     => 24,
        'is_active'            => true,
    ]);

    expect($rule->exists)->toBeTrue();
    expect($rule->priority)->toBe(TicketPriority::Normal);
    expect($rule->id)->toHaveLength(26);
});

// ---------- ChatbotRule ----------

it('can create a chatbot rule', function () {
    $rule = ChatbotRule::withoutGlobalScopes()->create([
        'company_id'       => $this->company->id,
        'name'             => 'FAQ Rule',
        'trigger_keywords' => ['faq', 'help'],
        'response_body'    => 'Please visit our FAQ page.',
        'is_active'        => true,
        'sort_order'       => 1,
    ]);

    expect($rule->exists)->toBeTrue();
    expect($rule->trigger_keywords)->toBe(['faq', 'help']);
    expect($rule->is_active)->toBeTrue();
});

// ---------- DealNote ----------

it('can create a deal note', function () {
    $pipeline = Pipeline::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'name'       => 'Pipeline',
        'is_default' => true,
    ]);
    $stage = DealStage::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'pipeline_id' => $pipeline->id,
        'name'        => 'Stage 1',
        'sort_order'  => 1,
        'probability' => 25,
    ]);
    $deal = Deal::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'title'      => 'Deal for notes',
        'status'     => DealStatus::Open->value,
    ]);
    $note = DealNote::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'deal_id'     => $deal->id,
        'tenant_id'   => $this->tenant->id,
        'body'        => 'Called the client, follow up next week.',
    ]);

    expect($note->exists)->toBeTrue();
    expect($note->id)->toHaveLength(26);
});

// ---------- CrmActivity ----------

it('can create a CRM activity', function () {
    $contact = CrmContact::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'first_name' => 'Activity',
        'last_name'  => 'Person',
        'type'       => ContactType::Lead->value,
    ]);
    $activity = CrmActivity::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'subject_type' => CrmContact::class,
        'subject_id'   => $contact->id,
        'tenant_id'    => $this->tenant->id,
        'type'         => 'call',
        'description'  => 'Discovery call',
        'occurred_at'  => now(),
    ]);

    expect($activity->exists)->toBeTrue();
    expect($activity->id)->toHaveLength(26);
});

// ---------- CsatSurvey ----------

it('can create a CSAT survey', function () {
    $ticket = Ticket::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'subject'    => 'Survey ticket',
        'status'     => TicketStatus::Resolved->value,
        'priority'   => TicketPriority::Normal->value,
    ]);
    $survey = CsatSurvey::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'ticket_id'  => $ticket->id,
        'token'      => \Illuminate\Support\Str::uuid()->toString(),
        'sent_at'    => now(),
    ]);

    expect($survey->exists)->toBeTrue();
    expect($survey->id)->toHaveLength(26);
});

// ---------- CrmContactCustomField ----------

it('can create a CRM contact custom field', function () {
    $field = CrmContactCustomField::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'name'       => 'LinkedIn URL',
        'type'       => 'text',
        'is_required'=> false,
        'sort_order' => 1,
    ]);

    expect($field->exists)->toBeTrue();
    expect($field->is_required)->toBeFalse();
});

// ---------- SharedInbox ----------

it('can create a shared inbox', function () {
    $inbox = SharedInbox::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'name'         => 'Support',
        'email_address' => 'support@acme.com',
        'is_active'    => true,
    ]);

    expect($inbox->exists)->toBeTrue();
    expect($inbox->is_active)->toBeTrue();
    expect($inbox->id)->toHaveLength(26);
});

// ---------- InboxEmail ----------

it('can create an inbox email', function () {
    $inbox = SharedInbox::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'name'       => 'Sales',
        'email_address' => 'sales@acme.com',
        'is_active'  => true,
    ]);
    $email = InboxEmail::withoutGlobalScopes()->create([
        'company_id'      => $this->company->id,
        'shared_inbox_id' => $inbox->id,
        'message_id'      => '<msg-' . uniqid() . '@example.com>',
        'from_email'      => 'customer@example.com',
        'subject'         => 'Inquiry about pricing',
        'body_html'       => '<p>Hello</p>',
        'received_at'     => now(),
    ]);

    expect($email->exists)->toBeTrue();
    expect($email->id)->toHaveLength(26);
});
