<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['company_id', 'status']);
            $table->index('due_date');
            $table->index('issue_date');
        });

        Schema::table('deals', function (Blueprint $table) {
            $table->index(['company_id', 'status']);
            $table->index('pipeline_id');
            $table->index('deal_stage_id');
            $table->index('expected_close_date');
        });

        Schema::table('crm_contacts', function (Blueprint $table) {
            $table->index(['company_id', 'type']);
            $table->index('crm_company_id');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'priority']);
            $table->index('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'status']);
            $table->dropIndex(['due_date']);
            $table->dropIndex(['issue_date']);
        });

        Schema::table('deals', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'status']);
            $table->dropIndex(['pipeline_id']);
            $table->dropIndex(['deal_stage_id']);
            $table->dropIndex(['expected_close_date']);
        });

        Schema::table('crm_contacts', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'type']);
            $table->dropIndex(['crm_company_id']);
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'status']);
            $table->dropIndex(['company_id', 'priority']);
            $table->dropIndex(['assigned_to']);
        });
    }
};
