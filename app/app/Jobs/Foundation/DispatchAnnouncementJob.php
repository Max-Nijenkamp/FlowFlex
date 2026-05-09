<?php

declare(strict_types=1);

namespace App\Jobs\Foundation;

use App\Models\Company;
use App\Models\PlatformAnnouncement;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DispatchAnnouncementJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(private readonly string $announcementId) {}

    public function handle(): void
    {
        $announcement = PlatformAnnouncement::findOrFail($this->announcementId);

        $query = match ($announcement->target) {
            'company' => User::withoutGlobalScopes()
                ->where('company_id', $announcement->target_value)
                ->where('status', 'active'),
            default => User::withoutGlobalScopes()
                ->whereHas('company', fn ($q) => $q->where('status', 'active'))
                ->where('status', 'active'),
        };

        $query->chunk(200, function ($users) use ($announcement): void {
            foreach ($users as $user) {
                $user->notify(new \App\Notifications\Foundation\PlatformAnnouncementNotification($announcement));
            }
        });
    }
}
