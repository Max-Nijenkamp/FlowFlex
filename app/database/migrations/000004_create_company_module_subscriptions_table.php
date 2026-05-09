<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_module_subscriptions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->references('id')->on('companies');
            $table->string('module_key'); // e.g. hr.payroll, finance.invoicing
            $table->string('status')->default('active'); // active|inactive|trial|suspended
            $table->json('settings')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('deactivated_at')->nullable();
            $table->timestamps();
            $table->index('company_id');
            $table->unique(['company_id', 'module_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_module_subscriptions');
    }
};
