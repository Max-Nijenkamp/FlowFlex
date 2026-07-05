<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_contact_accounts', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('contact_id')->constrained('crm_contacts')->cascadeOnDelete();
            $table->foreignUlid('account_id')->constrained('crm_accounts')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['contact_id', 'account_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_contact_accounts');
    }
};
