<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate existing country-code locale values to language codes
        DB::table('companies')->where('locale', 'NL')->update(['locale' => 'nl']);
        DB::table('companies')->where('locale', 'GB')->update(['locale' => 'en']);

        // Any remaining unmapped values default to English
        DB::table('companies')
            ->whereNotIn('locale', ['en', 'nl', 'de', 'fr', 'es'])
            ->update(['locale' => 'en']);

        Schema::table('companies', function (Blueprint $table) {
            $table->string('locale')->default('en')->change();
        });
    }

    public function down(): void
    {
        DB::table('companies')->where('locale', 'nl')->update(['locale' => 'NL']);
        DB::table('companies')->where('locale', 'en')->update(['locale' => 'GB']);

        Schema::table('companies', function (Blueprint $table) {
            $table->string('locale')->default('NL')->change();
        });
    }
};
