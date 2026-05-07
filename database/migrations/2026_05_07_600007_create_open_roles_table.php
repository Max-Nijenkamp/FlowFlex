<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('open_roles', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('department');
            $table->string('location');
            $table->string('type');
            $table->string('salary_range')->nullable();
            $table->text('about_role');
            $table->longText('responsibilities');
            $table->longText('requirements');
            $table->longText('nice_to_have')->nullable();
            $table->longText('how_to_apply');
            $table->string('status')->default('open');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('open_roles');
    }
};
