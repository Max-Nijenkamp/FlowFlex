<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('subscription_status')->default('trial'); // trial / active / suspended / cancelled
            $table->string('timezone')->default('Europe/Amsterdam');
            $table->string('locale', 5)->default('en');
            $table->string('currency', 3)->default('EUR'); // ISO 4217
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('setup_completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
