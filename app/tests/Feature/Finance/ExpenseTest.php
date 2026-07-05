<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\Finance\Expense;
use App\Models\Finance\ExpenseCategory;
use App\Models\Finance\ExpenseReport;
use App\Models\Finance\JournalEntry;
use App\Models\User;
use App\Services\Finance\ExpenseService;
use App\Services\Finance\LedgerService;
use App\Support\Services\BuiltInRoles;
use Database\Seeders\ModuleCatalogSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Validation\ValidationException;
use Spatie\ModelStates\Exceptions\TransitionNotFound;

function expenseCompany(): array
{
    test()->seed(PermissionSeeder::class);
    test()->seed(ModuleCatalogSeeder::class);

    $company = setCompany(Company::factory()->create(['currency' => 'EUR']));
    BuiltInRoles::ensure($company);

    $owner = User::factory()->for($company)->create();
    $owner->assignRole('owner');
    test()->actingAs($owner);

    LedgerService::ensureDefaultChartOfAccounts($company->id);

    $category = ExpenseCategory::query()->create([
        'company_id' => $company->id,
        'name' => 'Travel',
        'limit_per_transaction_cents' => 10000,
        'gl_account_id' => LedgerService::accountIdByCode('6100'),
    ]);

    return [$company, $owner, $category];
}

test('the state machine walks draft → submitted → approved → reimbursed and posts on reimbursement', function () {
    [$company, $owner, $category] = expenseCompany();
    $approver = User::factory()->for($company)->create();
    $approver->assignRole('admin');
    $service = app(ExpenseService::class);

    $expense = Expense::factory()->create([
        'company_id' => $company->id, 'user_id' => $owner->id,
        'category_id' => $category->id, 'amount_cents' => 4500,
    ]);

    $service->submit($expense);
    expect((string) $expense->fresh()->status)->toBe('submitted')
        ->and($expense->fresh()->is_over_limit)->toBeFalse();

    test()->actingAs($approver);
    setPermissionsTeamId($company->id);
    $service->approve($expense->fresh());
    expect((string) $expense->fresh()->status)->toBe('approved');

    $service->reimburse($expense->fresh());
    expect((string) $expense->fresh()->status)->toBe('reimbursed');

    $entry = JournalEntry::query()->where('source_type', 'expense')->where('source_id', $expense->id)->firstOrFail();
    expect((int) $entry->lines()->sum('debit_cents'))->toBe(4500);
});

test('self-approval is rejected; rejection requires a reason; a claim over the category limit is flagged', function () {
    [$company, $owner, $category] = expenseCompany();
    $service = app(ExpenseService::class);

    $expense = Expense::factory()->create([
        'company_id' => $company->id, 'user_id' => $owner->id,
        'category_id' => $category->id, 'amount_cents' => 15000, // over the €100 limit
    ]);

    $service->submit($expense);
    expect($expense->fresh()->is_over_limit)->toBeTrue();

    expect(fn () => $service->approve($expense->fresh()))->toThrow(ValidationException::class);

    expect(fn () => $service->reject($expense->fresh(), ''))->toThrow(ValidationException::class);

    $service->reject($expense->fresh(), 'Receipt missing');
    expect((string) $expense->fresh()->status)->toBe('rejected')
        ->and($expense->fresh()->rejection_reason)->toBe('Receipt missing');
});

test('illegal transitions are blocked by the state machine', function () {
    [$company, $owner, $category] = expenseCompany();
    $service = app(ExpenseService::class);

    $draft = Expense::factory()->create([
        'company_id' => $company->id, 'user_id' => $owner->id, 'category_id' => $category->id,
    ]);

    expect(fn () => $service->reimburse($draft))->toThrow(TransitionNotFound::class);
});

test('submitting a report cascades submit to its draft claims', function () {
    [$company, $owner, $category] = expenseCompany();
    $service = app(ExpenseService::class);

    $report = ExpenseReport::query()->create([
        'company_id' => $company->id, 'user_id' => $owner->id,
        'title' => 'June claims', 'period_start' => now()->startOfMonth(), 'period_end' => now(),
    ]);

    Expense::factory()->count(2)->create([
        'company_id' => $company->id, 'user_id' => $owner->id,
        'category_id' => $category->id, 'report_id' => $report->id,
    ]);

    $service->submitReport($report);

    expect($report->fresh()->status)->toBe('submitted')
        ->and(Expense::query()->where('report_id', $report->id)->where('status', 'submitted')->count())->toBe(2);
});
