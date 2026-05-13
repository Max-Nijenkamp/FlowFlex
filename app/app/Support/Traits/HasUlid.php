<?php

declare(strict_types=1);

namespace App\Support\Traits;

use Illuminate\Support\Str;

trait HasUlid
{
    public static function bootHasUlid(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::ulid();
            }
        });
    }

    public function initializeHasUlid(): void
    {
        $this->incrementing = false;
        $this->keyType = 'string';
    }
}
