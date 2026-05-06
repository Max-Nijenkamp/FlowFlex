<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_holidays', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->char('company_id', 26)->nullable()->index();
            $table->string('country_code', 2);
            $table->string('name');
            $table->date('date');
            $table->boolean('is_recurring')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_holidays');
    }
};
