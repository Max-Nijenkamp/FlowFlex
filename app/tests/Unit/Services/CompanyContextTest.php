<?php

declare(strict_types=1);

use App\Exceptions\MissingCompanyContextException;
use App\Models\Company;
use App\Support\Services\CompanyContext;

describe('CompanyContext Service (Unit)', function () {
    it('can set and retrieve company', function () {
        $company = new Company(['id' => '01jr1234567890abcdefghij01']);
        $context = new CompanyContext();

        $context->set($company);

        expect($context->current())->toBe($company);
    });

    it('returns id from currentId', function () {
        $company     = new Company();
        $company->id = '01jr1234567890abcdefghij01';
        $context     = new CompanyContext();

        $context->set($company);

        expect($context->currentId())->toBe('01jr1234567890abcdefghij01');
    });

    it('throws MissingCompanyContextException when no company set', function () {
        $context = new CompanyContext();

        expect(fn () => $context->current())->toThrow(MissingCompanyContextException::class);
    });

    it('currentId returns null when no context', function () {
        $context = new CompanyContext();

        expect($context->currentId())->toBeNull();
    });

    it('hasCompany is false by default', function () {
        $context = new CompanyContext();

        expect($context->hasCompany())->toBeFalse();
    });

    it('hasCompany is true after set', function () {
        $company = new Company(['id' => '01jr1234567890abcdefghij01']);
        $context = new CompanyContext();

        $context->set($company);

        expect($context->hasCompany())->toBeTrue();
    });

    it('clear resets context', function () {
        $company = new Company(['id' => '01jr1234567890abcdefghij01']);
        $context = new CompanyContext();

        $context->set($company);
        $context->clear();

        expect($context->hasCompany())->toBeFalse();
        expect($context->currentId())->toBeNull();
    });
});
