<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_dependencies', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();

            $table->ulid('task_id');
            $table->foreign('task_id')->references('id')->on('tasks')->cascadeOnDelete();

            $table->ulid('depends_on_task_id');
            $table->foreign('depends_on_task_id')->references('id')->on('tasks')->cascadeOnDelete();

            $table->enum('dependency_type', ['finish_to_start', 'start_to_start', 'finish_to_finish'])
                ->default('finish_to_start');

            $table->timestamps();

            $table->unique(['task_id', 'depends_on_task_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_dependencies');
    }
};
