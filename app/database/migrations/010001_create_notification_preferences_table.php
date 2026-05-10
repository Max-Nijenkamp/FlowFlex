<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('event_type');
            $table->string('channel');
            $table->boolean('enabled')->default(true);
            $table->string('delivery_mode')->default('realtime'); // realtime | digest
            $table->time('digest_time')->nullable();
            $table->string('timezone')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'event_type', 'channel']);
            $table->index(['company_id', 'user_id']);
        });

        Schema::create('notification_quiet_hours', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->time('start_time');
            $table->time('end_time');
            $table->string('timezone');
            $table->json('days_of_week')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });

        Schema::create('notification_log', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('event_type');
            $table->string('channel');
            $table->string('status'); // sent | delivered | read | failed
            $table->json('payload')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'user_id', 'sent_at']);
            $table->index(['user_id', 'read_at']);
        });

        Schema::create('notification_watches', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('watchable_type');
            $table->ulid('watchable_id');
            $table->timestamps();

            $table->unique(['user_id', 'watchable_type', 'watchable_id']);
            $table->index(['watchable_type', 'watchable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_watches');
        Schema::dropIfExists('notification_log');
        Schema::dropIfExists('notification_quiet_hours');
        Schema::dropIfExists('notification_preferences');
    }
};
