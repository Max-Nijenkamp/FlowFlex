<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('platform_announcements', function (Blueprint $table): void {
            $table->index('sent_at');
            $table->index('target');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::table('platform_announcements', function (Blueprint $table): void {
            $table->dropIndex(['sent_at']);
            $table->dropIndex(['target']);
            $table->dropIndex(['created_by']);
        });
    }
};
