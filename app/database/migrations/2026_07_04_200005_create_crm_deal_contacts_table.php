<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_deal_contacts', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('deal_id')->constrained('crm_deals')->cascadeOnDelete();
            $table->foreignUlid('contact_id')->constrained('crm_contacts')->cascadeOnDelete();
            $table->string('role')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['deal_id', 'contact_id']);
        });

        Schema::create('crm_deal_products', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('deal_id')->constrained('crm_deals')->cascadeOnDelete();
            $table->ulid('product_id')->nullable(); // FK to catalog when crm.pricing ships
            $table->string('description');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->unsignedBigInteger('unit_price_cents');
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_deal_products');
        Schema::dropIfExists('crm_deal_contacts');
    }
};
