<?php

declare(strict_types=1);

use App\Models\Company;

describe('Company Model', function () {
    it('isActive returns true for trial status', function () {
        $company = new Company(['status' => 'trial']);
        expect($company->isActive())->toBeTrue();
    });

    it('isActive returns true for active status', function () {
        $company = new Company(['status' => 'active']);
        expect($company->isActive())->toBeTrue();
    });

    it('isActive returns false for suspended status', function () {
        $company = new Company(['status' => 'suspended']);
        expect($company->isActive())->toBeFalse();
    });

    it('isSuspended returns true for suspended status', function () {
        $company = new Company(['status' => 'suspended']);
        expect($company->isSuspended())->toBeTrue();
    });

    it('isSuspended returns false for active status', function () {
        $company = new Company(['status' => 'active']);
        expect($company->isSuspended())->toBeFalse();
    });
});
