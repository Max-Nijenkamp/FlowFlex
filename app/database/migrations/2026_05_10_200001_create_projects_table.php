<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();

            $table->string('name');
            $table->text('description')->nullable();

            $table->enum('status', ['planning', 'active', 'on_hold', 'completed', 'cancelled'])
                ->default('planning');

            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])
                ->default('medium');

            $table->ulid('owner_id');
            $table->foreign('owner_id')->references('id')->on('users')->cascadeOnDelete();

            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->decimal('budget', 12, 2)->nullable();
            $table->string('color')->nullable();
            $table->boolean('is_template')->default(false);

            $table->ulid('template_id')->nullable();

            $table->json('custom_fields')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
            $table->index('status');
            $table->index('owner_id');
        });

        // Add self-referential FK after table is created so the primary key exists
        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('template_id')->references('id')->on('projects')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
