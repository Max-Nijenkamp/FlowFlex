<?php

namespace App\Models\Projects;

use App\Concerns\BelongsToCompany;
use App\Models\File;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Document extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'folder_id',
        'current_file_id',
        'title',
        'original_filename',
        'mime_type',
        'file_size_bytes',
        'version_number',
        'uploaded_by_tenant_id',
        'is_starred',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'tags'       => 'array',
            'is_starred' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'folder_id', 'version_number', 'is_starred', 'tags'])
            ->logOnlyDirty();
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(DocumentFolder::class, 'folder_id');
    }

    public function currentFile(): BelongsTo
    {
        return $this->belongsTo(File::class, 'current_file_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function shares(): HasMany
    {
        return $this->hasMany(DocumentShare::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'uploaded_by_tenant_id');
    }
}
