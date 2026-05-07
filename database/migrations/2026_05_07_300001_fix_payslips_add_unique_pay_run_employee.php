<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            $table->unique(['pay_run_id', 'employee_id'], 'payslips_pay_run_id_employee_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            $table->dropUnique('payslips_pay_run_id_employee_id_unique');
        });
    }
};
