<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->ulid('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();

            $table->ulid('project_id')->nullable();
            $table->foreign('project_id')->references('id')->on('projects')->nullOnDelete();

            $table->ulid('task_id')->nullable();
            $table->foreign('task_id')->references('id')->on('tasks')->nullOnDelete();

            $table->ulid('uploaded_by');
            $table->foreign('uploaded_by')->references('id')->on('users')->cascadeOnDelete();

            $table->string('name');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
