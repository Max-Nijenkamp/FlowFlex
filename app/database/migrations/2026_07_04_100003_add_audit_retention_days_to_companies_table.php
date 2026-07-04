<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            // Per-company audit retention (data-lifecycle); null = platform
            // default (730 days). Editable via core.company-settings later.
            $table->unsignedSmallInteger('audit_retention_days')->nullable()->after('setup_completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->dropColumn('audit_retention_days');
        });
    }
};
