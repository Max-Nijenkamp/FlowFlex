<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // PostgreSQL treats NULL != NULL in regular unique indexes, so
        // unique(['company_id','flag']) allows duplicate (NULL, 'flag') rows.
        // A partial unique index on company_id IS NULL enforces global flag uniqueness.
        DB::statement(
            'CREATE UNIQUE INDEX IF NOT EXISTS company_feature_flags_global_flag_unique
             ON company_feature_flags (flag)
             WHERE company_id IS NULL'
        );
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS company_feature_flags_global_flag_unique');
    }
};
