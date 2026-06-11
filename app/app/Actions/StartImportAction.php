<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\CreateImportData;
use App\Jobs\ProcessImportJob;
use App\Models\DataImport;
use App\Support\Import\ImporterRegistry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class StartImportAction
{
    use AsAction;

    public function handle(CreateImportData $data): DataImport
    {
        $registry = app(ImporterRegistry::class);

        if (! array_key_exists($data->target, $registry->available())) {
            throw ValidationException::withMessages(['target' => 'Unknown import target or module not active.']);
        }

        $missing = array_diff(
            $registry->resolve($data->target)->requiredFields(),
            array_values($data->column_map),
        );

        if ($missing !== []) {
            throw ValidationException::withMessages([
                'column_map' => 'Required fields not mapped: '.implode(', ', $missing),
            ]);
        }

        $import = DataImport::create([
            'target' => $data->target,
            'filename' => $data->filename,
            'column_map' => $data->column_map,
            'stored_path' => $data->stored_path,
            'imported_by' => Auth::guard('web')->id(),
        ]);

        ProcessImportJob::dispatch($import->id);

        return $import;
    }
}
