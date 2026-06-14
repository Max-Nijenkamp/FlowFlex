<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_leads', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('source')->default('manual'); // manual / website / referral / event / import
            $table->string('status')->default('new');     // new / working / qualified / unqualified / converted
            $table->foreignUlid('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->bigInteger('estimated_value_cents')->default(0);
            $table->text('notes')->nullable();
            $table->foreignUlid('converted_deal_id')->nullable()->constrained('crm_deals')->nullOnDelete();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_leads');
    }
};
