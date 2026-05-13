<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Traits\HasUlid;
use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    /** @use HasFactory<CompanyFactory> */
    use HasFactory;
    use HasUlid;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'timezone',
        'locale',
        'currency',
        'logo_path',
        'favicon_path',
        'primary_color',
        'status',
    ];

    protected $attributes = [
        'timezone' => 'UTC',
        'locale' => 'en',
        'currency' => 'EUR',
        'status' => 'trial',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
