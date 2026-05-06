<?php

namespace App\Models;

use App\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class File extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'uploaded_by_tenant_id',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'collection',
        'model_type',
        'model_id',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['original_name', 'collection', 'model_type', 'model_id'])
            ->logOnlyDirty();
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'uploaded_by_tenant_id');
    }

    public function url(int $minutes = 60): string
    {
        try {
            return Storage::disk($this->disk)->temporaryUrl(
                $this->path,
                now()->addMinutes($minutes)
            );
        } catch (\RuntimeException) {
            // Local disk does not support temporary URLs — fall back to a regular URL.
            return Storage::disk($this->disk)->url($this->path);
        }
    }

    public function humanSize(): string
    {
        $bytes = $this->size;

        if ($bytes >= 1_073_741_824) {
            return number_format($bytes / 1_073_741_824, 2) . ' GB';
        }

        if ($bytes >= 1_048_576) {
            return number_format($bytes / 1_048_576, 2) . ' MB';
        }

        if ($bytes >= 1_024) {
            return number_format($bytes / 1_024, 2) . ' KB';
        }

        return $bytes . ' B';
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    public function scopeInCollection($query, string $collection)
    {
        return $query->where('collection', $collection);
    }

    public function scopeForModel($query, Model $model)
    {
        return $query
            ->where('model_type', get_class($model))
            ->where('model_id', $model->getKey());
    }
}
