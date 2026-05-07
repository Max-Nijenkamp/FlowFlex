<?php

use App\Enums\Crm\DealStatus;
use App\Models\Crm\Deal;
use App\Models\Crm\DealNote;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    $this->deal = Deal::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'title'      => 'Test Deal',
        'status'     => DealStatus::Open->value,
        'currency'   => 'EUR',
    ]);
});

it('can create a deal note', function () {
    $note = DealNote::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'deal_id'    => $this->deal->id,
        'body'       => 'Client mentioned budget concerns.',
    ]);

    expect($note)->toBeInstanceOf(DealNote::class);
    expect($note->body)->toBe('Client mentioned budget concerns.');
});

it('deal note belongs to deal', function () {
    $note = DealNote::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'deal_id'    => $this->deal->id,
        'body'       => 'Follow up next week.',
    ]);

    expect($note->deal->id)->toBe($this->deal->id);
});

it('deal has notes relationship', function () {
    DealNote::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'deal_id'    => $this->deal->id,
        'body'       => 'Sent proposal.',
    ]);

    expect($this->deal->notes()->count())->toBe(1);
});

it('deal note is scoped to company', function () {
    $note = DealNote::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'deal_id'    => $this->deal->id,
        'body'       => 'Scoped note.',
    ]);

    $this->actingAs($this->tenant, 'tenant');

    $found = DealNote::find($note->id);
    expect($found)->not->toBeNull();
    expect($found->company_id)->toBe($this->company->id);
});

it('deal note from another company is not visible', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    $otherDeal    = Deal::withoutGlobalScopes()->create([
        'company_id' => $otherCompany->id,
        'title'      => 'Other Deal',
        'status'     => DealStatus::Open->value,
        'currency'   => 'EUR',
    ]);

    $otherNote = DealNote::withoutGlobalScopes()->create([
        'company_id' => $otherCompany->id,
        'deal_id'    => $otherDeal->id,
        'body'       => 'Other company note.',
    ]);

    $this->actingAs($this->tenant, 'tenant');

    expect(DealNote::all()->pluck('id'))->not->toContain($otherNote->id);
});

it('deal note can optionally reference a tenant author', function () {
    $note = DealNote::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'deal_id'    => $this->deal->id,
        'tenant_id'  => $this->tenant->id,
        'body'       => 'Authored note.',
    ]);

    expect($note->tenant->id)->toBe($this->tenant->id);
});
