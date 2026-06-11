<?php

declare(strict_types=1);

namespace App\Support\Services;

use Illuminate\Database\Eloquent\Model;

/**
 * Single source for tenant-scoped storage paths. Raw Storage::put() with a
 * hand-built path is forbidden — always go through here (or Media Library,
 * which uses CompanyPathGenerator).
 */
class FileStorageService
{
    public function pathFor(Model $model, string $filename): string
    {
        $companyId = $model->company_id ?? app(CompanyContext::class)->current()->id;

        return "companies/{$companyId}/{$model->getTable()}/{$model->getKey()}/{$filename}";
    }
}
