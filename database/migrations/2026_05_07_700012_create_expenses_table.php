<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->ulid('tenant_id');
            $table->ulid('expense_report_id')->nullable();
            $table->ulid('expense_category_id')->nullable();
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('EUR');
            $table->date('expense_date');
            $table->string('status')->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->ulid('receipt_file_id')->nullable();
            $table->string('vendor')->nullable();
            $table->decimal('mileage_km', 8, 2)->nullable();
            $table->ulid('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
