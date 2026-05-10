<?php

declare(strict_types=1);

namespace App\Models\Core;

use App\Models\User;
use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApiClient extends Model
{
    use BelongsToCompany;
    use HasFactory;
    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'created_by',
        'name',
        'client_id',
        'client_secret',
        'scopes',
        'allowed_ips',
        'is_active',
        'last_used_at',
    ];

    protected $hidden = ['client_secret'];

    protected $attributes = ['is_active' => true];

    protected $casts = [
        'scopes'      => 'array',
        'allowed_ips' => 'array',
        'is_active'   => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(ApiToken::class);
    }
}
