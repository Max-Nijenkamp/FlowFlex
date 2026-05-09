<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_feature_flags', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->nullable()->references('id')->on('companies');
            $table->string('flag');
            $table->boolean('enabled')->default(false);
            $table->timestamps();
            $table->unique(['company_id', 'flag']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_feature_flags');
    }
};
