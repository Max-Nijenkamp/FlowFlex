<?php

declare(strict_types=1);

namespace App\Services\Core;

use App\Models\Core\ImportJob;
use App\Models\Core\ImportJobRow;
use App\Support\Services\CompanyContext;
use Illuminate\Support\Facades\DB;

class DataImportService
{
    public function __construct(private readonly CompanyContext $companyContext) {}

    public function createJob(string $entityType, string $filePath, string $duplicateStrategy = 'skip'): ImportJob
    {
        $company = $this->companyContext->current();

        return ImportJob::create([
            'company_id'         => $company->id,
            'created_by'         => auth()->id(),
            'entity_type'        => $entityType,
            'file_path'          => $filePath,
            'duplicate_strategy' => $duplicateStrategy,
            'status'             => 'pending',
        ]);
    }

    public function parseAndStoreRows(ImportJob $job, array $rows): void
    {
        $job->update([
            'total_rows' => count($rows),
            'status'     => 'mapping',
        ]);

        $offset = 0;
        foreach (array_chunk($rows, 100) as $chunk) {
            $inserts = [];
            foreach ($chunk as $index => $row) {
                $inserts[] = [
                    'id'            => \Illuminate\Support\Str::ulid()->toString(),
                    'import_job_id' => $job->id,
                    'row_number'    => $offset + $index + 1,
                    'status'        => 'pending',
                    'raw_data'      => json_encode($row),
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }
            DB::table('import_job_rows')->insert($inserts);
            $offset += count($chunk);
        }
    }

    public function validate(ImportJob $job, array $columnMapping, callable $validator): int
    {
        $job->update([
            'column_mapping' => $columnMapping,
            'status'         => 'validating',
        ]);

        $failedCount = 0;

        $job->rows()->chunk(200, function ($rows) use ($validator, &$failedCount): void {
            foreach ($rows as $row) {
                $mapped = $this->applyMapping($row->raw_data, $job->column_mapping ?? []);
                $errors = $validator($mapped);

                if (! empty($errors)) {
                    $row->update(['status' => 'failed', 'mapped_data' => $mapped, 'errors' => $errors]);
                    $failedCount++;
                } else {
                    $row->update(['status' => 'pending', 'mapped_data' => $mapped]);
                }
            }
        });

        return $failedCount;
    }

    public function rollback(ImportJob $job): void
    {
        $job->update(['status' => 'rolled_back']);
        // Domain-specific rollback is handled by each domain's importer
    }

    private function applyMapping(array $rawData, array $columnMapping): array
    {
        if (empty($columnMapping)) {
            return $rawData;
        }

        $mapped = [];
        foreach ($columnMapping as $sourceCol => $targetField) {
            if (isset($rawData[$sourceCol])) {
                $mapped[$targetField] = $rawData[$sourceCol];
            }
        }

        return $mapped;
    }
}
