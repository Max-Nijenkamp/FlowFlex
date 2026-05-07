<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Health\ResultStores\ResultStore;

class StatusController extends Controller
{
    public function __invoke(ResultStore $store): Response
    {
        $results = $store->latestResults();

        $checks = [];
        $allOk = true;
        $anyDegraded = false;

        if ($results) {
            foreach ($results->storedCheckResults as $result) {
                $status = match ($result->status) {
                    'ok' => 'operational',
                    'warning' => 'degraded',
                    'failed', 'crashed' => 'outage',
                    'skipped' => 'maintenance',
                    default => 'unknown',
                };

                if ($status === 'degraded') {
                    $anyDegraded = true;
                    $allOk = false;
                }
                if ($status === 'outage') {
                    $allOk = false;
                    $anyDegraded = false;
                }

                $checks[] = [
                    'name' => $result->name,
                    'label' => $result->label ?: $this->labelFor($result->name),
                    'status' => $status,
                    'summary' => $result->shortSummary ?: '',
                ];
            }
        }

        $overall = match (true) {
            $checks === [] => 'unknown',
            $allOk => 'operational',
            $anyDegraded => 'degraded',
            default => 'outage',
        };

        return Inertia::render('Marketing/Status', [
            'checks' => $checks,
            'overall' => $overall,
            'last_checked_at' => $results?->finishedAt->format('Y-m-d\TH:i:s\Z'),
        ]);
    }

    private function labelFor(string $name): string
    {
        return match ($name) {
            'Database' => 'Database',
            'Cache' => 'Cache',
            'Redis' => 'Redis',
            'UsedDiskSpace' => 'Disk Space',
            'Environment' => 'Environment',
            default => $name,
        };
    }
}
