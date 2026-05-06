<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pay_runs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->char('company_id', 26)->index();
            $table->char('payroll_entity_id', 26)->index();
            $table->string('status')->default('draft');
            $table->string('pay_frequency')->default('monthly');
            $table->date('pay_period_start');
            $table->date('pay_period_end');
            $table->date('payment_date');
            $table->decimal('total_gross', 12, 2)->default(0);
            $table->decimal('total_net', 12, 2)->default(0);
            $table->decimal('total_deductions', 12, 2)->default(0);
            $table->char('created_by_tenant_id', 26)->nullable()->index();
            $table->char('approved_by_tenant_id', 26)->nullable()->index();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pay_runs');
    }
};
