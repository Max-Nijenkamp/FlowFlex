<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('timesheets', function (Blueprint $table) {
            $table->unique(['tenant_id', 'week_start_date'], 'timesheets_tenant_id_week_start_date_unique');
        });
    }

    public function down(): void
    {
        Schema::table('timesheets', function (Blueprint $table) {
            $table->dropUnique('timesheets_tenant_id_week_start_date_unique');
        });
    }
};
