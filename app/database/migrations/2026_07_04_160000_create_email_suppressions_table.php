<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_suppressions', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('email')->unique();
            $table->string('reason'); // bounce | complaint | soft-bounce
            $table->unsignedInteger('soft_bounce_count')->default(0);
            $table->timestamp('suppressed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_suppressions');
    }
};
