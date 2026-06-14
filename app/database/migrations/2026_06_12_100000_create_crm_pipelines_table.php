<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_pipelines', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->boolean('is_default')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
        });

        Schema::table('crm_pipeline_stages', function (Blueprint $table): void {
            // Nullable during backfill; every stage gets a pipeline below.
            $table->foreignUlid('pipeline_id')->nullable()->constrained('crm_pipelines')->cascadeOnDelete();
        });

        // Backfill: one default "Sales pipeline" per company that already has stages.
        $companyIds = DB::table('crm_pipeline_stages')->distinct()->pluck('company_id');

        foreach ($companyIds as $companyId) {
            $pipelineId = strtolower((string) Str::ulid());

            DB::table('crm_pipelines')->insert([
                'id' => $pipelineId,
                'company_id' => $companyId,
                'name' => 'Sales pipeline',
                'is_default' => true,
                'order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('crm_pipeline_stages')
                ->where('company_id', $companyId)
                ->update(['pipeline_id' => $pipelineId]);
        }
    }

    public function down(): void
    {
        Schema::table('crm_pipeline_stages', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('pipeline_id');
        });

        Schema::dropIfExists('crm_pipelines');
    }
};
