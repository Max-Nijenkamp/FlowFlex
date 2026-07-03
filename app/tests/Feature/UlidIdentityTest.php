<?php

declare(strict_types=1);

use App\Models\Company;

test('primary keys are 26-char ULIDs, lexicographically sortable by creation', function () {
    $first = Company::factory()->create();
    $second = Company::factory()->create();

    expect($first->id)->toHaveLength(26)
        ->and($second->id)->toHaveLength(26)
        ->and(strcmp($first->id, $second->id))->toBeLessThan(0);
});

test('users migration keys the tenant scope with foreignUlid company_id', function () {
    $migration = file_get_contents(
        database_path('migrations/0001_01_01_000001_create_users_table.php')
    );

    expect($migration)->toContain("foreignUlid('company_id')");
});
