<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            $table->ulid('crm_contact_id')->nullable();
            $table->ulid('crm_company_id')->nullable();
            $table->ulid('pipeline_id')->nullable();
            $table->ulid('deal_stage_id')->nullable();
            $table->string('title');
            $table->decimal('value', 12, 2)->nullable();
            $table->string('currency', 3)->default('EUR');
            $table->string('status')->default('open');
            $table->integer('close_probability')->nullable();
            $table->date('expected_close_date')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->text('lost_reason')->nullable();
            $table->ulid('owner_tenant_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
