<?php

declare(strict_types=1);

use App\Exceptions\MissingCompanyContextException;
use App\Models\Company;
use App\Support\Services\CompanyContext;

describe('Company Context', function () {
    it('returns current company after set', function () {
        $company = Company::factory()->create();
        $context = app(CompanyContext::class);

        $context->set($company);

        expect($context->current())->toBeInstanceOf(Company::class);
        expect($context->current()->id)->toBe($company->id);
    });

    it('returns current company id', function () {
        $company = Company::factory()->create();
        $context = app(CompanyContext::class);

        $context->set($company);

        expect($context->currentId())->toBe($company->id);
    });

    it('throws when current() called without context', function () {
        $context = app(CompanyContext::class);
        $context->clear();

        expect(fn () => $context->current())->toThrow(MissingCompanyContextException::class);
    });

    it('returns null id when no context', function () {
        $context = app(CompanyContext::class);
        $context->clear();

        expect($context->currentId())->toBeNull();
    });

    it('hasCompany returns false when cleared', function () {
        $context = app(CompanyContext::class);
        $context->clear();

        expect($context->hasCompany())->toBeFalse();
    });

    it('hasCompany returns true when set', function () {
        $company = Company::factory()->create();
        $context = app(CompanyContext::class);

        $context->set($company);

        expect($context->hasCompany())->toBeTrue();
    });

    it('clear removes company context', function () {
        $company = Company::factory()->create();
        $context = app(CompanyContext::class);

        $context->set($company);
        $context->clear();

        expect($context->hasCompany())->toBeFalse();
    });

    it('is a singleton', function () {
        $a = app(CompanyContext::class);
        $b = app(CompanyContext::class);

        expect($a)->toBe($b);
    });
});
