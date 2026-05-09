<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->references('id')->on('companies');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('password')->nullable();
            $table->string('locale')->default('en');
            $table->string('timezone')->default('UTC');
            $table->string('status')->default('invited'); // invited|active|deactivated
            $table->boolean('two_factor_enabled')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['company_id', 'email']);
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
