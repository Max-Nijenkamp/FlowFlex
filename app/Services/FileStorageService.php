<?php

namespace App\Services;

use App\Models\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileStorageService
{
    public function store(
        UploadedFile $file,
        string $collection = 'default',
        ?string $disk = null,
        ?Model $model = null
    ): File {
        $disk        = $disk ?? config('filesystems.default', 'local');
        $companyId   = $this->resolveCompanyId();
        $companySlug = $this->resolveCompanySlug();

        $path = $file->store(
            "companies/{$companySlug}/{$collection}",
            $disk
        );

        return File::create([
            'company_id'             => $companyId,
            'uploaded_by_tenant_id'  => auth('tenant')->id(),
            'disk'                   => $disk,
            'path'                   => $path,
            'original_name'          => $file->getClientOriginalName(),
            'mime_type'              => $file->getMimeType(),
            'size'                   => $file->getSize(),
            'collection'             => $collection,
            'model_type'             => $model ? get_class($model) : null,
            'model_id'               => $model?->getKey(),
        ]);
    }

    /**
     * Resolve the company ID from the active authentication context.
     *
     * Supports three contexts:
     *  - Filament / HTTP (tenant guard active)
     *  - API (AuthenticateApiKey middleware sets api_company on the request)
     *  - Job / console (returns null; callers must set company_id explicitly)
     */
    private function resolveCompanyId(): ?string
    {
        if (auth('tenant')->check()) {
            return auth('tenant')->user()->company_id;
        }

        $apiCompany = request()->attributes->get('api_company');

        return $apiCompany?->id ?? null;
    }

    /**
     * Resolve a slug-safe company identifier for use in storage paths.
     */
    private function resolveCompanySlug(): string
    {
        if (auth('tenant')->check()) {
            return auth('tenant')->user()->company?->slug ?? 'general';
        }

        $apiCompany = request()->attributes->get('api_company');

        return $apiCompany?->slug ?? 'general';
    }

    public function delete(File $file): bool
    {
        Storage::disk($file->disk)->delete($file->path);

        return $file->delete();
    }

    public function temporaryUrl(File $file, int $minutes = 60): string
    {
        try {
            return Storage::disk($file->disk)->temporaryUrl(
                $file->path,
                now()->addMinutes($minutes)
            );
        } catch (\RuntimeException) {
            return Storage::disk($file->disk)->url($file->path);
        }
    }
}
