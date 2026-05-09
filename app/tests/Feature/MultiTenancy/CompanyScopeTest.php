<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\User;
use App\Support\Services\CompanyContext;

describe('Company Scope', function () {
    it('scopes user queries to current company', function () {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        User::factory()->forCompany($companyA)->count(3)->create();
        User::factory()->forCompany($companyB)->count(2)->create();

        app(CompanyContext::class)->set($companyA);

        expect(User::count())->toBe(3);
    });

    it('returns all users when no company context set', function () {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        User::factory()->forCompany($companyA)->count(2)->create();
        User::factory()->forCompany($companyB)->count(2)->create();

        app(CompanyContext::class)->clear();

        expect(User::count())->toBe(4);
    });

    it('does not leak records across companies', function () {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $userA = User::factory()->forCompany($companyA)->create();
        User::factory()->forCompany($companyB)->count(5)->create();

        app(CompanyContext::class)->set($companyA);

        $users = User::all();

        expect($users)->toHaveCount(1);
        expect($users->first()->id)->toBe($userA->id);
    });

    it('switches scope correctly when company context changes', function () {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        User::factory()->forCompany($companyA)->count(2)->create();
        User::factory()->forCompany($companyB)->count(3)->create();

        app(CompanyContext::class)->set($companyA);
        expect(User::count())->toBe(2);

        app(CompanyContext::class)->set($companyB);
        expect(User::count())->toBe(3);
    });

    it('auto-fills company_id on create when context is set', function () {
        $company = Company::factory()->create();

        app(CompanyContext::class)->set($company);

        $user = User::factory()->make(['company_id' => null]);
        $user->company_id = null;
        $user->save();

        expect($user->company_id)->toBe($company->id);
    });
});
