<?php

use App\Models\Crm\CrmActivity;
use App\Models\Crm\CrmContact;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    $this->contact = CrmContact::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'first_name' => 'Jane',
        'last_name'  => 'Doe',
        'type'       => 'lead',
    ]);
});

it('can create a crm activity with morph to contact', function () {
    $activity = CrmActivity::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'subject_type' => CrmContact::class,
        'subject_id'   => $this->contact->id,
        'type'         => 'note',
        'description'  => 'Left a voicemail',
        'occurred_at'  => now(),
    ]);

    expect($activity)->toBeInstanceOf(CrmActivity::class);
    expect($activity->subject_type)->toBe(CrmContact::class);
});

it('crm activity morph resolves to contact', function () {
    $activity = CrmActivity::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'subject_type' => CrmContact::class,
        'subject_id'   => $this->contact->id,
        'type'         => 'call',
        'description'  => 'Spoke for 15 minutes',
        'occurred_at'  => now(),
    ]);

    expect($activity->subject)->toBeInstanceOf(CrmContact::class);
    expect($activity->subject->id)->toBe($this->contact->id);
});

it('crm activity is scoped to company', function () {
    $activity = CrmActivity::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'subject_type' => CrmContact::class,
        'subject_id'   => $this->contact->id,
        'type'         => 'email',
        'description'  => 'Sent welcome email',
        'occurred_at'  => now(),
    ]);

    $this->actingAs($this->tenant, 'tenant');

    $found = CrmActivity::find($activity->id);
    expect($found)->not->toBeNull();
    expect($found->company_id)->toBe($this->company->id);
});

it('crm activity from another company is not visible', function () {
    $otherCompany = makeCompany();
    $otherContact = CrmContact::withoutGlobalScopes()->create([
        'company_id' => $otherCompany->id,
        'first_name' => 'Other',
        'last_name'  => 'Contact',
        'type'       => 'lead',
    ]);

    $otherActivity = CrmActivity::withoutGlobalScopes()->create([
        'company_id'   => $otherCompany->id,
        'subject_type' => CrmContact::class,
        'subject_id'   => $otherContact->id,
        'type'         => 'note',
        'description'  => 'Other company note',
        'occurred_at'  => now(),
    ]);

    $this->actingAs($this->tenant, 'tenant');

    expect(CrmActivity::all()->pluck('id'))->not->toContain($otherActivity->id);
});

it('crm activity occurred_at casts to datetime', function () {
    $activity = CrmActivity::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'subject_type' => CrmContact::class,
        'subject_id'   => $this->contact->id,
        'type'         => 'meeting',
        'description'  => 'Quarterly review',
        'occurred_at'  => '2026-05-01 14:00:00',
    ]);

    expect($activity->occurred_at)->toBeInstanceOf(\DateTimeInterface::class);
});
