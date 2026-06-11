<?php

declare(strict_types=1);

namespace App\Actions;

use App\Support\Services\CompanyContext;
use Lorisleiva\Actions\Concerns\AsAction;

class CompleteSetupAction
{
    use AsAction;

    public function handle(): void
    {
        app(CompanyContext::class)->current()
            ->forceFill(['setup_completed_at' => now()])
            ->save();
    }
}
