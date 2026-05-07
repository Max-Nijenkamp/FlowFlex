<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_dependencies', function (Blueprint $table) {
            $table->unique(['task_id', 'depends_on_task_id'], 'task_dependencies_task_id_depends_on_task_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('task_dependencies', function (Blueprint $table) {
            $table->dropUnique('task_dependencies_task_id_depends_on_task_id_unique');
        });
    }
};
