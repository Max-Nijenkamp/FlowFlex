<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table): void {
            // Tenant column per core.notifications spec - notifiable is the
            // user, but cross-checks and admin views key on company_id.
            $table->foreignUlid('company_id')->nullable()->index()->after('data');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table): void {
            $table->dropColumn('company_id');
        });
    }
};
