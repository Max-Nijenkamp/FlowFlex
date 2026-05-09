<?php

declare(strict_types=1);

namespace App\Events\Foundation;

use App\Models\Company;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Company $company,
        public readonly User $owner,
    ) {}
}
