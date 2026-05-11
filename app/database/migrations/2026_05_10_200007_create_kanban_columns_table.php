<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kanban_columns', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();

            $table->ulid('board_id');
            $table->foreign('board_id')->references('id')->on('kanban_boards')->cascadeOnDelete();

            $table->string('name');
            $table->string('color')->nullable();
            $table->integer('wip_limit')->nullable();
            $table->string('maps_to_status')->nullable();
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->unique(['board_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kanban_columns');
    }
};
