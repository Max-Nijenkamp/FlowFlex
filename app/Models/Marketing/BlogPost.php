<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'blog_category_id',
    'title',
    'slug',
    'excerpt',
    'featured_image',
    'body',
    'author_id',
    'tags',
    'status',
    'published_at',
    'seo_title',
    'seo_description',
    'og_image',
    'seo_noindex',
    'reading_time',
    'cta_type',
    'cta_module',
])]
class BlogPost extends Model
{
    use HasUlids, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'slug', 'status', 'published_at', 'blog_category_id'])
            ->logOnlyDirty();
    }

    protected function casts(): array
    {
        return [
            'tags'         => 'array',
            'published_at' => 'datetime',
            'seo_noindex'  => 'boolean',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (BlogPost $post): void {
            if ($post->body) {
                $wordCount = str_word_count(strip_tags($post->body));
                $post->reading_time = (int) max(1, ceil($wordCount / 200));
            }
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }
}
