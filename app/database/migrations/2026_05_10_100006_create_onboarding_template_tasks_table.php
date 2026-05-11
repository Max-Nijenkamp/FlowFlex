<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboarding_template_tasks', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();

            $table->ulid('template_id');
            $table->foreign('template_id')->references('id')->on('onboarding_templates')->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('assignee_role')->nullable();
            $table->integer('due_days_after_hire')->default(1);
            $table->boolean('is_required')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index('company_id');
            $table->index('template_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_template_tasks');
    }
};
