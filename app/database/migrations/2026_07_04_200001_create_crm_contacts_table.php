<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_contacts', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable(); // E.164
            $table->string('job_title')->nullable();
            $table->foreignUlid('account_id')->nullable()->constrained('crm_accounts')->nullOnDelete();
            $table->string('lifecycle_stage')->default('lead');
            $table->string('source')->nullable();
            $table->foreignUlid('owner_id')->constrained('users');
            $table->jsonb('custom_fields')->default('{}');
            $table->timestamps();
            $table->softDeletes();

            // Duplicate detection: unique per company where email present.
            $table->unique(['company_id', 'email']);
            $table->index(['company_id', 'lifecycle_stage']);
            $table->index(['company_id', 'owner_id']);
            $table->index(['company_id', 'account_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_contacts');
    }
};
