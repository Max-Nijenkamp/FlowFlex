<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_catalog', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('module_key')->unique();
            $table->string('domain');
            $table->string('name');
            $table->decimal('per_user_monthly_price', 8, 2)->default(0.00);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_catalog');
    }
};
