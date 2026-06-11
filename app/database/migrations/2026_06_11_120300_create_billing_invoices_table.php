<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_invoices', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->bigInteger('total_cents'); // minor units, brick/money for arithmetic
            $table->string('currency', 3);
            $table->string('stripe_invoice_id')->nullable()->unique();
            $table->string('status')->default('draft'); // spatie/laravel-model-states
            $table->timestamp('paid_at')->nullable();
            $table->unsignedTinyInteger('dunning_attempts')->default(0);
            $table->timestamp('next_dunning_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'period_start']); // idempotent monthly generation
            $table->index(['company_id', 'status']);
        });

        Schema::create('billing_invoice_lines', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('invoice_id')->constrained('billing_invoices')->cascadeOnDelete();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('module_key');
            $table->string('module_name'); // snapshot at billing time
            $table->unsignedInteger('user_count');
            $table->bigInteger('unit_price_cents');
            $table->bigInteger('line_total_cents');
            $table->timestamps();
        });

        // Stripe customer reference (encrypted at rest per spec).
        Schema::table('companies', function (Blueprint $table) {
            $table->text('stripe_customer_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('companies', fn (Blueprint $table) => $table->dropColumn('stripe_customer_id'));
        Schema::dropIfExists('billing_invoice_lines');
        Schema::dropIfExists('billing_invoices');
    }
};
