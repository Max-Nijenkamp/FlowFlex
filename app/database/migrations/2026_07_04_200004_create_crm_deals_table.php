<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_deals', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->foreignUlid('account_id')->nullable()->constrained('crm_accounts')->nullOnDelete();
            $table->foreignUlid('contact_id')->nullable()->constrained('crm_contacts')->nullOnDelete();
            $table->foreignUlid('owner_id')->constrained('users');
            $table->foreignUlid('stage_id')->constrained('crm_pipeline_stages');
            $table->unsignedBigInteger('value_cents')->default(0);
            $table->string('currency', 3)->default('EUR');
            $table->decimal('probability', 5, 2)->default(0);
            $table->date('expected_close_date')->nullable();
            $table->date('actual_close_date')->nullable();
            $table->string('status')->default('open');
            $table->text('lost_reason')->nullable();
            $table->string('lost_to')->nullable();
            $table->timestamp('stage_entered_at');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'stage_id']);
            $table->index(['company_id', 'owner_id']);
            $table->index(['company_id', 'expected_close_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_deals');
    }
};
