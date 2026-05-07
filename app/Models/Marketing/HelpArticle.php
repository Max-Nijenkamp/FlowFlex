<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'help_category_id',
    'title',
    'slug',
    'body',
    'seo_title',
    'seo_description',
    'is_published',
    'last_reviewed_at',
    'helpful_count',
    'not_helpful_count',
    'module_link',
])]
class HelpArticle extends Model
{
    use HasUlids, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'slug', 'help_category_id', 'is_published', 'last_reviewed_at'])
            ->logOnlyDirty();
    }

    protected function casts(): array
    {
        return [
            'is_published'     => 'boolean',
            'last_reviewed_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(HelpCategory::class, 'help_category_id');
    }
}
