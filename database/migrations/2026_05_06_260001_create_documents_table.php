<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->char('company_id', 26)->index();
            $table->char('folder_id', 26)->nullable()->index();
            $table->char('current_file_id', 26)->nullable()->index();
            $table->string('title');
            $table->string('original_filename');
            $table->string('mime_type')->nullable();
            $table->integer('file_size_bytes')->nullable();
            $table->integer('version_number')->default(1);
            $table->char('uploaded_by_tenant_id', 26)->nullable()->index();
            $table->boolean('is_starred')->default(false);
            $table->json('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
