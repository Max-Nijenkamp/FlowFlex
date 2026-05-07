<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'question',
    'answer',
    'context',
    'display_order',
    'is_published',
])]
class FaqEntry extends Model
{
    use HasUlids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function scopeForContext(Builder $query, string $context): Builder
    {
        return $query->where('context', $context);
    }
}
