<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // PostgreSQL does not support ALTER COLUMN ... USING for enums in the same way as MySQL.
        // We rename the constraint and add 'archived' to the allowed values.
        DB::statement("ALTER TABLE projects DROP CONSTRAINT IF EXISTS projects_status_check");
        DB::statement("ALTER TABLE projects ADD CONSTRAINT projects_status_check CHECK (status IN ('planning','active','on_hold','completed','cancelled','archived'))");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE projects DROP CONSTRAINT IF EXISTS projects_status_check");
        DB::statement("ALTER TABLE projects ADD CONSTRAINT projects_status_check CHECK (status IN ('planning','active','on_hold','completed','cancelled'))");
    }
};
