<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demo_requests', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('company_name');
            $table->string('company_size');
            $table->json('modules_interested')->nullable();
            $table->string('heard_from')->nullable();
            $table->text('notes')->nullable();
            $table->string('phone')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('status')->default('new');
            $table->string('assigned_to')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->text('notes_internal')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_requests');
    }
};
