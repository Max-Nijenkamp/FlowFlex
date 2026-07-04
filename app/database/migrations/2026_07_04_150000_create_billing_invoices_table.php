<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_invoices', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->unsignedBigInteger('total_cents');
            $table->string('currency', 3);
            $table->string('stripe_invoice_id')->nullable()->unique();
            $table->string('status')->default('draft');
            $table->timestamp('paid_at')->nullable();
            // dunning schedule (3 attempts over 14 days)
            $table->unsignedTinyInteger('dunning_attempts')->default(0);
            $table->timestamp('next_retry_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // idempotent generation: one invoice per company per period
            $table->unique(['company_id', 'period_start']);
        });

        Schema::create('billing_invoice_lines', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('invoice_id')->constrained('billing_invoices')->cascadeOnDelete();
            $table->foreignUlid('company_id')->index();
            $table->string('module_key');
            $table->string('module_name');
            $table->unsignedInteger('user_count');
            $table->unsignedBigInteger('unit_price_cents');
            $table->unsignedBigInteger('line_total_cents');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('companies', function (Blueprint $table): void {
            // encrypted cast on the model -> text column (code conventions)
            $table->text('stripe_customer_id')->nullable()->after('audit_retention_days');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_invoice_lines');
        Schema::dropIfExists('billing_invoices');
        Schema::table('companies', function (Blueprint $table): void {
            $table->dropColumn('stripe_customer_id');
        });
    }
};
