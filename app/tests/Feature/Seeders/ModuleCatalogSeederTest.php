<?php

declare(strict_types=1);

use App\Models\ModuleCatalog;
use Database\Seeders\ModuleCatalogSeeder;

describe('Module Catalog Seeder', function () {
    it('seeds module catalog with records', function () {
        $this->seed(ModuleCatalogSeeder::class);
        expect(ModuleCatalog::count())->toBeGreaterThan(50);
    });

    it('seeds idempotently', function () {
        $this->seed(ModuleCatalogSeeder::class);
        $count = ModuleCatalog::count();
        $this->seed(ModuleCatalogSeeder::class);
        expect(ModuleCatalog::count())->toBe($count);
    });

    it('seeds core free modules with zero price', function () {
        $this->seed(ModuleCatalogSeeder::class);
        $core = ModuleCatalog::where('domain', 'core')->get();
        expect($core->count())->toBeGreaterThan(0);
        $core->each(fn ($m) => expect((float) $m->per_user_monthly_price)->toBe(0.0));
    });

    it('all module keys follow domain.slug pattern', function () {
        $this->seed(ModuleCatalogSeeder::class);
        ModuleCatalog::all()->each(function ($m) {
            // Pattern: domain.slug — both parts are lowercase alphanumeric with hyphens
            // The slug part may start with a digit (e.g. it.2fa)
            expect($m->module_key)->toMatch('/^[a-z][a-z0-9-]*\.[a-z0-9][a-z0-9-]*$/');
        });
    });
});
