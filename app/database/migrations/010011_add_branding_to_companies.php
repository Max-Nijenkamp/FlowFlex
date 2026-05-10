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
            $table->string('logo_path')->nullable()->after('slug');
            $table->string('favicon_path')->nullable()->after('logo_path');
            $table->string('primary_color')->nullable()->after('favicon_path');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->dropColumn(['logo_path', 'favicon_path', 'primary_color']);
        });
    }
};
