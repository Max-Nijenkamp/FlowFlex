<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_custom_field_values', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->char('company_id', 26)->index();
            $table->char('employee_id', 26)->index();
            $table->char('custom_field_id', 26)->index();
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'custom_field_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_custom_field_values');
    }
};
