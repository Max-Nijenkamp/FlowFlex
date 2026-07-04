<?php

declare(strict_types=1);

use App\Models\Admin;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\LocalDevSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

test('the full seed runs clean from an empty database and is idempotent', function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class); // second run must not violate unique keys

    expect(Permission::query()->count())->toBe(count(PermissionSeeder::PERMISSIONS));
});

test('owner role holds every web-guard permission after seeding', function () {
    $this->seed(DatabaseSeeder::class);

    // test@test.nl is THE owner; demo@flowflex.nl is admin (one owner per company)
    $owner = User::query()->withoutGlobalScopes()
        ->where('email', 'test@test.nl')->firstOrFail();

    setCompany($owner->company()->firstOrFail());

    foreach (PermissionSeeder::PERMISSIONS as $permission) {
        expect($owner->can($permission))->toBeTrue();
    }
});

test('demo logins exist with working passwords', function () {
    $this->seed(DatabaseSeeder::class);

    $user = User::query()->withoutGlobalScopes()
        ->where('email', 'test@test.nl')->firstOrFail();

    expect(Hash::check('test1234', $user->password))->toBeTrue()
        ->and(Admin::query()->where('email', 'admin@flowflex.nl')->exists())->toBeTrue();
});

test('LocalDevSeeder refuses to run in production', function () {
    app()->detectEnvironment(fn () => 'production');

    try {
        expect(fn () => (new LocalDevSeeder)->run())
            ->toThrow(RuntimeException::class, 'production');
    } finally {
        app()->detectEnvironment(fn () => 'testing');
    }
});
