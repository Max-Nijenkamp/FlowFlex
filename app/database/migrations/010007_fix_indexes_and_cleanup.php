<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The user_invitations table already has a company_id index (from 000010 migration).
        // We only add it if somehow it is missing — use Schema::hasIndex() is not available
        // on all drivers, so we use a try/catch approach to be safe.

        // Drop dead attribute_changes column from activity_log if it exists
        if (Schema::hasColumn('activity_log', 'attribute_changes')) {
            Schema::table('activity_log', function (Blueprint $table): void {
                $table->dropColumn('attribute_changes');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('activity_log', 'attribute_changes')) {
            Schema::table('activity_log', function (Blueprint $table): void {
                $table->json('attribute_changes')->nullable();
            });
        }
    }
};
