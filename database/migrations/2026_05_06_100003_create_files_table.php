<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->char('company_id', 26)->index();
            $table->char('uploaded_by_tenant_id', 26)->nullable();
            $table->string('disk')->default('s3');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size');
            $table->string('collection', 50)->nullable();
            $table->string('model_type')->nullable();
            $table->char('model_id', 26)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['model_type', 'model_id']);
            $table->index(['company_id', 'collection']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
