<?php

namespace App\Models\Projects;

use App\Concerns\BelongsToCompany;
use App\Models\File;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class DocumentVersion extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'document_id',
        'file_id',
        'version_number',
        'uploaded_by_tenant_id',
        'change_notes',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['document_id', 'version_number', 'change_notes'])
            ->logOnlyDirty();
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'uploaded_by_tenant_id');
    }
}
