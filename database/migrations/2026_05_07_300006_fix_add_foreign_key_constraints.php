<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Each entry describes one FK to add.
     *
     * Keys:
     *   table      – table that owns the FK column
     *   column     – the FK column on that table
     *   references – column on the referenced table
     *   on         – the referenced table
     *   onDelete   – 'cascade' | 'set null' | 'restrict'
     *   name       – explicit constraint name (to stay under 63-char PG limit)
     */
    private array $foreignKeys = [
        [
            'table'      => 'employees',
            'column'     => 'company_id',
            'references' => 'id',
            'on'         => 'companies',
            'onDelete'   => 'cascade',
            'name'       => 'fk_employees_company_id',
        ],
        [
            'table'      => 'employees',
            'column'     => 'department_id',
            'references' => 'id',
            'on'         => 'departments',
            'onDelete'   => 'set null',
            'name'       => 'fk_employees_department_id',
        ],
        [
            'table'      => 'employees',
            'column'     => 'manager_id',
            'references' => 'id',
            'on'         => 'employees',
            'onDelete'   => 'set null',
            'name'       => 'fk_employees_manager_id',
        ],
        [
            'table'      => 'leave_requests',
            'column'     => 'employee_id',
            'references' => 'id',
            'on'         => 'employees',
            'onDelete'   => 'cascade',
            'name'       => 'fk_leave_requests_employee_id',
        ],
        [
            'table'      => 'leave_requests',
            'column'     => 'leave_type_id',
            'references' => 'id',
            'on'         => 'leave_types',
            'onDelete'   => 'restrict',
            'name'       => 'fk_leave_requests_leave_type_id',
        ],
        [
            'table'      => 'leave_balances',
            'column'     => 'employee_id',
            'references' => 'id',
            'on'         => 'employees',
            'onDelete'   => 'cascade',
            'name'       => 'fk_leave_balances_employee_id',
        ],
        [
            'table'      => 'leave_balances',
            'column'     => 'leave_type_id',
            'references' => 'id',
            'on'         => 'leave_types',
            'onDelete'   => 'restrict',
            'name'       => 'fk_leave_balances_leave_type_id',
        ],
        [
            'table'      => 'onboarding_flows',
            'column'     => 'employee_id',
            'references' => 'id',
            'on'         => 'employees',
            'onDelete'   => 'cascade',
            'name'       => 'fk_onboarding_flows_employee_id',
        ],
        [
            'table'      => 'onboarding_flows',
            'column'     => 'template_id',
            'references' => 'id',
            'on'         => 'onboarding_templates',
            'onDelete'   => 'set null',
            'name'       => 'fk_onboarding_flows_template_id',
        ],
        [
            'table'      => 'onboarding_tasks',
            'column'     => 'flow_id',
            'references' => 'id',
            'on'         => 'onboarding_flows',
            'onDelete'   => 'cascade',
            'name'       => 'fk_onboarding_tasks_flow_id',
        ],
        [
            'table'      => 'pay_runs',
            'column'     => 'company_id',
            'references' => 'id',
            'on'         => 'companies',
            'onDelete'   => 'cascade',
            'name'       => 'fk_pay_runs_company_id',
        ],
        [
            'table'      => 'pay_run_employees',
            'column'     => 'pay_run_id',
            'references' => 'id',
            'on'         => 'pay_runs',
            'onDelete'   => 'cascade',
            'name'       => 'fk_pay_run_employees_pay_run_id',
        ],
        [
            'table'      => 'pay_run_employees',
            'column'     => 'employee_id',
            'references' => 'id',
            'on'         => 'employees',
            'onDelete'   => 'cascade',
            'name'       => 'fk_pay_run_employees_employee_id',
        ],
        [
            'table'      => 'pay_run_lines',
            'column'     => 'pay_run_employee_id',
            'references' => 'id',
            'on'         => 'pay_run_employees',
            'onDelete'   => 'cascade',
            'name'       => 'fk_pay_run_lines_pay_run_employee_id',
        ],
        [
            'table'      => 'payslips',
            'column'     => 'pay_run_id',
            'references' => 'id',
            'on'         => 'pay_runs',
            'onDelete'   => 'cascade',
            'name'       => 'fk_payslips_pay_run_id',
        ],
        [
            'table'      => 'payslips',
            'column'     => 'employee_id',
            'references' => 'id',
            'on'         => 'employees',
            'onDelete'   => 'cascade',
            'name'       => 'fk_payslips_employee_id',
        ],
        [
            'table'      => 'salary_records',
            'column'     => 'employee_id',
            'references' => 'id',
            'on'         => 'employees',
            'onDelete'   => 'cascade',
            'name'       => 'fk_salary_records_employee_id',
        ],
        [
            'table'      => 'tasks',
            'column'     => 'company_id',
            'references' => 'id',
            'on'         => 'companies',
            'onDelete'   => 'cascade',
            'name'       => 'fk_tasks_company_id',
        ],
        [
            'table'      => 'tasks',
            'column'     => 'assignee_tenant_id',
            'references' => 'id',
            'on'         => 'tenants',
            'onDelete'   => 'set null',
            'name'       => 'fk_tasks_assignee_tenant_id',
        ],
        [
            'table'      => 'time_entries',
            'column'     => 'tenant_id',
            'references' => 'id',
            'on'         => 'tenants',
            'onDelete'   => 'cascade',
            'name'       => 'fk_time_entries_tenant_id',
        ],
        [
            'table'      => 'time_entries',
            'column'     => 'task_id',
            'references' => 'id',
            'on'         => 'tasks',
            'onDelete'   => 'set null',
            'name'       => 'fk_time_entries_task_id',
        ],
        [
            'table'      => 'timesheets',
            'column'     => 'tenant_id',
            'references' => 'id',
            'on'         => 'tenants',
            'onDelete'   => 'cascade',
            'name'       => 'fk_timesheets_tenant_id',
        ],
        [
            'table'      => 'timesheets',
            'column'     => 'company_id',
            'references' => 'id',
            'on'         => 'companies',
            'onDelete'   => 'cascade',
            'name'       => 'fk_timesheets_company_id',
        ],
        [
            'table'      => 'document_folders',
            'column'     => 'company_id',
            'references' => 'id',
            'on'         => 'companies',
            'onDelete'   => 'cascade',
            'name'       => 'fk_document_folders_company_id',
        ],
        [
            'table'      => 'documents',
            'column'     => 'company_id',
            'references' => 'id',
            'on'         => 'companies',
            'onDelete'   => 'cascade',
            'name'       => 'fk_documents_company_id',
        ],
        [
            'table'      => 'documents',
            'column'     => 'folder_id',
            'references' => 'id',
            'on'         => 'document_folders',
            'onDelete'   => 'set null',
            'name'       => 'fk_documents_folder_id',
        ],
    ];

    public function up(): void
    {
        foreach ($this->foreignKeys as $fk) {
            try {
                Schema::table($fk['table'], function (Blueprint $table) use ($fk) {
                    $table->foreign($fk['column'], $fk['name'])
                        ->references($fk['references'])
                        ->on($fk['on'])
                        ->onDelete($fk['onDelete']);
                });
            } catch (\Throwable $e) {
                // Log but do not abort — data inconsistencies or already-existing
                // constraints should not prevent the remaining FKs from being applied.
                \Illuminate\Support\Facades\Log::warning(
                    "FlowFlex FK migration: skipped {$fk['name']} — {$e->getMessage()}"
                );
            }
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->foreignKeys) as $fk) {
            try {
                Schema::table($fk['table'], function (Blueprint $table) use ($fk) {
                    $table->dropForeign($fk['name']);
                });
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning(
                    "FlowFlex FK migration rollback: skipped {$fk['name']} — {$e->getMessage()}"
                );
            }
        }
    }
};
