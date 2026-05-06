<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_policies', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->char('company_id', 26)->index();
            $table->char('leave_type_id', 26)->index();
            $table->string('accrual_type')->default('immediate');
            $table->decimal('annual_entitlement_days', 8, 2)->default(0);
            $table->decimal('max_carry_over_days', 8, 2)->default(0);
            $table->boolean('allow_negative')->default(false);
            $table->integer('probation_restriction_months')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_policies');
    }
};
