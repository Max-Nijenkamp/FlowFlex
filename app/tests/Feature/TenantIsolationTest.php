<?php

declare(strict_types=1);

use App\Exceptions\CompanyMismatchException;
use App\Exceptions\MissingCompanyContextException;
use App\Models\Company;
use App\Support\Services\CompanyContext;
use Tests\Support\TestItem;

beforeEach(function () {
    TestItem::migrate();
});

test('company A context returns zero company B rows', function () {
    $a = Company::factory()->create();
    $b = Company::factory()->create();

    setCompany($a);
    TestItem::create(['name' => 'a-item']);

    setCompany($b);
    TestItem::create(['name' => 'b-item']);

    setCompany($a);
    expect(TestItem::count())->toBe(1)
        ->and(TestItem::first()->name)->toBe('a-item');

    setCompany($b);
    expect(TestItem::count())->toBe(1)
        ->and(TestItem::first()->name)->toBe('b-item');
});

test('creating hook auto-fills company_id from the context', function () {
    $company = setCompany(Company::factory()->create());

    $item = TestItem::create(['name' => 'auto']);

    expect($item->company_id)->toBe($company->id);
});

test('creating without any context fails closed', function () {
    expect(fn () => TestItem::create(['name' => 'orphan']))
        ->toThrow(MissingCompanyContextException::class);
});

test('forging another company id under an active context throws', function () {
    $a = Company::factory()->create();
    $b = Company::factory()->create();

    setCompany($a);

    expect(fn () => TestItem::create(['name' => 'forged', 'company_id' => $b->id]))
        ->toThrow(CompanyMismatchException::class);
});

test('current() without context throws MissingCompanyContextException', function () {
    expect(fn () => app(CompanyContext::class)->current())
        ->toThrow(MissingCompanyContextException::class);
});
