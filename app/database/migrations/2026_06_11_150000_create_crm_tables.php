<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_accounts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('industry')->nullable();
            $table->integer('employee_count')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->foreignUlid('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->bigInteger('lifetime_value_cents')->default(0); // InvoicePaid listener
            $table->json('custom_fields')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
        });

        Schema::create('crm_contacts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable(); // E.164
            $table->string('job_title')->nullable();
            $table->foreignUlid('account_id')->nullable()->constrained('crm_accounts')->nullOnDelete();
            $table->string('lifecycle_stage')->default('lead'); // plain string — any move allowed
            $table->string('source')->nullable();
            $table->foreignUlid('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('custom_fields')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'email']);
            $table->index(['company_id', 'lifecycle_stage']);
            $table->index(['company_id', 'owner_id']);
        });

        Schema::create('crm_pipeline_stages', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->integer('order')->default(0);
            $table->decimal('probability_default', 5, 2)->default(20);
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
        });

        Schema::create('crm_deals', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->foreignUlid('account_id')->nullable()->constrained('crm_accounts')->nullOnDelete();
            $table->foreignUlid('contact_id')->nullable()->constrained('crm_contacts')->nullOnDelete();
            $table->foreignUlid('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUlid('stage_id')->constrained('crm_pipeline_stages')->cascadeOnDelete();
            $table->bigInteger('value_cents')->default(0);
            $table->string('currency', 3)->default('EUR');
            $table->decimal('probability', 5, 2)->default(20);
            $table->date('expected_close_date')->nullable();
            $table->date('actual_close_date')->nullable();
            $table->string('status')->default('open'); // open / won / lost
            $table->text('lost_reason')->nullable();
            $table->timestamp('stage_entered_at');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'stage_id']);
        });

        Schema::create('crm_activities', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('type'); // call / email / meeting / note / task
            $table->string('subject');
            $table->text('body')->nullable();
            $table->foreignUlid('contact_id')->nullable()->constrained('crm_contacts')->cascadeOnDelete();
            $table->foreignUlid('deal_id')->nullable()->constrained('crm_deals')->cascadeOnDelete();
            $table->foreignUlid('owner_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'contact_id']);
            $table->index(['company_id', 'deal_id']);
        });

        Schema::create('crm_quotes', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('deal_id')->nullable()->constrained('crm_deals')->nullOnDelete();
            $table->foreignUlid('contact_id')->nullable()->constrained('crm_contacts')->nullOnDelete();
            $table->string('quote_number')->nullable();
            $table->string('status')->default('draft'); // draft / sent / accepted / declined / expired
            $table->bigInteger('total_cents')->default(0);
            $table->string('currency', 3)->default('EUR');
            $table->date('valid_until')->nullable();
            $table->uuid('accept_token')->nullable()->unique(); // signed public accept
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
        });

        Schema::create('crm_quote_lines', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('quote_id')->constrained('crm_quotes')->cascadeOnDelete();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('description');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->bigInteger('unit_price_cents');
            $table->bigInteger('line_total_cents')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_quote_lines');
        Schema::dropIfExists('crm_quotes');
        Schema::dropIfExists('crm_activities');
        Schema::dropIfExists('crm_deals');
        Schema::dropIfExists('crm_pipeline_stages');
        Schema::dropIfExists('crm_contacts');
        Schema::dropIfExists('crm_accounts');
    }
};
