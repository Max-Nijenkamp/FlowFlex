<?php

namespace App\Models\Crm;

use App\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ChatbotRule extends Model
{
    use BelongsToCompany, HasUlids, LogsActivity, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'trigger_keywords',
        'response_body',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'trigger_keywords' => 'array',
            'is_active'        => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'trigger_keywords', 'is_active', 'sort_order'])
            ->logOnlyDirty();
    }
}
