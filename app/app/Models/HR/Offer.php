<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Support\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use BelongsToCompany, HasUlids;

    protected $table = 'hr_offers';

    protected $fillable = ['company_id', 'applicant_id', 'salary_raw', 'currency', 'start_date', 'status', 'sent_at', 'accepted_at'];

    /** @var list<string> */
    protected $hidden = ['salary_raw'];

    protected function casts(): array
    {
        return ['salary_raw' => 'encrypted', 'start_date' => 'date', 'sent_at' => 'datetime', 'accepted_at' => 'datetime'];
    }
}
