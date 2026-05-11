<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();

            $table->ulid('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();

            $table->ulid('policy_id');
            $table->foreign('policy_id')->references('id')->on('leave_policies')->cascadeOnDelete();

            $table->integer('year');
            $table->decimal('allocated_days', 5, 1);
            $table->decimal('used_days', 5, 1)->default(0);
            $table->decimal('pending_days', 5, 1)->default(0);

            $table->timestamps();

            $table->unique(['employee_id', 'policy_id', 'year']);
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
    }
};
