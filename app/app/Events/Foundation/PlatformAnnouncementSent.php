<?php

declare(strict_types=1);

namespace App\Events\Foundation;

use App\Models\PlatformAnnouncement;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlatformAnnouncementSent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly PlatformAnnouncement $announcement,
    ) {}
}
