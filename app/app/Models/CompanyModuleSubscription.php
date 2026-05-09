<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyModuleSubscription extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'company_id',
        'module_key',
        'status',
        'settings',
        'activated_at',
        'deactivated_at',
    ];

    protected $casts = [
        'settings'       => 'array',
        'activated_at'   => 'datetime',
        'deactivated_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(ModuleCatalog::class, 'module_key', 'module_key');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function activate(): void
    {
        $this->update([
            'status'       => 'active',
            'activated_at' => now(),
        ]);
    }

    public function deactivate(): void
    {
        $this->update([
            'status'         => 'inactive',
            'deactivated_at' => now(),
        ]);
    }
}
