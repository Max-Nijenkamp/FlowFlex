<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('log_name')->nullable()->index();
            $table->text('description');
            $table->nullableUlidMorphs('subject', 'subject');
            $table->string('event')->nullable();
            $table->nullableUlidMorphs('causer', 'causer');
            $table->json('attribute_changes')->nullable(); // activitylog v5
            $table->json('properties')->nullable();
            $table->ulid('company_id')->nullable(); // tenant scope
            $table->string('batch_uuid')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'created_at']);
            $table->index(['company_id', 'subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};
