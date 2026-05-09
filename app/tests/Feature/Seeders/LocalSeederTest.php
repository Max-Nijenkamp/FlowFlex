<?php

declare(strict_types=1);

use App\Models\Admin;
use App\Models\Company;
use App\Models\User;
use Database\Seeders\LocalAdminSeeder;
use Database\Seeders\LocalCompanySeeder;
use Illuminate\Support\Facades\Hash;

describe('Local Seeders', function () {
    it('local admin seeder creates test admin with correct credentials', function () {
        $this->seed(LocalAdminSeeder::class);

        $admin = Admin::where('email', 'test@test.nl')->first();

        expect($admin)->not->toBeNull();
        expect($admin->role)->toBe('super_admin');
        expect(Hash::check('test1234', $admin->password))->toBeTrue();
    });

    it('local admin seeder is idempotent', function () {
        $this->seed(LocalAdminSeeder::class);
        $this->seed(LocalAdminSeeder::class);

        expect(Admin::where('email', 'test@test.nl')->count())->toBe(1);
    });

    it('local company seeder creates FlowFlex Demo company', function () {
        $this->seed(LocalCompanySeeder::class);

        $company = Company::where('slug', 'flowflex-demo')->first();

        expect($company)->not->toBeNull();
        expect($company->name)->toBe('FlowFlex Demo');
    });

    it('local company seeder creates test user with correct credentials', function () {
        $this->seed(LocalCompanySeeder::class);

        $user = User::withoutGlobalScopes()->where('email', 'test@test.nl')->first();

        expect($user)->not->toBeNull();
        expect($user->status)->toBe('active');
        expect(Hash::check('test1234', $user->password))->toBeTrue();
    });

    it('local company seeder assigns owner role to test user', function () {
        $this->seed(LocalCompanySeeder::class);

        $user = User::withoutGlobalScopes()->where('email', 'test@test.nl')->first();

        expect($user->hasRole('owner'))->toBeTrue();
    });

    it('local company seeder is idempotent', function () {
        $this->seed(LocalCompanySeeder::class);
        $this->seed(LocalCompanySeeder::class);

        expect(Company::where('slug', 'flowflex-demo')->count())->toBe(1);
        expect(User::withoutGlobalScopes()->where('email', 'test@test.nl')->count())->toBe(1);
    });
});
