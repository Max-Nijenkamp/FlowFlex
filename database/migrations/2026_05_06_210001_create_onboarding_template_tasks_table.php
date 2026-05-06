<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onboarding_template_tasks', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->char('company_id', 26)->index();
            $table->char('template_id', 26)->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('task_type')->default('read_acknowledge');
            $table->string('default_assignee')->nullable();
            $table->integer('due_day_offset')->default(1);
            $table->boolean('is_required')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_template_tasks');
    }
};
