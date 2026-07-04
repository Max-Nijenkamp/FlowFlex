<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
 * Tenant-scoped notification channel (core.notifications/realtime-broadcast):
 * a subscriber must belong to the channel's company.
 *
 * Broadcast::channel() resolves the default broadcaster, and the reverb
 * driver refuses to construct without its app credentials — guard so a
 * cred-less environment (CI, fresh clone) still boots; realtime auth is
 * simply absent until REVERB_APP_* are set.
 */
try {
    Broadcast::channel('company.{companyId}.notifications', function (User $user, string $companyId): bool {
        return $user->company_id === $companyId;
    });
} catch (Throwable) {
    report_if(app()->isProduction(), new RuntimeException('Broadcast channels not registered — broadcaster failed to construct.'));
}
