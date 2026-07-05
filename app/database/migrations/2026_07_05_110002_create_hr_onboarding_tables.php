<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_onboarding_templates', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignUlid('department_id')->nullable()->constrained('hr_departments')->nullOnDelete();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hr_onboarding_tasks', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('template_id')->constrained('hr_onboarding_templates')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('assigned_role'); // hr / it / manager / employee
            $table->unsignedInteger('due_days_after_start')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hr_onboarding_plans', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignUlid('template_id')->constrained('hr_onboarding_templates');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'completed_at']);
        });

        Schema::create('hr_onboarding_plan_tasks', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('plan_id')->constrained('hr_onboarding_plans')->cascadeOnDelete();
            $table->foreignUlid('task_id')->constrained('hr_onboarding_tasks');
            $table->string('status')->default('pending'); // pending / complete / skipped
            $table->foreignUlid('completed_by')->nullable()->constrained('users');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_onboarding_plan_tasks');
        Schema::dropIfExists('hr_onboarding_plans');
        Schema::dropIfExists('hr_onboarding_tasks');
        Schema::dropIfExists('hr_onboarding_templates');
    }
};
