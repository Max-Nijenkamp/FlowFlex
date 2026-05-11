<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_module_subscriptions', function (Blueprint $table): void {
            $table->index(
                ['company_id', 'module_key', 'status'],
                'cms_company_module_status',
            );
        });

        Schema::table('user_invitations', function (Blueprint $table): void {
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('company_module_subscriptions', function (Blueprint $table): void {
            $table->dropIndex('cms_company_module_status');
        });

        Schema::table('user_invitations', function (Blueprint $table): void {
            $table->dropIndex(['expires_at']);
        });
    }
};
