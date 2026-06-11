<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class ShiftSwapRequest extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'hr_shift_swap_requests';

    protected $fillable = ['company_id', 'requester_id', 'recipient_id', 'shift_id', 'status', 'manager_approved_at'];

    protected function casts(): array
    {
        return ['manager_approved_at' => 'datetime'];
    }
}
