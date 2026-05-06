<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pay_run_lines', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->char('company_id', 26)->index();
            $table->char('pay_run_employee_id', 26)->index();
            $table->char('pay_element_id', 26)->nullable()->index();
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->boolean('is_deduction')->default(false);
            $table->string('source')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pay_run_lines');
    }
};
