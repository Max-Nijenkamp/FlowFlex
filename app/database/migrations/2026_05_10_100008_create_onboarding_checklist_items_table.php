<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboarding_checklist_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();

            $table->ulid('checklist_id');
            $table->foreign('checklist_id')->references('id')->on('onboarding_checklists')->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->ulid('assignee_id')->nullable();
            $table->foreign('assignee_id')->references('id')->on('users')->nullOnDelete();

            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('is_required')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index('company_id');
            $table->index('checklist_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_checklist_items');
    }
};
