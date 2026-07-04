<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;

arch('all models use ULID primary keys')
    ->expect('App\Models')
    ->toUseTrait(HasUlids::class);

arch('all models soft delete')
    ->expect('App\Models')
    ->toUseTrait(SoftDeletes::class);

test('every tenant table migration carries a company_id foreignUlid', function () {
    // Tenant tables = every create_* migration except the platform-level ones.
    $platformTables = [
        'companies', 'admins', 'cache', 'jobs', 'password_reset_tokens', 'sessions',
        'personal_access_tokens', 'permission', 'notifications', // sanctum/spatie/laravel infrastructure
        'module_catalog', // platform-level: one catalog for every tenant (module-system.md)
    ];

    foreach (glob(database_path('migrations/*_create_*_table.php')) as $file) {
        preg_match('/create_(\w+)_table/', basename($file), $m);
        $table = $m[1];

        if (in_array($table, $platformTables, true)) {
            continue;
        }

        expect(file_get_contents($file))->toContain("foreignUlid('company_id')");
    }

    expect(true)->toBeTrue();
});
