<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_payroll_employees', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('employee_id')->unique()->constrained('hr_employees')->cascadeOnDelete();
            $table->text('salary_raw')->nullable(); // encrypted integer cents (monthly gross)
            $table->string('salary_band')->nullable(); // coarse, derived — reporting only
            $table->text('iban')->nullable(); // encrypted
            $table->string('pay_type')->default('salaried'); // salaried / hourly
            $table->text('hourly_rate_raw')->nullable(); // encrypted integer cents
            $table->string('status')->default('incomplete'); // incomplete / ready
            $table->boolean('final_pay_flagged')->default(false); // EmployeeOffboarded
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
        });

        Schema::create('hr_deduction_types', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('calculation_type'); // percent / flat
            $table->integer('value'); // basis points (percent) / cents (flat)
            $table->boolean('is_employer_contribution')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
        });

        Schema::create('hr_payroll_runs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->string('status')->default('draft'); // state machine
            $table->bigInteger('total_gross_cents')->default(0);
            $table->bigInteger('total_net_cents')->default(0);
            $table->bigInteger('total_employer_cost_cents')->default(0);
            $table->string('currency', 3)->default('EUR');
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUlid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'period_start']);
        });

        Schema::create('hr_payslips', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('payroll_run_id')->constrained('hr_payroll_runs')->cascadeOnDelete();
            $table->foreignUlid('employee_id')->constrained('hr_employees')->cascadeOnDelete();
            $table->text('amounts_raw'); // encrypted json: gross/net/employer cost/deductions
            $table->string('pdf_path')->nullable(); // tenant-scoped
            $table->timestamps();
            $table->softDeletes(); // kept 7 years per data-lifecycle

            $table->unique(['payroll_run_id', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_payslips');
        Schema::dropIfExists('hr_payroll_runs');
        Schema::dropIfExists('hr_deduction_types');
        Schema::dropIfExists('hr_payroll_employees');
    }
};
