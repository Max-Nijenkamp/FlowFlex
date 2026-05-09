<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_module_subscriptions', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('module_catalog', function (Blueprint $table) {
            $table->index('domain');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('company_module_subscriptions', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('module_catalog', function (Blueprint $table) {
            $table->dropIndex(['domain']);
            $table->dropIndex(['is_active']);
        });
    }
};
