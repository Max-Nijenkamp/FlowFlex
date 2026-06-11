<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_accounts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('code'); // e.g. 1100
            $table->string('name');
            $table->string('type'); // asset / liability / equity / revenue / expense
            $table->foreignUlid('parent_account_id')->nullable()->constrained('fin_accounts')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'code']);
        });

        Schema::create('fin_fiscal_periods', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('period', 7); // YYYY-MM
            $table->string('status')->default('open'); // open / closed
            $table->foreignUlid('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'period']);
        });

        Schema::create('fin_journal_entries', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('reference');
            $table->string('description');
            $table->date('entry_date'); // must fall in an open fiscal period
            $table->string('status')->default('posted'); // posted entries never deleted — reversals only
            $table->string('source_type')->nullable();
            $table->ulid('source_id')->nullable();
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'entry_date']);
            $table->index(['company_id', 'source_type', 'source_id']);
        });

        Schema::create('fin_journal_lines', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('journal_entry_id')->constrained('fin_journal_entries')->cascadeOnDelete();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('account_id')->constrained('fin_accounts')->cascadeOnDelete();
            $table->bigInteger('debit_cents')->default(0);
            $table->bigInteger('credit_cents')->default(0);
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'account_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_journal_lines');
        Schema::dropIfExists('fin_journal_entries');
        Schema::dropIfExists('fin_fiscal_periods');
        Schema::dropIfExists('fin_accounts');
    }
};
