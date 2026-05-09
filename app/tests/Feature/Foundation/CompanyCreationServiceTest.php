<?php

declare(strict_types=1);

use App\Data\Foundation\CreateCompanyData;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\User;
use App\Models\UserInvitation;
use App\Services\Foundation\CompanyCreationService;
use Spatie\Permission\Models\Role;

describe('CompanyCreationService', function () {
    it('creates a company with the correct fields', function () {
        $data = new CreateCompanyData(
            name: 'Acme Corp',
            slug: 'acme-corp',
            email: 'billing@acme.com',
            timezone: 'UTC',
            locale: 'en',
            currency: 'EUR',
            owner_first_name: 'Jane',
            owner_last_name: 'Doe',
            owner_email: 'jane@acme.com',
            country: 'NL',
        );

        $company = app(CompanyCreationService::class)->create($data);

        expect(Company::withoutGlobalScopes()->find($company->id))
            ->not->toBeNull()
            ->name->toBe('Acme Corp')
            ->slug->toBe('acme-corp')
            ->email->toBe('billing@acme.com')
            ->country->toBe('NL')
            ->timezone->toBe('UTC')
            ->locale->toBe('en')
            ->currency->toBe('EUR')
            ->status->toBe('trial');
    });

    it('creates an owner user with invited status', function () {
        $data = new CreateCompanyData(
            name: 'Beta Ltd',
            slug: 'beta-ltd',
            email: 'info@beta.com',
            timezone: 'Europe/Amsterdam',
            locale: 'nl',
            currency: 'EUR',
            owner_first_name: 'Bob',
            owner_last_name: 'Smith',
            owner_email: 'bob@beta.com',
        );

        $company = app(CompanyCreationService::class)->create($data);

        $owner = User::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->where('email', 'bob@beta.com')
            ->first();

        expect($owner)
            ->not->toBeNull()
            ->first_name->toBe('Bob')
            ->last_name->toBe('Smith')
            ->status->toBe('invited')
            ->password->toBeNull();
    });

    it('assigns owner role with all permissions to the owner user', function () {
        $data = new CreateCompanyData(
            name: 'Gamma Inc',
            slug: 'gamma-inc',
            email: 'info@gamma.com',
            timezone: 'UTC',
            locale: 'en',
            currency: 'USD',
            owner_first_name: 'Alice',
            owner_last_name: 'Jones',
            owner_email: 'alice@gamma.com',
        );

        $company = app(CompanyCreationService::class)->create($data);

        $owner = User::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->first();

        setPermissionsTeamId($company->id);

        expect($owner->hasRole('owner'))->toBeTrue();
    });

    it('activates foundation modules for the new company', function () {
        $data = new CreateCompanyData(
            name: 'Delta Co',
            slug: 'delta-co',
            email: 'info@delta.com',
            timezone: 'UTC',
            locale: 'en',
            currency: 'EUR',
            owner_first_name: 'Mark',
            owner_last_name: 'Brown',
            owner_email: 'mark@delta.com',
        );

        $company = app(CompanyCreationService::class)->create($data);

        $activeKeys = CompanyModuleSubscription::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->where('status', 'active')
            ->pluck('module_key')
            ->toArray();

        expect($activeKeys)
            ->toContain('core.auth')
            ->toContain('core.notifications')
            ->toContain('core.audit-log')
            ->toContain('core.file-storage')
            ->toContain('core.rbac');
    });

    it('persists invite token to the database', function () {
        $data = new CreateCompanyData(
            name: 'Echo LLC',
            slug: 'echo-llc',
            email: 'info@echo.com',
            timezone: 'UTC',
            locale: 'en',
            currency: 'EUR',
            owner_first_name: 'Sara',
            owner_last_name: 'Lee',
            owner_email: 'sara@echo.com',
        );

        $company = app(CompanyCreationService::class)->create($data);

        $owner = User::withoutGlobalScopes()->where('email', 'sara@echo.com')->first();

        $invitation = UserInvitation::where('user_id', $owner->id)
            ->where('company_id', $company->id)
            ->first();

        expect($invitation)->not->toBeNull();
        expect($invitation->token)->toHaveLength(64);
        expect($invitation->isPending())->toBeTrue();
        expect($invitation->expires_at->isFuture())->toBeTrue();
    });

    it('wraps creation in a transaction — rolls back on failure', function () {
        $companyCountBefore = Company::withoutGlobalScopes()->count();

        // Force a failure by using a duplicate slug
        $first = new CreateCompanyData(
            name: 'First Corp',
            slug: 'dupe-slug',
            email: 'first@corp.com',
            timezone: 'UTC',
            locale: 'en',
            currency: 'EUR',
            owner_first_name: 'A',
            owner_last_name: 'B',
            owner_email: 'a@first.com',
        );
        app(CompanyCreationService::class)->create($first);

        $second = new CreateCompanyData(
            name: 'Second Corp',
            slug: 'dupe-slug', // duplicate — will throw
            email: 'second@corp.com',
            timezone: 'UTC',
            locale: 'en',
            currency: 'EUR',
            owner_first_name: 'C',
            owner_last_name: 'D',
            owner_email: 'c@second.com',
        );

        expect(fn () => app(CompanyCreationService::class)->create($second))
            ->toThrow(\Illuminate\Database\QueryException::class);

        // Only the first company was created
        expect(Company::withoutGlobalScopes()->count())->toBe($companyCountBefore + 1);
    });
});
