<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_module_subscriptions', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained()->cascadeOnDelete();
            $table->string('module_key');
            $table->timestamp('activated_at');
            // null = still active; deactivation keeps the row as history and
            // reactivation creates a fresh row (architecture/module-system.md)
            $table->timestamp('deactivated_at')->nullable();
            $table->foreignUlid('activated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'module_key', 'deactivated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_module_subscriptions');
    }
};
