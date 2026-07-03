<?php

declare(strict_types=1);

use App\Models\Admin;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\QueryException;

test('migrations create companies, users, and admins with ULID primary keys', function () {
    $company = Company::factory()->create();
    $user = User::factory()->for($company)->create();
    $admin = Admin::factory()->create();

    foreach ([$company, $user, $admin] as $model) {
        expect(strlen($model->id))->toBe(26);
    }
});

test('user email is unique per company, not globally', function () {
    $a = Company::factory()->create();
    $b = Company::factory()->create();

    User::factory()->for($a)->create(['email' => 'same@flowflex.test']);

    // Same email in another company is fine.
    User::factory()->for($b)->create(['email' => 'same@flowflex.test']);
    expect(User::withoutGlobalScopes()->where('email', 'same@flowflex.test')->count())->toBe(2);

    // Duplicate inside the same company violates the (company_id, email) unique index.
    expect(fn () => User::factory()->for($a)->create(['email' => 'same@flowflex.test']))
        ->toThrow(QueryException::class);
});

test('deleting a company cascades its users at the database level', function () {
    $company = Company::factory()->create();
    User::factory()->for($company)->count(2)->create();

    $company->forceDelete();

    expect(User::withoutGlobalScopes()->where('company_id', $company->id)->count())->toBe(0);
});

test('config defaults pin pgsql + redis drivers (test env overrides aside)', function () {
    expect(file_get_contents(config_path('database.php')))
        ->toContain("env('DB_CONNECTION', 'pgsql')");
    expect(file_get_contents(config_path('session.php')))
        ->toContain("env('SESSION_DRIVER', 'redis')");
    expect(file_get_contents(config_path('queue.php')))
        ->toContain("env('QUEUE_CONNECTION', 'redis')");
    expect(file_get_contents(config_path('cache.php')))
        ->toContain("env('CACHE_STORE', 'redis')");
});
