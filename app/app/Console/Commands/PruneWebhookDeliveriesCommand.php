<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\WebhookDelivery;
use Illuminate\Console\Command;

class PruneWebhookDeliveriesCommand extends Command
{
    protected $signature = 'flowflex:prune-webhook-deliveries {--days=30}';

    protected $description = 'Delete webhook delivery logs older than the retention window (idempotent)';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));

        $deleted = WebhookDelivery::query()->withoutGlobalScopes()
            ->where('created_at', '<', now()->subDays($days))
            ->delete();

        $this->info("Pruned {$deleted} webhook deliveries older than {$days} days.");

        return self::SUCCESS;
    }
}
