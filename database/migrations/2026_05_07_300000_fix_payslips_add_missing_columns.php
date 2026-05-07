<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            if (! Schema::hasColumn('payslips', 'period_start')) {
                $table->date('period_start')->nullable()->after('pay_run_employee_id');
            }

            if (! Schema::hasColumn('payslips', 'period_end')) {
                $table->date('period_end')->nullable()->after('period_start');
            }

            if (! Schema::hasColumn('payslips', 'status')) {
                $table->string('status')->default('generated')->after('period_end');
            }

            if (! Schema::hasColumn('payslips', 'pdf_path')) {
                $table->string('pdf_path')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('payslips', 'period_start')) {
                $columns[] = 'period_start';
            }

            if (Schema::hasColumn('payslips', 'period_end')) {
                $columns[] = 'period_end';
            }

            if (Schema::hasColumn('payslips', 'status')) {
                $columns[] = 'status';
            }

            if (Schema::hasColumn('payslips', 'pdf_path')) {
                $columns[] = 'pdf_path';
            }

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
