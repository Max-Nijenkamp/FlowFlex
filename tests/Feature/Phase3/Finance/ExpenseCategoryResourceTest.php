<?php

use App\Models\Finance\ExpenseCategory;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'finance', 'finance');
    givePermissions($this->tenant, [
        'finance.expense-categories.view',
        'finance.expense-categories.create',
        'finance.expense-categories.edit',
        'finance.expense-categories.delete',
    ]);

    $this->category = ExpenseCategory::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'name'        => 'Travel',
        'description' => 'Travel expenses',
        'gl_code'     => '6001',
    ]);
});

it('authenticated tenant with permission can list expense categories', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/finance/expense-categories')
        ->assertOk();
});

it('unauthenticated request redirects from expense categories list', function () {
    $this->get('/finance/expense-categories')->assertRedirect();
});

it('tenant without permission gets 403 on expense categories list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/finance/expense-categories')
        ->assertForbidden();
});

it('can create an expense category via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('finance');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(\App\Filament\Finance\Resources\ExpenseCategoryResource\Pages\CreateExpenseCategory::class)
        ->fillForm(['name' => 'Meals'])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(ExpenseCategory::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('name', 'Meals')
        ->exists()
    )->toBeTrue();
});

it('can update an expense category via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('finance');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(
            \App\Filament\Finance\Resources\ExpenseCategoryResource\Pages\EditExpenseCategory::class,
            ['record' => $this->category->getRouteKey()]
        )
        ->fillForm(['name' => 'Travel & Transport'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->category->fresh()->name)->toBe('Travel & Transport');
});

it('tenant from another company cannot see expense categories from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'finance.expense-categories.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(ExpenseCategory::all()->pluck('id'))->not->toContain($this->category->id);
});
