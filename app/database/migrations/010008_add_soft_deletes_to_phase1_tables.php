<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLES = [
        'billing_subscriptions',
        'billing_invoices',
        'sandboxes',
        'api_clients',
        'api_tokens',
        'webhook_endpoints',
        'notification_watches',
    ];

    public function up(): void
    {
        // import_jobs already has soft deletes from its original migration — skip it
        foreach (self::TABLES as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $blueprint) use ($table): void {
                    $blueprint->softDeletes();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $blueprint): void {
                    $blueprint->dropSoftDeletes();
                });
            }
        }
    }
};
