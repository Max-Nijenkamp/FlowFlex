<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_accounts', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('industry')->nullable();
            $table->unsignedInteger('employee_count')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->foreignUlid('owner_id')->constrained('users');
            $table->unsignedBigInteger('lifetime_value_cents')->default(0);
            $table->jsonb('custom_fields')->default('{}');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'owner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_accounts');
    }
};
