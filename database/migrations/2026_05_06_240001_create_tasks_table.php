<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->char('company_id', 26)->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->char('parent_task_id', 26)->nullable()->index();
            $table->string('priority')->default('p3_medium');
            $table->string('status')->default('todo');
            $table->char('assignee_tenant_id', 26)->nullable()->index();
            $table->date('due_date')->nullable();
            $table->date('start_date')->nullable();
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_rule')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
