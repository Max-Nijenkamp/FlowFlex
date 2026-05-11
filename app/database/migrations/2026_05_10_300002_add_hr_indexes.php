<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->index(['company_id', 'status'], 'employees_company_status');
            $table->index(['company_id', 'department'], 'employees_company_department');
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            $table->index(['employee_id', 'status'], 'leave_requests_employee_status');
            $table->index(['employee_id', 'start_date', 'end_date'], 'leave_requests_employee_dates');
        });

        Schema::table('leave_balances', function (Blueprint $table) {
            $table->index(['employee_id', 'policy_id', 'year'], 'leave_balances_employee_policy_year');
        });

        Schema::table('payroll_entries', function (Blueprint $table) {
            $table->index(['run_id'], 'payroll_entries_run_id');
            $table->index(['company_id', 'employee_id'], 'payroll_entries_company_employee');
        });

        Schema::table('onboarding_checklists', function (Blueprint $table) {
            $table->index(['employee_id'], 'onboarding_checklists_employee_id');
        });
    }

    public function down(): void
    {
        Schema::table('employees', fn (Blueprint $t) => $t->dropIndex('employees_company_status'));
        Schema::table('employees', fn (Blueprint $t) => $t->dropIndex('employees_company_department'));
        Schema::table('leave_requests', fn (Blueprint $t) => $t->dropIndex('leave_requests_employee_status'));
        Schema::table('leave_requests', fn (Blueprint $t) => $t->dropIndex('leave_requests_employee_dates'));
        Schema::table('leave_balances', fn (Blueprint $t) => $t->dropIndex('leave_balances_employee_policy_year'));
        Schema::table('payroll_entries', fn (Blueprint $t) => $t->dropIndex('payroll_entries_run_id'));
        Schema::table('payroll_entries', fn (Blueprint $t) => $t->dropIndex('payroll_entries_company_employee'));
        Schema::table('onboarding_checklists', fn (Blueprint $t) => $t->dropIndex('onboarding_checklists_employee_id'));
    }
};
