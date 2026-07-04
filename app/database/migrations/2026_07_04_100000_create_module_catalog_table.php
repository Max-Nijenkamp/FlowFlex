<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_catalog', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('module_key')->unique();
            $table->string('domain');
            $table->string('name');
            // Minor currency units (euro cents) — brick/money convention, never floats.
            $table->unsignedInteger('per_user_monthly_price')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_catalog');
    }
};
