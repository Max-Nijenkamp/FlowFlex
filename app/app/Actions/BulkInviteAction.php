<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\CreateInvitationData;
use App\Models\User;
use App\Support\Services\AuditLogger;
use App\Support\Services\CompanyContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Bulk invite from pasted CSV rows (core.invitations, gap-feature-core-
 * bulk-invite): `email` or `email,role` per line, header rows skipped.
 * Each row goes through SendInvitationAction so every existing guard
 * (owner role, unknown role, duplicates) applies per row; one bad row
 * never blocks the rest.
 */
class BulkInviteAction
{
    use AsAction;

    /** @return array{sent: int, failures: list<string>} */
    public function handle(string $rows, string $defaultRole): array
    {
        $sent = 0;
        $failures = [];

        foreach (preg_split('/\r\n|\r|\n/', trim($rows)) ?: [] as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            [$email, $role] = array_pad(array_map(trim(...), explode(',', $line, 2)), 2, null);

            if ($email === null || strcasecmp($email, 'email') === 0) {
                continue; // CSV header row
            }

            $role = ($role === null || $role === '') ? $defaultRole : $role;

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $failures[] = "{$email}: not a valid email address";

                continue;
            }

            try {
                SendInvitationAction::run(new CreateInvitationData(email: $email, role: $role));
                $sent++;
            } catch (ValidationException $e) {
                $message = collect($e->errors())->flatten()->first() ?? 'rejected';
                $failures[] = "{$email}: {$message}";
            }
        }

        if ($sent > 0) {
            $causer = Auth::user();
            app(AuditLogger::class)->log(
                'core.invitations-bulk-sent',
                app(CompanyContext::class)->current(),
                $causer instanceof User ? $causer : null,
                ['sent' => $sent, 'failed' => count($failures)],
            );
        }

        return ['sent' => $sent, 'failures' => $failures];
    }
}
