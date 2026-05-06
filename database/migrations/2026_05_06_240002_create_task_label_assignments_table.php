<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_label_assignments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('task_id', 26)->index();
            $table->char('label_id', 26)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_label_assignments');
    }
};
