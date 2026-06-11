<?php

declare(strict_types=1);

namespace App\Models\CRM;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'crm_availability';

    protected $fillable = ['company_id', 'user_id', 'working_hours', 'calendar_connection'];

    protected function casts(): array
    {
        return ['working_hours' => 'array', 'calendar_connection' => 'encrypted'];
    }
}
