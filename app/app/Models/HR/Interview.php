<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'hr_interviews';

    protected $fillable = ['company_id', 'applicant_id', 'scheduled_at', 'interviewers', 'type', 'outcome', 'notes'];

    protected function casts(): array
    {
        return ['scheduled_at' => 'datetime', 'interviewers' => 'array'];
    }
}
