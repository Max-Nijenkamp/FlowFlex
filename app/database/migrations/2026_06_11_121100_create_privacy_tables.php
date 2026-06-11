<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dsar_requests', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('subject_email');
            $table->string('request_type'); // access / erasure
            $table->string('status')->default('received');
            $table->timestamp('due_at'); // created + 30 days
            $table->timestamp('completed_at')->nullable();
            $table->string('result_path')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes(); // compliance proof — rows themselves never purged

            $table->index('company_id');
        });

        Schema::create('consent_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('data_category');
            $table->timestamp('consented_at');
            $table->timestamp('withdrawn_at')->nullable();
            $table->timestamps();

            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consent_logs');
        Schema::dropIfExists('dsar_requests');
    }
};
