<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- finance.ar ---
        Schema::create('fin_ar_dunning_rules', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->string('aging_bucket');
            $t->integer('days_overdue');
            $t->string('email_template');
            $t->integer('escalation_level');
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['company_id', 'escalation_level']);
        });

        Schema::create('fin_ar_writeoffs', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->foreignUlid('invoice_id')->constrained('fin_invoices');
            $t->bigInteger('amount_cents');
            $t->text('reason');
            $t->foreignUlid('approved_by')->constrained('users');
            $t->timestamp('written_off_at');
            $t->timestamps();
        });

        Schema::table('fin_invoices', function (Blueprint $t): void {
            $t->integer('last_dunning_level')->default(0);
        });

        Schema::table('fin_customers', function (Blueprint $t): void {
            $t->bigInteger('credit_limit_cents')->nullable();
        });

        // --- finance.ap ---
        Schema::create('fin_suppliers', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('email')->nullable();
            $t->string('vat_number')->nullable();
            $t->text('iban')->nullable(); // encrypted
            $t->string('iban_last4', 4)->nullable();
            $t->integer('payment_terms_days')->default(30);
            $t->timestamps();
            $t->softDeletes();
        });

        Schema::create('fin_payment_runs', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->date('run_date');
            $t->bigInteger('total_cents')->default(0);
            $t->string('status')->default('draft');
            $t->timestamps();
        });

        Schema::create('fin_bills', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->foreignUlid('supplier_id')->constrained('fin_suppliers');
            $t->string('bill_number');
            $t->ulid('po_id')->nullable();
            $t->bigInteger('amount_cents');
            $t->string('currency', 3)->default('EUR');
            $t->date('bill_date');
            $t->date('due_date');
            $t->string('status')->default('draft');
            $t->decimal('early_discount_percent', 5, 2)->nullable();
            $t->date('early_discount_until')->nullable();
            $t->foreignUlid('approved_by')->nullable()->constrained('users');
            $t->timestamp('paid_at')->nullable();
            $t->foreignUlid('payment_run_id')->nullable()->constrained('fin_payment_runs');
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['company_id', 'supplier_id', 'bill_number']);
            $t->index(['company_id', 'status', 'due_date']);
        });

        Schema::create('fin_bill_lines', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('bill_id')->constrained('fin_bills')->cascadeOnDelete();
            $t->foreignUlid('company_id')->index();
            $t->string('description');
            $t->foreignUlid('account_id')->constrained('fin_accounts');
            $t->bigInteger('amount_cents');
            $t->timestamps();
        });

        // --- finance.budgets ---
        Schema::create('fin_budgets', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->integer('fiscal_year');
            $t->string('scope_type')->default('company');
            $t->ulid('scope_id')->nullable();
            $t->string('status')->default('draft');
            $t->integer('version')->default(1);
            $t->timestamps();
            $t->softDeletes();
            $t->unique(['company_id', 'name', 'fiscal_year', 'version']);
        });

        Schema::create('fin_budget_lines', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('budget_id')->constrained('fin_budgets')->cascadeOnDelete();
            $t->foreignUlid('company_id')->index();
            $t->foreignUlid('account_id')->constrained('fin_accounts');
            $t->string('period', 7); // YYYY-MM
            $t->bigInteger('budgeted_cents');
            $t->timestamps();
            $t->unique(['budget_id', 'account_id', 'period']);
        });

        // --- finance.tax ---
        Schema::create('fin_tax_rates', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->integer('rate_basis_points'); // 2100 = 21%
            $t->string('type')->default('vat');
            $t->string('jurisdiction', 2);
            $t->boolean('is_reverse_charge')->default(false);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->softDeletes();
        });

        Schema::create('fin_tax_classes', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->foreignUlid('default_rate_id')->constrained('fin_tax_rates');
            $t->timestamps();
            $t->softDeletes();
        });

        Schema::create('fin_tax_periods', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->string('period'); // YYYY-Qn or YYYY-MM
            $t->bigInteger('output_tax_cents')->default(0);
            $t->bigInteger('input_tax_cents')->default(0);
            $t->bigInteger('net_payable_cents')->default(0);
            $t->string('status')->default('open');
            $t->timestamps();
            $t->unique(['company_id', 'period']);
        });

        // --- finance.cashflow ---
        Schema::create('fin_cashflow_projections', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->date('week_start');
            $t->bigInteger('opening_cents')->default(0);
            $t->bigInteger('inflow_cents')->default(0);
            $t->bigInteger('outflow_cents')->default(0);
            $t->bigInteger('closing_cents')->default(0);
            $t->boolean('is_actual')->default(false);
            $t->timestamps();
            $t->unique(['company_id', 'week_start', 'is_actual']);
        });

        Schema::create('fin_cashflow_items', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('projection_id')->constrained('fin_cashflow_projections')->cascadeOnDelete();
            $t->foreignUlid('company_id')->index();
            $t->string('type'); // inflow / outflow
            $t->string('source'); // invoice / bill / payroll / manual
            $t->ulid('source_id')->nullable();
            $t->string('description');
            $t->bigInteger('amount_cents');
            $t->date('expected_date');
            $t->timestamps();
        });

        // --- finance.assets ---
        Schema::create('fin_fixed_assets', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('category');
            $t->bigInteger('cost_cents');
            $t->date('purchase_date');
            $t->integer('useful_life_months');
            $t->string('method')->default('straight-line');
            $t->bigInteger('salvage_cents')->default(0);
            $t->bigInteger('accumulated_depreciation_cents')->default(0);
            $t->string('status')->default('active');
            $t->ulid('it_asset_id')->nullable();
            $t->timestamp('disposed_at')->nullable();
            $t->bigInteger('disposal_proceeds_cents')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });

        Schema::create('fin_depreciation_entries', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('asset_id')->constrained('fin_fixed_assets')->cascadeOnDelete();
            $t->foreignUlid('company_id')->index();
            $t->string('period', 7);
            $t->bigInteger('depreciation_cents');
            $t->foreignUlid('journal_entry_id')->constrained('fin_journal_entries');
            $t->timestamps();
            $t->unique(['asset_id', 'period']);
        });

        // --- finance.forecasting ---
        Schema::create('fin_forecasts', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('scenario')->default('base');
            $t->integer('fiscal_year');
            $t->json('assumptions')->nullable();
            $t->timestamps();
            $t->softDeletes();
        });

        Schema::create('fin_forecast_lines', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('forecast_id')->constrained('fin_forecasts')->cascadeOnDelete();
            $t->foreignUlid('company_id')->index();
            $t->foreignUlid('account_id')->constrained('fin_accounts');
            $t->string('period', 7);
            $t->bigInteger('projected_cents');
            $t->timestamps();
            $t->unique(['forecast_id', 'account_id', 'period']);
        });

        // --- finance.currency ---
        Schema::create('fin_currencies', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->string('code', 3);
            $t->string('symbol');
            $t->integer('minor_unit_digits')->default(2);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->unique(['company_id', 'code']);
        });

        Schema::create('fin_exchange_rates', function (Blueprint $t): void {
            $t->ulid('id')->primary();
            $t->foreignUlid('company_id')->index()->constrained()->cascadeOnDelete();
            $t->string('from_currency', 3);
            $t->string('to_currency', 3);
            $t->decimal('rate', 16, 8);
            $t->date('effective_date');
            $t->timestamps();
            $t->unique(['company_id', 'from_currency', 'to_currency', 'effective_date']);
        });
    }

    public function down(): void
    {
        foreach (['fin_exchange_rates', 'fin_currencies', 'fin_forecast_lines', 'fin_forecasts',
            'fin_depreciation_entries', 'fin_fixed_assets', 'fin_cashflow_items', 'fin_cashflow_projections',
            'fin_tax_periods', 'fin_tax_classes', 'fin_tax_rates', 'fin_budget_lines', 'fin_budgets',
            'fin_bill_lines', 'fin_bills', 'fin_payment_runs', 'fin_suppliers',
            'fin_ar_writeoffs', 'fin_ar_dunning_rules'] as $table) {
            Schema::dropIfExists($table);
        }
        Schema::table('fin_invoices', fn (Blueprint $t) => $t->dropColumn('last_dunning_level'));
        Schema::table('fin_customers', fn (Blueprint $t) => $t->dropColumn('credit_limit_cents'));
    }
};
