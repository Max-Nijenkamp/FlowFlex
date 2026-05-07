<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite can't drop PK columns — recreate table without the id column.
            DB::statement('CREATE TABLE "task_label_assignments_new" (
                "task_id" varchar NOT NULL,
                "label_id" varchar NOT NULL,
                "created_at" datetime,
                "updated_at" datetime,
                PRIMARY KEY ("task_id", "label_id")
            )');

            DB::statement('INSERT INTO "task_label_assignments_new" (task_id, label_id, created_at, updated_at)
                SELECT task_id, label_id, created_at, updated_at FROM "task_label_assignments"');

            Schema::drop('task_label_assignments');

            DB::statement('ALTER TABLE "task_label_assignments_new" RENAME TO "task_label_assignments"');
        } else {
            // PostgreSQL / MySQL — can alter columns normally.
            Schema::table('task_label_assignments', function (Blueprint $table) {
                $table->dropPrimary();
            });

            Schema::table('task_label_assignments', function (Blueprint $table) {
                $table->dropColumn('id');
            });

            Schema::table('task_label_assignments', function (Blueprint $table) {
                $table->primary(['task_id', 'label_id'], 'task_label_assignments_pkey');
            });
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('CREATE TABLE "task_label_assignments_old" (
                "id" integer PRIMARY KEY AUTOINCREMENT NOT NULL,
                "task_id" varchar NOT NULL,
                "label_id" varchar NOT NULL,
                "created_at" datetime,
                "updated_at" datetime
            )');

            DB::statement('INSERT INTO "task_label_assignments_old" (task_id, label_id, created_at, updated_at)
                SELECT task_id, label_id, created_at, updated_at FROM "task_label_assignments"');

            Schema::drop('task_label_assignments');

            DB::statement('ALTER TABLE "task_label_assignments_old" RENAME TO "task_label_assignments"');
        } else {
            Schema::table('task_label_assignments', function (Blueprint $table) {
                $table->dropPrimary('task_label_assignments_pkey');
            });

            Schema::table('task_label_assignments', function (Blueprint $table) {
                $table->bigIncrements('id')->first();
            });
        }
    }
};
