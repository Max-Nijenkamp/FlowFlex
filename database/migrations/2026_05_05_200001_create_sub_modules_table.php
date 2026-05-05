<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_modules', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('module_id')->constrained('modules')->cascadeOnDelete();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('sort_order')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_modules');
    }
};
