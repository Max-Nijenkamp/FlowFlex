<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_announcements', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('title');
            $table->text('body');
            $table->string('target')->default('all'); // all|company
            $table->string('target_value')->nullable();
            $table->foreignUlid('created_by')->references('id')->on('admins');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_announcements');
    }
};
