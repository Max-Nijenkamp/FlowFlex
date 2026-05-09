<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Contracts\Console\Kernel;

abstract class TestCase extends BaseTestCase
{
    public function createApplication(): \Illuminate\Foundation\Application
    {
        // PHPUnit <env force="true"> sets $_ENV and putenv but not $_SERVER.
        // Laravel's Dotenv repository reads $_SERVER first, so Docker process-level
        // env vars (DB_DATABASE=flowflex) override PHPUnit's overrides.
        // Sync $_ENV → $_SERVER before bootstrap so the correct values win.
        foreach ($_ENV as $key => $value) {
            $_SERVER[$key] = $value;
        }

        $app = require __DIR__ . '/../bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
