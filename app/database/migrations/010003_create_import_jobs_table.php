<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_jobs', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUlid('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('entity_type'); // users, employees, contacts, etc.
            $table->string('status')->default('pending'); // pending | mapping | validating | importing | done | failed | rolled_back
            $table->string('duplicate_strategy')->default('skip'); // skip | update | error
            $table->integer('total_rows')->default(0);
            $table->integer('imported_rows')->default(0);
            $table->integer('skipped_rows')->default(0);
            $table->integer('failed_rows')->default(0);
            $table->json('column_mapping')->nullable();
            $table->string('file_path')->nullable();
            $table->string('error_log_path')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'entity_type']);
        });

        Schema::create('import_job_rows', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('import_job_id')->constrained('import_jobs')->cascadeOnDelete();
            $table->integer('row_number');
            $table->string('status')->default('pending'); // pending | imported | skipped | failed
            $table->json('raw_data');
            $table->json('mapped_data')->nullable();
            $table->json('errors')->nullable();
            $table->timestamps();

            $table->index(['import_job_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_job_rows');
        Schema::dropIfExists('import_jobs');
    }
};
