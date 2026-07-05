<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_activities', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // call / email / meeting / task / note
            $table->string('subject');
            $table->text('description')->nullable();
            $table->foreignUlid('owner_id')->constrained('users');
            $table->foreignUlid('contact_id')->nullable()->constrained('crm_contacts')->nullOnDelete();
            $table->foreignUlid('deal_id')->nullable()->constrained('crm_deals')->nullOnDelete();
            $table->foreignUlid('account_id')->nullable()->constrained('crm_accounts')->nullOnDelete();
            $table->timestamp('activity_date');
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->string('outcome')->nullable();
            $table->boolean('is_complete')->default(true);
            $table->timestamp('due_at')->nullable();
            $table->timestamp('reminded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'contact_id', 'activity_date']);
            $table->index(['company_id', 'deal_id', 'activity_date']);
            $table->index(['company_id', 'owner_id', 'is_complete', 'due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_activities');
    }
};
