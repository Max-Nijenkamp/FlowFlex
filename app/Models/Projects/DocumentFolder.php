<?php

namespace App\Models\Projects;

use App\Concerns\BelongsToCompany;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DocumentFolder extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'parent_folder_id',
        'created_by_tenant_id',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'parent_folder_id'])
            ->logOnlyDirty();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(DocumentFolder::class, 'parent_folder_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(DocumentFolder::class, 'parent_folder_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'folder_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'created_by_tenant_id');
    }
}
