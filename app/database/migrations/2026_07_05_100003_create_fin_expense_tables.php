<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_expense_categories', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedBigInteger('limit_per_transaction_cents')->nullable();
            $table->foreignUlid('gl_account_id')->constrained('fin_accounts');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'name']);
        });

        Schema::create('fin_expense_reports', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained('users');
            $table->string('title');
            $table->date('period_start');
            $table->date('period_end');
            $table->string('status')->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('fin_expenses', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained('users');
            $table->ulid('employee_id')->nullable(); // FK once hr_employees exists
            $table->foreignUlid('category_id')->constrained('fin_expense_categories');
            $table->unsignedBigInteger('amount_cents');
            $table->string('currency', 3)->default('EUR');
            $table->date('expense_date');
            $table->string('merchant');
            $table->text('description')->nullable();
            $table->string('status')->default('draft');
            $table->boolean('is_over_limit')->default(false);
            $table->foreignUlid('approved_by')->nullable()->constrained('users');
            $table->text('rejection_reason')->nullable();
            $table->foreignUlid('report_id')->nullable()->constrained('fin_expense_reports');
            $table->string('reimbursed_via')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_expenses');
        Schema::dropIfExists('fin_expense_reports');
        Schema::dropIfExists('fin_expense_categories');
    }
};
