<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_bank_accounts', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('bank_name');
            $table->text('account_number')->nullable(); // encrypted
            $table->text('iban')->nullable(); // encrypted
            $table->string('iban_last4', 4)->nullable();
            $table->string('currency', 3)->default('EUR');
            $table->foreignUlid('gl_account_id')->constrained('fin_accounts');
            $table->bigInteger('current_balance_cents')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('fin_bank_transactions', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('bank_account_id')->constrained('fin_bank_accounts')->cascadeOnDelete();
            $table->date('transaction_date');
            $table->string('description');
            $table->bigInteger('amount_cents'); // signed
            $table->string('import_hash');
            $table->timestamp('reconciled_at')->nullable();
            $table->foreignUlid('journal_line_id')->nullable()->constrained('fin_journal_lines');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['bank_account_id', 'import_hash']);
            $table->index(['company_id', 'bank_account_id', 'reconciled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_bank_transactions');
        Schema::dropIfExists('fin_bank_accounts');
    }
};
