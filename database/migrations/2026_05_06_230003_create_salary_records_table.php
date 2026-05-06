<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_records', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->char('company_id', 26)->index();
            $table->char('employee_id', 26)->index();
            $table->text('salary_encrypted');
            $table->string('currency', 3)->default('EUR');
            $table->string('pay_frequency')->default('monthly');
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->text('notes')->nullable();
            $table->char('created_by_tenant_id', 26)->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_records');
    }
};
