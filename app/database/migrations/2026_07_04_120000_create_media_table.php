<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Spatie media-library shape with FlowFlex conventions: ULID keys,
        // ULID morphs, tenant column, soft deletes.
        Schema::create('media', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('model_type');
            $table->ulid('model_id');
            $table->uuid()->nullable()->unique();
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->string('conversions_disk')->nullable();
            $table->unsignedBigInteger('size');
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('generated_conversions');
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable()->index();
            $table->foreignUlid('company_id')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['model_type', 'model_id']);
            $table->index(['company_id', 'collection_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
