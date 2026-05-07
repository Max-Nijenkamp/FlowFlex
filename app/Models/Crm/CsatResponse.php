<?php

namespace App\Models\Crm;

use App\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class CsatResponse extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity;

    protected $fillable = [
        'company_id',
        'csat_survey_id',
        'rating',
        'comment',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['csat_survey_id', 'rating', 'responded_at'])
            ->logOnlyDirty();
    }

    public function survey(): BelongsTo
    {
        return $this->belongsTo(CsatSurvey::class, 'csat_survey_id');
    }
}
