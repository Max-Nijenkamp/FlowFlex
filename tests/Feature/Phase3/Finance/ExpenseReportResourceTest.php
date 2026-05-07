<?php

use App\Models\Finance\ExpenseReport;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'finance', 'finance');
    givePermissions($this->tenant, [
        'finance.expense-reports.view',
        'finance.expense-reports.create',
        'finance.expense-reports.edit',
        'finance.expense-reports.delete',
        'finance.expense-reports.approve',
    ]);

    $this->report = ExpenseReport::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'tenant_id'  => $this->tenant->id,
        'title'      => 'Q2 Expenses',
        'status'     => 'draft',
    ]);
});

it('authenticated tenant with permission can list expense reports', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/finance/expense-reports')
        ->assertOk();
});

it('unauthenticated request redirects from expense reports list', function () {
    $this->get('/finance/expense-reports')->assertRedirect();
});

it('tenant without permission gets 403 on expense reports list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/finance/expense-reports')
        ->assertForbidden();
});

it('can create an expense report via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('finance');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(\App\Filament\Finance\Resources\ExpenseReportResource\Pages\CreateExpenseReport::class)
        ->fillForm([
            'tenant_id' => $this->tenant->id,
            'title'     => 'May Conference Expenses',
            'status'    => 'draft',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(ExpenseReport::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('title', 'May Conference Expenses')
        ->exists()
    )->toBeTrue();
});

it('can update an expense report via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('finance');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(
            \App\Filament\Finance\Resources\ExpenseReportResource\Pages\EditExpenseReport::class,
            ['record' => $this->report->getRouteKey()]
        )
        ->fillForm(['title' => 'Q2 Expenses - Updated'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->report->fresh()->title)->toBe('Q2 Expenses - Updated');
});

it('tenant from another company cannot see expense reports from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'finance.expense-reports.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(ExpenseReport::all()->pluck('id'))->not->toContain($this->report->id);
});
