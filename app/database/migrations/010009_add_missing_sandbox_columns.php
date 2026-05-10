<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sandboxes', function (Blueprint $table): void {
            if (! Schema::hasColumn('sandboxes', 'redis_prefix')) {
                $table->string('redis_prefix')->nullable()->after('database_name');
            }
            if (! Schema::hasColumn('sandboxes', 's3_prefix')) {
                $table->string('s3_prefix')->nullable()->after('redis_prefix');
            }
            if (! Schema::hasColumn('sandboxes', 'subdomain')) {
                $table->string('subdomain')->nullable()->after('s3_prefix');
            }
            if (! Schema::hasColumn('sandboxes', 'reset_scheduled_at')) {
                $table->timestamp('reset_scheduled_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sandboxes', function (Blueprint $table): void {
            $table->dropColumn(array_filter(
                ['redis_prefix', 's3_prefix', 'subdomain', 'reset_scheduled_at'],
                fn (string $col) => Schema::hasColumn('sandboxes', $col),
            ));
        });
    }
};
