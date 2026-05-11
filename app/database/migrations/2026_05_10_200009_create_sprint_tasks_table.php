<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sprint_tasks', function (Blueprint $table) {
            $table->ulid('sprint_id');
            $table->foreign('sprint_id')->references('id')->on('sprints')->cascadeOnDelete();

            $table->ulid('task_id');
            $table->foreign('task_id')->references('id')->on('tasks')->cascadeOnDelete();

            $table->timestamps();

            $table->primary(['sprint_id', 'task_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sprint_tasks');
    }
};
