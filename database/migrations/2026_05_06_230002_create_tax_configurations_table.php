<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_configurations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->char('company_id', 26)->index();
            $table->char('payroll_entity_id', 26)->index();
            $table->string('country_code', 2);
            $table->smallInteger('tax_year');
            $table->json('configuration');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['payroll_entity_id', 'tax_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_configurations');
    }
};
