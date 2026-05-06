<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboarding_tasks', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->char('company_id', 26)->index();
            $table->char('flow_id', 26)->index();
            $table->char('template_task_id', 26)->nullable()->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('task_type')->default('read_acknowledge');
            $table->char('assigned_to_tenant_id', 26)->nullable()->index();
            $table->date('due_date')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->text('completion_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_tasks');
    }
};
