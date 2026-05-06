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
        $company     = auth('tenant')->user()?->company;
        $companySlug = $company?->slug ?? 'general';

        $path = $file->store(
            "companies/{$companySlug}/{$collection}",
            $disk
        );

        return File::create([
            'company_id'             => $company?->id,
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
