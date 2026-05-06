<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_entries', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->char('company_id', 26)->index();
            $table->char('tenant_id', 26)->index();
            $table->char('task_id', 26)->nullable()->index();
            $table->string('description')->nullable();
            $table->date('entry_date');
            $table->integer('minutes');
            $table->boolean('is_billable')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->char('approved_by_tenant_id', 26)->nullable()->index();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('time_entries');
    }
};
