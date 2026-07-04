<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Hard stop before any test touches a non-sqlite connection. A cached
     * config (bootstrap/cache/config.php) makes Laravel ignore phpunit.xml's
     * <env> overrides — RefreshDatabase would then migrate:fresh the REAL
     * pgsql dev database (it wiped it on 2026-07-04; see
     * gap-tests-wiped-dev-database).
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') !== 'sqlite') {
            throw new RuntimeException(
                'Tests must run on sqlite — got "'.config('database.default').'". '
                .'A cached config is probably shadowing phpunit.xml; run: php artisan config:clear',
            );
        }
    }
}
