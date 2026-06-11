<?php

declare(strict_types=1);

namespace App\Jobs\Core;

use App\Models\Core\DataImport;
use App\States\Core\DataImport\Complete;
use App\States\Core\DataImport\Failed;
use App\States\Core\DataImport\Processing;
use App\Support\Import\ImporterRegistry;
use App\Support\Jobs\Middleware\WithCompanyContext;
use App\Support\Services\CompanyContext;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProcessImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ?string $company_id = null;

    public function __construct(
        public readonly string $importId,
    ) {
        $this->onQueue('imports');

        $import = DataImport::query()->withoutGlobalScopes()->find($importId);
        $this->company_id = $import?->company_id;
    }

    /** @return list<object> */
    public function middleware(): array
    {
        return [new WithCompanyContext];
    }

    public function handle(): void
    {
        $import = DataImport::query()->withoutGlobalScopes()->findOrFail($this->importId);
        $registry = app(ImporterRegistry::class);

        $import->status->transitionTo(Processing::class);

        try {
            $importer = $registry->resolve($import->target);
            $rows = $this->readRows($import);
            $errors = [];
            $success = 0;

            foreach ($rows as $index => $sourceRow) {
                $mapped = [];
                foreach ($import->column_map as $sourceColumn => $field) {
                    $mapped[$field] = $sourceRow[$sourceColumn] ?? null;
                }

                try {
                    $importer->importRow($mapped);
                    $success++;
                } catch (Throwable $e) {
                    // Row failures never abort the import — they land in the report.
                    $errors[] = ['row' => $index + 1, 'error' => $e->getMessage()];
                }
            }

            $errorPath = null;
            if ($errors !== []) {
                $companyId = app(CompanyContext::class)->currentId() ?? $import->company_id;
                $errorPath = "companies/{$companyId}/data_imports/{$import->id}/error-report.csv";
                $csv = "row,error\n".implode("\n", array_map(
                    fn (array $e) => "{$e['row']},\"".str_replace('"', '""', $e['error']).'"',
                    $errors,
                ));
                Storage::put($errorPath, $csv);
            }

            $import->forceFill([
                'total_rows' => count($rows),
                'success_rows' => $success,
                'error_rows' => count($errors),
                'error_report_path' => $errorPath,
            ])->save();

            $import->status->transitionTo(Complete::class);
        } catch (Throwable $e) {
            // Infrastructure failure (not row errors).
            $import->status->transitionTo(Failed::class);

            throw $e;
        }
    }

    /** @return list<array<string, mixed>> */
    private function readRows(DataImport $import): array
    {
        $content = Storage::get($import->stored_path);
        $lines = array_values(array_filter(array_map('trim', explode("\n", $content)), fn ($l) => $l !== ''));

        if ($lines === []) {
            return [];
        }

        $header = str_getcsv(array_shift($lines));

        return array_map(
            fn (string $line) => array_combine($header, array_pad(str_getcsv($line), count($header), null)),
            $lines,
        );
    }
}
