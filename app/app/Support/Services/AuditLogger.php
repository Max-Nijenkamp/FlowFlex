<?php

declare(strict_types=1);

namespace App\Support\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Single entry point for all audit records — no domain writes to activity_log
 * directly. company_id is force-set from CompanyContext by the Activity model's
 * BelongsToCompany creating hook. PII values are stripped against the denylist.
 */
class AuditLogger
{
    /** @var list<string> PII keys whose values never enter the log. */
    private const array PII_DENYLIST = [
        'password',
        'national_id',
        'date_of_birth',
        'iban',
        'bic',
        'salary',
        'salary_raw',
        'salary_cents',
        'token',
        'secret',
        'api_key',
        'api_secret',
        'webhook_secret',
        'stripe_customer_id',
    ];

    /** @param array<string, mixed> $properties */
    public static function log(string $event, ?Model $subject, ?User $causer, array $properties = []): void
    {
        $logger = activity(explode('.', $event)[0])
            ->event($event)
            ->withProperties([
                ...self::stripPii($properties),
                'ip' => request()->ip(),
            ]);

        if ($subject !== null) {
            $logger->performedOn($subject);
        }

        if ($causer !== null) {
            $logger->causedBy($causer);
        }

        $logger->log($event);
    }

    /**
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    private static function stripPii(array $properties): array
    {
        foreach ($properties as $key => $value) {
            if (in_array(strtolower((string) $key), self::PII_DENYLIST, true)) {
                $properties[$key] = '[redacted]';

                continue;
            }

            if (is_array($value)) {
                $properties[$key] = self::stripPii($value);
            }
        }

        return $properties;
    }
}
