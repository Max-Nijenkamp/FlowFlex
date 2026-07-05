<?php

declare(strict_types=1);

use App\Contracts\Finance\LedgerServiceInterface;
use App\Exceptions\Finance\ClosedPeriodException;
use App\Exceptions\Finance\UnbalancedEntryException;
use App\Models\Company;
use App\Models\Finance\Account;
use App\Models\Finance\FiscalPeriod;
use App\Models\Finance\JournalEntry;
use App\Models\User;
use App\Services\Finance\LedgerService;
use App\Support\Services\BuiltInRoles;
use Database\Seeders\ModuleCatalogSeeder;
use Database\Seeders\PermissionSeeder;

function ledgerCompany(): array
{
    test()->seed(PermissionSeeder::class);
    test()->seed(ModuleCatalogSeeder::class);

    $company = setCompany(Company::factory()->create(['currency' => 'EUR']));
    BuiltInRoles::ensure($company);

    $owner = User::factory()->for($company)->create();
    $owner->assignRole('owner');
    test()->actingAs($owner);

    LedgerService::ensureDefaultChartOfAccounts($company->id);

    return [$company, $owner];
}

test('the default chart seeds idempotently with the five account types', function () {
    [$company] = ledgerCompany();
    LedgerService::ensureDefaultChartOfAccounts($company->id);

    expect(Account::query()->count())->toBe(13)
        ->and(Account::query()->pluck('type')->unique()->sort()->values()->all())
        ->toBe(['asset', 'equity', 'expense', 'liability', 'revenue']);
});

test('a balanced entry posts; an unbalanced one is rejected', function () {
    ledgerCompany();
    $ledger = app(LedgerServiceInterface::class);

    $entry = $ledger->post('TEST-1', 'Cash sale', now(), [
        ['account_id' => LedgerService::accountIdByCode('1000'), 'debit_cents' => 12100],
        ['account_id' => LedgerService::accountIdByCode('4000'), 'credit_cents' => 10000],
        ['account_id' => LedgerService::accountIdByCode('2200'), 'credit_cents' => 2100],
    ]);

    expect($entry->lines()->count())->toBe(3);

    expect(fn () => $ledger->post('TEST-2', 'Broken', now(), [
        ['account_id' => LedgerService::accountIdByCode('1000'), 'debit_cents' => 100],
        ['account_id' => LedgerService::accountIdByCode('4000'), 'credit_cents' => 99],
    ]))->toThrow(UnbalancedEntryException::class);
});

test('a line with both sides or neither side is rejected', function () {
    ledgerCompany();
    $ledger = app(LedgerServiceInterface::class);

    expect(fn () => $ledger->post('BAD', 'Both sides', now(), [
        ['account_id' => LedgerService::accountIdByCode('1000'), 'debit_cents' => 100, 'credit_cents' => 100],
        ['account_id' => LedgerService::accountIdByCode('4000'), 'credit_cents' => 0],
    ]))->toThrow(InvalidArgumentException::class);
});

test('posting into a closed period is rejected; reopening allows it again', function () {
    [$company] = ledgerCompany();
    $ledger = app(LedgerServiceInterface::class);

    FiscalPeriod::query()->create([
        'company_id' => $company->id,
        'period' => now()->format('Y-m'),
        'status' => 'closed',
    ]);

    $lines = [
        ['account_id' => LedgerService::accountIdByCode('1000'), 'debit_cents' => 100],
        ['account_id' => LedgerService::accountIdByCode('4000'), 'credit_cents' => 100],
    ];

    expect(fn () => $ledger->post('LOCKED', 'Should fail', now(), $lines))
        ->toThrow(ClosedPeriodException::class);

    FiscalPeriod::query()->where('period', now()->format('Y-m'))->update(['status' => 'open']);

    expect($ledger->post('UNLOCKED', 'Now fine', now(), $lines))->toBeInstanceOf(JournalEntry::class);
});

test('a reversal mirrors every line; the original stays untouched', function () {
    ledgerCompany();
    $ledger = app(LedgerServiceInterface::class);

    $entry = $ledger->post('ORIG', 'Original', now(), [
        ['account_id' => LedgerService::accountIdByCode('1200'), 'debit_cents' => 5000],
        ['account_id' => LedgerService::accountIdByCode('4000'), 'credit_cents' => 5000],
    ]);

    $reversal = $ledger->reverse($entry->id);

    expect($reversal->reference)->toBe('ORIG-REV')
        ->and($reversal->lines()->where('credit_cents', 5000)->where('account_id', LedgerService::accountIdByCode('1200'))->exists())->toBeTrue()
        ->and($entry->fresh()->lines()->count())->toBe(2);
});

test('the trial balance always balances and aggregates per account', function () {
    ledgerCompany();
    $ledger = app(LedgerServiceInterface::class);

    $ledger->post('A', 'One', now(), [
        ['account_id' => LedgerService::accountIdByCode('1000'), 'debit_cents' => 3000],
        ['account_id' => LedgerService::accountIdByCode('4000'), 'credit_cents' => 3000],
    ]);
    $ledger->post('B', 'Two', now(), [
        ['account_id' => LedgerService::accountIdByCode('1000'), 'debit_cents' => 2000],
        ['account_id' => LedgerService::accountIdByCode('4000'), 'credit_cents' => 2000],
    ]);

    $rows = $ledger->trialBalance(now()->subDay(), now()->addDay());

    expect($rows->sum('debit_cents'))->toBe($rows->sum('credit_cents'))
        ->and($rows->firstWhere(fn (array $row): bool => $row['account']->code === '1000')['debit_cents'])->toBe(5000);
});

test('tenant isolation: company B sees no company A accounts or journals', function () {
    ledgerCompany();
    app(LedgerServiceInterface::class)->post('A-ONLY', 'Secret', now(), [
        ['account_id' => LedgerService::accountIdByCode('1000'), 'debit_cents' => 100],
        ['account_id' => LedgerService::accountIdByCode('4000'), 'credit_cents' => 100],
    ]);

    ledgerCompany(); // company B

    expect(JournalEntry::query()->count())->toBe(0)
        ->and(Account::query()->count())->toBe(13); // B's own seeded chart only
});
