<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_expense_categories', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->bigInteger('limit_per_transaction_cents')->nullable();
            $table->foreignUlid('gl_account_id')->nullable()->constrained('fin_accounts')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'name']);
        });

        Schema::create('fin_expenses', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete(); // submitter
            $table->ulid('employee_id')->nullable(); // hr_employees when HR active
            $table->foreignUlid('category_id')->constrained('fin_expense_categories')->cascadeOnDelete();
            $table->bigInteger('amount_cents');
            $table->string('currency', 3)->default('EUR');
            $table->date('expense_date');
            $table->string('merchant');
            $table->text('description')->nullable();
            $table->string('status')->default('draft');
            $table->boolean('is_over_limit')->default(false);
            $table->foreignUlid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reimbursed_via')->nullable(); // payroll / bank-transfer
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'user_id']);
        });

        Schema::create('fin_bank_accounts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('bank_name');
            $table->text('account_number')->nullable(); // encrypted
            $table->text('iban')->nullable(); // encrypted
            $table->string('iban_last4', 4)->nullable(); // display
            $table->string('currency', 3)->default('EUR');
            $table->foreignUlid('gl_account_id')->nullable()->constrained('fin_accounts')->nullOnDelete();
            $table->bigInteger('current_balance_cents')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
        });

        Schema::create('fin_bank_transactions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('bank_account_id')->constrained('fin_bank_accounts')->cascadeOnDelete();
            $table->date('transaction_date');
            $table->string('description');
            $table->bigInteger('amount_cents'); // signed
            $table->string('import_hash'); // dedupe: hash(date+amount+description)
            $table->timestamp('reconciled_at')->nullable();
            $table->ulid('journal_line_id')->nullable();
            $table->timestamps();

            $table->unique(['bank_account_id', 'import_hash']);
            $table->index(['company_id', 'bank_account_id', 'reconciled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_bank_transactions');
        Schema::dropIfExists('fin_bank_accounts');
        Schema::dropIfExists('fin_expenses');
        Schema::dropIfExists('fin_expense_categories');
    }
};
