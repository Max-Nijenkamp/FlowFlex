<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_customers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->json('address')->nullable();
            $table->string('vat_number')->nullable();
            $table->ulid('crm_account_id')->nullable(); // link when CRM active
            $table->integer('payment_terms_days')->default(14);
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
        });

        Schema::create('fin_invoices', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('customer_id')->constrained('fin_customers')->cascadeOnDelete();
            $table->string('invoice_number')->nullable(); // assigned at first send, gap-free per company
            $table->string('status')->default('draft');
            $table->date('issue_date');
            $table->date('due_date');
            $table->bigInteger('subtotal_cents')->default(0);
            $table->bigInteger('tax_total_cents')->default(0);
            $table->bigInteger('total_cents')->default(0);
            $table->bigInteger('paid_amount_cents')->default(0);
            $table->string('currency', 3)->default('EUR');
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->ulid('source_deal_id')->nullable(); // DealWon origin
            $table->string('pdf_path')->nullable();
            $table->timestamps();
            $table->softDeletes(); // kept 7y per data-lifecycle

            $table->unique(['company_id', 'invoice_number']);
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'due_date']);
        });

        Schema::create('fin_invoice_lines', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('invoice_id')->constrained('fin_invoices')->cascadeOnDelete();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('description');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->bigInteger('unit_price_cents');
            $table->bigInteger('tax_cents')->default(0);
            $table->bigInteger('line_total_cents')->default(0);
            $table->timestamps();
        });

        Schema::create('fin_payments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('invoice_id')->constrained('fin_invoices')->cascadeOnDelete();
            $table->bigInteger('amount_cents');
            $table->date('payment_date');
            $table->string('payment_method'); // bank-transfer / stripe / cash / other
            $table->string('reference_number')->nullable();
            $table->timestamps();
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
