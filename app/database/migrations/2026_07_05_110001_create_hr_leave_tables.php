<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_leave_types', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('color', 7)->default('#4ADE80');
            $table->decimal('accrual_days_per_year', 5, 2)->default(0);
            $table->unsignedInteger('carry_over_days')->default(0);
            $table->boolean('requires_approval')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'name']);
        });

        Schema::create('hr_leave_balances', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignUlid('leave_type_id')->constrained('hr_leave_types')->cascadeOnDelete();
            $table->unsignedInteger('year');
            $table->decimal('allocated_days', 5, 2)->default(0);
            $table->decimal('taken_days', 5, 2)->default(0);
            $table->decimal('pending_days', 5, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'employee_id', 'leave_type_id', 'year']);
        });

        Schema::create('hr_leave_requests', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->foreignUlid('leave_type_id')->constrained('hr_leave_types');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('days_requested', 5, 2);
            $table->string('status')->default('draft');
            $table->text('note')->nullable();
            $table->foreignUlid('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'employee_id', 'status']);
            $table->index(['company_id', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_leave_requests');
        Schema::dropIfExists('hr_leave_balances');
        Schema::dropIfExists('hr_leave_types');
    }
};
