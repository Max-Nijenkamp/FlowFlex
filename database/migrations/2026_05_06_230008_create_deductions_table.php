<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deductions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->char('company_id', 26)->index();
            $table->char('employee_id', 26)->index();
            $table->char('pay_element_id', 26)->nullable()->index();
            $table->string('name');
            $table->string('deduction_type')->default('other');
            $table->decimal('amount', 12, 2);
            $table->boolean('is_percentage')->default(false);
            $table->boolean('is_recurring')->default(true);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deductions');
    }
};
