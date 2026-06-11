<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use BelongsToCompany, HasFactory, HasUlids, SoftDeletes;

    protected $table = 'crm_accounts';

    protected $fillable = ['company_id', 'name', 'industry', 'employee_count', 'website', 'phone', 'owner_id', 'lifetime_value_cents', 'custom_fields'];

    protected function casts(): array
    {
        return ['custom_fields' => 'array', 'lifetime_value_cents' => 'integer', 'employee_count' => 'integer'];
    }
}
