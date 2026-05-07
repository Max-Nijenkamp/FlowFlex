<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_entities', function (Blueprint $table) {
            $table->renameColumn('tax_reference', 'tax_reference_encrypted');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_entities', function (Blueprint $table) {
            $table->renameColumn('tax_reference_encrypted', 'tax_reference');
        });
    }
};
