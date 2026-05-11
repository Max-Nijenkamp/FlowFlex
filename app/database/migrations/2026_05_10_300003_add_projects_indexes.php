<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Composite index: project+status queries (e.g. "all open tasks for project X")
            $table->index(['project_id', 'status'], 'tasks_project_status');
            // Composite index: project+sort_order for Kanban ordering
            $table->index(['project_id', 'sort_order'], 'tasks_project_sort_order');
            // Note: tasks_assignee_id_index already exists from the initial migration
        });

        Schema::table('time_entries', function (Blueprint $table) {
            // Composite index: user+started_at for date-range queries per user
            $table->index(['user_id', 'date'], 'time_entries_user_date');
            // Index on project_id for project-scoped time entry queries
            $table->index(['project_id'], 'time_entries_project_id');
        });

        Schema::table('project_members', function (Blueprint $table) {
            // Index on user_id for "which projects is this user a member of" queries
            $table->index(['user_id'], 'project_members_user_id');
        });

        // kanban_columns already has a compound unique index on (board_id, sort_order)
        // which covers board+sort queries; no additional index needed
    }

    public function down(): void
    {
        Schema::table('tasks', fn (Blueprint $t) => $t->dropIndex('tasks_project_status'));
        Schema::table('tasks', fn (Blueprint $t) => $t->dropIndex('tasks_project_sort_order'));
        Schema::table('time_entries', fn (Blueprint $t) => $t->dropIndex('time_entries_user_date'));
        Schema::table('time_entries', fn (Blueprint $t) => $t->dropIndex('time_entries_project_id'));
        Schema::table('project_members', fn (Blueprint $t) => $t->dropIndex('project_members_user_id'));
    }
};
