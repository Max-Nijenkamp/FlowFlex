<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_customers', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->jsonb('address')->default('{}');
            $table->string('vat_number')->nullable();
            $table->ulid('crm_account_id')->nullable(); // link when CRM active
            $table->unsignedInteger('payment_terms_days')->default(14);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('fin_invoices', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('customer_id')->constrained('fin_customers');
            $table->string('invoice_number')->nullable(); // assigned on send
            $table->string('status')->default('draft');
            $table->date('issue_date');
            $table->date('due_date');
            $table->unsignedBigInteger('subtotal_cents')->default(0);
            $table->unsignedBigInteger('tax_total_cents')->default(0);
            $table->unsignedBigInteger('total_cents')->default(0);
            $table->unsignedBigInteger('paid_amount_cents')->default(0);
            $table->string('currency', 3)->default('EUR');
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('recurring_schedule')->nullable(); // monthly / quarterly / annually
            $table->date('next_recurring_at')->nullable();
            $table->ulid('source_deal_id')->nullable(); // DealWon origin
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'invoice_number']);
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'due_date']);
            $table->index(['company_id', 'customer_id']);
        });

        Schema::create('fin_invoice_lines', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('invoice_id')->constrained('fin_invoices')->cascadeOnDelete();
            $table->string('description');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->unsignedBigInteger('unit_price_cents');
            $table->decimal('tax_rate_percent', 5, 2)->default(21); // NL default until finance.tax
            $table->unsignedBigInteger('tax_cents')->default(0);
            $table->unsignedBigInteger('line_total_cents')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('fin_payments', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('invoice_id')->constrained('fin_invoices');
            $table->unsignedBigInteger('amount_cents');
            $table->date('payment_date');
            $table->string('method')->nullable(); // bank-transfer / cash / other
            $table->string('reference')->nullable();
            $table->foreignUlid('recorded_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_payments');
        Schema::dropIfExists('fin_invoice_lines');
        Schema::dropIfExists('fin_invoices');
        Schema::dropIfExists('fin_customers');
    }
};
