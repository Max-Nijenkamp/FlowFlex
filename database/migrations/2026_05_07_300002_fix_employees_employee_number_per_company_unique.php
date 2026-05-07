<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop the global unique index created in the original migration.
            $table->dropUnique(['employee_number']);

            // Make the column nullable — employee_number is optional.
            $table->string('employee_number')->nullable()->change();

            // Add composite unique so employee numbers are unique per company only.
            $table->unique(['company_id', 'employee_number'], 'employees_company_id_employee_number_unique');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropUnique('employees_company_id_employee_number_unique');

            $table->string('employee_number')->nullable(false)->change();

            $table->unique(['employee_number']);
        });
    }
};
