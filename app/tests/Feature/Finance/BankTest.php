<?php

declare(strict_types=1);

use App\Contracts\Finance\LedgerServiceInterface;
use App\Models\Company;
use App\Models\Finance\BankAccount;
use App\Models\Finance\BankTransaction;
use App\Models\User;
use App\Services\Finance\BankService;
use App\Services\Finance\LedgerService;
use App\Support\Services\BuiltInRoles;
use Database\Seeders\ModuleCatalogSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\DB;

function bankCompany(): array
{
    test()->seed(PermissionSeeder::class);
    test()->seed(ModuleCatalogSeeder::class);

    $company = setCompany(Company::factory()->create(['currency' => 'EUR']));
    BuiltInRoles::ensure($company);

    $owner = User::factory()->for($company)->create();
    $owner->assignRole('owner');
    test()->actingAs($owner);

    LedgerService::ensureDefaultChartOfAccounts($company->id);

    $account = BankAccount::factory()->create([
        'company_id' => $company->id,
        'gl_account_id' => LedgerService::accountIdByCode('1100'),
    ]);

    return [$company, $owner, $account];
}

test('csv import parses rows, skips the header, reports bad rows, and dedupes re-imports', function () {
    [, , $account] = bankCompany();
    $service = app(BankService::class);

    $csv = "date,description,amount\n2026-07-01,Coffee beans,-12.50\n2026-07-02,Customer payment,250.00\nnot-a-date,Broken row,10\n";

    $first = $service->importCsv($account, $csv);
    expect($first['imported'])->toBe(2)
        ->and($first['errors'])->toHaveCount(1);

    $second = $service->importCsv($account, $csv);
    expect($second['imported'])->toBe(0)
        ->and($second['skipped'])->toBe(2);

    expect($account->fresh()->current_balance_cents)->toBe(-1250 + 25000);
});

test('iban is stored encrypted at rest', function () {
    [, , $account] = bankCompany();

    $raw = DB::table('fin_bank_accounts')->where('id', $account->id)->value('iban');

    expect($raw)->not->toBeNull()
        ->and($raw)->not->toContain('NL91')
        ->and($account->fresh()->iban)->toBe('NL91ABNA0417164300');
});

test('suggestMatches finds exact-amount journal lines within five days only', function () {
    [, , $account] = bankCompany();
    $ledger = app(LedgerServiceInterface::class);
    $service = app(BankService::class);

    $service->importRows($account, [
        ['date' => now()->toDateString(), 'description' => 'Incoming 100', 'amount' => '100.00'],
    ]);
    $transaction = BankTransaction::query()->firstOrFail();

    // In-window exact match (bank side = debit on the GL bank account)
    $ledger->post('NEAR', 'Match me', now()->subDays(2), [
        ['account_id' => LedgerService::accountIdByCode('1100'), 'debit_cents' => 10000],
        ['account_id' => LedgerService::accountIdByCode('4000'), 'credit_cents' => 10000],
    ]);
    // Out-of-window exact match
    $ledger->post('FAR', 'Too old', now()->subDays(9), [
        ['account_id' => LedgerService::accountIdByCode('1100'), 'debit_cents' => 10000],
        ['account_id' => LedgerService::accountIdByCode('4000'), 'credit_cents' => 10000],
    ]);
    // Wrong amount
    $ledger->post('OFF', 'Wrong amount', now(), [
        ['account_id' => LedgerService::accountIdByCode('1100'), 'debit_cents' => 9999],
        ['account_id' => LedgerService::accountIdByCode('4000'), 'credit_cents' => 9999],
    ]);

    $matches = $service->suggestMatches($transaction);

    expect($matches)->toHaveCount(1)
        ->and($matches->first()->entry?->reference)->toBe('NEAR');
});

test('reconcile links, unreconcile unlinks, and the balance comparison reports the gap', function () {
    [, , $account] = bankCompany();
    $ledger = app(LedgerServiceInterface::class);
    $service = app(BankService::class);

    $service->importRows($account, [
        ['date' => now()->toDateString(), 'description' => 'Payment in', 'amount' => '50.00'],
    ]);
    $transaction = BankTransaction::query()->firstOrFail();

    $entry = $ledger->post('REC', 'Bank in', now(), [
        ['account_id' => LedgerService::accountIdByCode('1100'), 'debit_cents' => 5000],
        ['account_id' => LedgerService::accountIdByCode('4000'), 'credit_cents' => 5000],
    ]);
    $line = $entry->lines()->where('debit_cents', 5000)->firstOrFail();

    $service->reconcile($transaction, $line->id);
    expect($transaction->fresh()->reconciled_at)->not->toBeNull();

    $check = $service->balanceComparison($account->fresh());
    expect($check['bank_cents'])->toBe(5000)
        ->and($check['ledger_cents'])->toBe(5000)
        ->and($check['difference_cents'])->toBe(0);

    $service->unreconcile($transaction->fresh());
    expect($transaction->fresh()->journal_line_id)->toBeNull();
});
