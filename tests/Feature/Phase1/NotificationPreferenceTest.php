<?php

use App\Models\Company;
use App\Models\NotificationPreference;
use App\Models\Tenant;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);
});

it('can create a notification preference record', function () {
    $pref = NotificationPreference::withoutGlobalScopes()->create([
        'company_id'        => $this->company->id,
        'tenant_id'         => $this->tenant->id,
        'notification_type' => 'leave_requested',
        'channels'          => ['mail', 'database'],
        'is_enabled'        => true,
    ]);

    expect($pref->exists)->toBeTrue();
    expect($pref->notification_type)->toBe('leave_requested');
    expect($pref->channels)->toBe(['mail', 'database']);
    expect($pref->is_enabled)->toBeTrue();
});

it('can read notification preference belonging to tenant', function () {
    NotificationPreference::withoutGlobalScopes()->create([
        'company_id'        => $this->company->id,
        'tenant_id'         => $this->tenant->id,
        'notification_type' => 'payslip_generated',
        'channels'          => ['mail'],
        'is_enabled'        => true,
    ]);

    $this->actingAs($this->tenant, 'tenant');

    $prefs = NotificationPreference::where('tenant_id', $this->tenant->id)->get();

    expect($prefs)->toHaveCount(1);
    expect($prefs->first()->notification_type)->toBe('payslip_generated');
});

it('can update a notification preference', function () {
    $pref = NotificationPreference::withoutGlobalScopes()->create([
        'company_id'        => $this->company->id,
        'tenant_id'         => $this->tenant->id,
        'notification_type' => 'leave_approved',
        'channels'          => ['mail'],
        'is_enabled'        => true,
    ]);

    $pref->update([
        'channels'   => ['mail', 'slack'],
        'is_enabled' => false,
    ]);

    $pref->refresh();
    expect($pref->channels)->toBe(['mail', 'slack']);
    expect($pref->is_enabled)->toBeFalse();
});

it('can soft-delete a notification preference', function () {
    $pref = NotificationPreference::withoutGlobalScopes()->create([
        'company_id'        => $this->company->id,
        'tenant_id'         => $this->tenant->id,
        'notification_type' => 'task_assigned',
        'channels'          => ['database'],
        'is_enabled'        => true,
    ]);

    $pref->delete();

    // After soft delete, deleted_at is set
    expect($pref->deleted_at)->not->toBeNull();

    // withTrashed includes soft-deleted records, withoutGlobalScopes bypasses CompanyScope
    expect(NotificationPreference::withTrashed()->withoutGlobalScopes()->find($pref->id))->not->toBeNull();

    // Using only SoftDeletes (not withoutGlobalScopes) — the record has a deleted_at set
    $freshPref = NotificationPreference::withTrashed()->find($pref->id);
    expect($freshPref)->not->toBeNull();
    expect($freshPref->trashed())->toBeTrue();
});

it('company isolation: preference from company A not visible to tenant in company B', function () {
    $companyB = makeCompany();
    $tenantB  = makeTenant($companyB);

    // Create preference for company A
    NotificationPreference::withoutGlobalScopes()->create([
        'company_id'        => $this->company->id,
        'tenant_id'         => $this->tenant->id,
        'notification_type' => 'leave_requested',
        'channels'          => ['mail'],
        'is_enabled'        => true,
    ]);

    // Tenant B should not see company A's preferences via the global scope
    $this->actingAs($tenantB, 'tenant');

    $visible = NotificationPreference::all();

    expect($visible)->toHaveCount(0);
});

it('channels resolves as array correctly', function () {
    $pref = NotificationPreference::withoutGlobalScopes()->create([
        'company_id'        => $this->company->id,
        'tenant_id'         => $this->tenant->id,
        'notification_type' => 'onboarding_started',
        'channels'          => ['mail', 'database', 'sms'],
        'is_enabled'        => true,
    ]);

    $pref->refresh();

    expect($pref->channels)->toBeArray();
    expect($pref->channels)->toContain('sms');
});

it('tenant relationship loads correctly', function () {
    $pref = NotificationPreference::withoutGlobalScopes()->create([
        'company_id'        => $this->company->id,
        'tenant_id'         => $this->tenant->id,
        'notification_type' => 'pay_run_processed',
        'channels'          => ['database'],
        'is_enabled'        => true,
    ]);

    $pref->load('tenant');

    expect($pref->tenant->id)->toBe($this->tenant->id);
});
