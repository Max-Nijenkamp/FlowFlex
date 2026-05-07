<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name',
    'slug',
    'description',
    'display_order',
    'is_published',
])]
class BlogCategory extends Model
{
    use HasUlids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'blog_category_id');
    }
}
