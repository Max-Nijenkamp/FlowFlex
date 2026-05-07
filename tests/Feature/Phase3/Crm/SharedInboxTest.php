<?php

use App\Models\Crm\InboxEmail;
use App\Models\Crm\SharedInbox;
use Illuminate\Support\Str;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    $this->inbox = SharedInbox::withoutGlobalScopes()->create([
        'company_id'    => $this->company->id,
        'name'          => 'Support Inbox',
        'email_address' => 'support@example.com',
        'is_active'     => true,
    ]);
});

it('can create a shared inbox', function () {
    expect($this->inbox)->toBeInstanceOf(SharedInbox::class);
    expect($this->inbox->name)->toBe('Support Inbox');
});

it('shared inbox is scoped to company', function () {
    $this->actingAs($this->tenant, 'tenant');

    $found = SharedInbox::find($this->inbox->id);
    expect($found)->not->toBeNull();
    expect($found->company_id)->toBe($this->company->id);
});

it('shared inbox from another company is not visible', function () {
    $otherCompany = makeCompany();

    $otherInbox = SharedInbox::withoutGlobalScopes()->create([
        'company_id'    => $otherCompany->id,
        'name'          => 'Other Inbox',
        'email_address' => 'other@example.com',
        'is_active'     => true,
    ]);

    $this->actingAs($this->tenant, 'tenant');

    expect(SharedInbox::all()->pluck('id'))->not->toContain($otherInbox->id);
});

it('can create an inbox email in a shared inbox', function () {
    $email = InboxEmail::withoutGlobalScopes()->create([
        'company_id'      => $this->company->id,
        'shared_inbox_id' => $this->inbox->id,
        'message_id'      => '<' . Str::random(20) . '@mail.test>',
        'from_email'      => 'customer@test.com',
        'from_name'       => 'Test Customer',
        'subject'         => 'Need help with my order',
        'body_html'       => '<p>Help please</p>',
        'status'          => 'unread',
        'received_at'     => now(),
    ]);

    expect($email)->toBeInstanceOf(InboxEmail::class);
    expect($email->status)->toBe('unread');
});

it('inbox email belongs to shared inbox', function () {
    $email = InboxEmail::withoutGlobalScopes()->create([
        'company_id'      => $this->company->id,
        'shared_inbox_id' => $this->inbox->id,
        'message_id'      => '<' . Str::random(20) . '@mail.test>',
        'from_email'      => 'customer@test.com',
        'subject'         => 'Question',
        'body_html'       => '<p>Hi</p>',
        'status'          => 'unread',
        'received_at'     => now(),
    ]);

    expect($email->sharedInbox->id)->toBe($this->inbox->id);
});

it('shared inbox has emails relationship', function () {
    InboxEmail::withoutGlobalScopes()->create([
        'company_id'      => $this->company->id,
        'shared_inbox_id' => $this->inbox->id,
        'message_id'      => '<' . Str::random(20) . '@mail.test>',
        'from_email'      => 'sender@test.com',
        'subject'         => 'Test',
        'body_html'       => '<p>Test</p>',
        'status'          => 'unread',
        'received_at'     => now(),
    ]);

    expect($this->inbox->emails()->count())->toBe(1);
});

it('message_id must be unique', function () {
    $messageId = '<unique-id@mail.test>';

    InboxEmail::withoutGlobalScopes()->create([
        'company_id'      => $this->company->id,
        'shared_inbox_id' => $this->inbox->id,
        'message_id'      => $messageId,
        'from_email'      => 'a@test.com',
        'subject'         => 'First',
        'body_html'       => '<p>First</p>',
        'status'          => 'unread',
        'received_at'     => now(),
    ]);

    expect(fn () => InboxEmail::withoutGlobalScopes()->create([
        'company_id'      => $this->company->id,
        'shared_inbox_id' => $this->inbox->id,
        'message_id'      => $messageId,
        'from_email'      => 'b@test.com',
        'subject'         => 'Duplicate',
        'body_html'       => '<p>Duplicate</p>',
        'status'          => 'unread',
        'received_at'     => now(),
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});
