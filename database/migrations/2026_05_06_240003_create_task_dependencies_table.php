<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_dependencies', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->char('company_id', 26)->index();
            $table->char('task_id', 26)->index();
            $table->char('depends_on_task_id', 26)->index();
            $table->string('dependency_type')->default('blocks');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_dependencies');
    }
};
