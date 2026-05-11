<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();

            $table->ulid('project_id')->nullable();
            $table->foreign('project_id')->references('id')->on('projects')->nullOnDelete();

            $table->ulid('parent_id')->nullable();

            $table->string('title');
            $table->text('description')->nullable();

            $table->ulid('assignee_id')->nullable();
            $table->foreign('assignee_id')->references('id')->on('users')->nullOnDelete();

            $table->ulid('created_by');
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();

            $table->enum('status', ['todo', 'in_progress', 'in_review', 'done', 'cancelled'])
                ->default('todo');

            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])
                ->default('medium');

            $table->date('due_date')->nullable();
            $table->date('start_date')->nullable();
            $table->decimal('estimate_hours', 5, 1)->nullable();
            $table->integer('story_points')->nullable();
            $table->json('labels')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_recurring')->default(false);
            $table->json('recurrence_rule')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
            $table->index('project_id');
            $table->index('assignee_id');
            $table->index('status');
            $table->index('due_date');
        });

        // Add self-referential FK after table is created so the primary key exists
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('tasks')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
