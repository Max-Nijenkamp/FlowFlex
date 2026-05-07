<?php

use App\Enums\Finance\ExpenseStatus;
use App\Models\Finance\Expense;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'finance', 'finance');
    givePermissions($this->tenant, [
        'finance.expenses.view',
        'finance.expenses.create',
        'finance.expenses.edit',
        'finance.expenses.delete',
        'finance.expenses.approve',
    ]);

    $this->expense = Expense::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'tenant_id'    => $this->tenant->id,
        'description'  => 'Travel to client',
        'amount'       => '150.00',
        'currency'     => 'EUR',
        'expense_date' => now(),
        'status'       => ExpenseStatus::Pending->value,
    ]);
});

it('authenticated tenant with permission can list expenses', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/finance/expenses')
        ->assertOk();
});

it('unauthenticated request redirects from expenses list', function () {
    $this->get('/finance/expenses')->assertRedirect();
});

it('tenant without permission gets 403 on expenses list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/finance/expenses')
        ->assertForbidden();
});

it('can create an expense via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('finance');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(\App\Filament\Finance\Resources\ExpenseResource\Pages\CreateExpense::class)
        ->fillForm([
            'tenant_id'    => $this->tenant->id,
            'description'  => 'Hotel booking',
            'amount'       => '250.00',
            'currency'     => 'EUR',
            'expense_date' => now()->toDateString(),
            'status'       => ExpenseStatus::Pending->value,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Expense::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('description', 'Hotel booking')
        ->exists()
    )->toBeTrue();
});

it('can update an expense via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('finance');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(
            \App\Filament\Finance\Resources\ExpenseResource\Pages\EditExpense::class,
            ['record' => $this->expense->getRouteKey()]
        )
        ->fillForm(['description' => 'Updated travel'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->expense->fresh()->description)->toBe('Updated travel');
});

it('expense status casts to ExpenseStatus enum', function () {
    expect($this->expense->status)->toBe(ExpenseStatus::Pending);
});

it('tenant from another company cannot see expenses from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'finance.expenses.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(Expense::all()->pluck('id'))->not->toContain($this->expense->id);
});
