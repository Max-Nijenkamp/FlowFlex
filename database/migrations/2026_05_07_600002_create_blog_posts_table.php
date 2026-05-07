<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('blog_category_id', 26);
            $table->foreign('blog_category_id')->references('id')->on('blog_categories')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->string('featured_image')->nullable();
            $table->longText('body');
            $table->string('author_id')->nullable();
            $table->json('tags')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('og_image')->nullable();
            $table->boolean('seo_noindex')->default(false);
            $table->integer('reading_time')->nullable();
            $table->string('cta_type')->default('demo');
            $table->string('cta_module')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
