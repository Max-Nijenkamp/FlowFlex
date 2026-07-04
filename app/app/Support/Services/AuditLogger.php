<?php

declare(strict_types=1);

namespace App\Support\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Single audit entry point (core.audit-log): no domain writes activity_log
 * directly. Forces company_id from CompanyContext (via the Activity model's
 * BelongsToCompany hook) and strips PII before anything is persisted.
 */
class AuditLogger
{
    /**
     * Keys stripped from properties regardless of model configuration —
     * the fail-closed floor under the per-model $auditExclude list.
     *
     * @var list<string>
     */
    public const DEFAULT_DENYLIST = [
        'password',
        'remember_token',
        'secret',
        'token',
        'api_key',
        'national_id',
        'bsn',
        'date_of_birth',
        'dob',
        'iban',
        'salary',
        'app_authentication_secret',
        'app_authentication_recovery_codes',
    ];

    /** @param array<string, mixed> $properties */
    public function log(string $event, Model $subject, ?User $causer, array $properties = []): void
    {
        $properties = $this->sanitize($properties, $this->denylistFor($subject));

        activity(Str::before($event, '.') ?: 'default')
            ->performedOn($subject)
            ->causedBy($causer)
            ->event($event)
            ->withProperties($properties)
            ->log($event);
    }

    /**
     * Denylist = fail-closed floor + the subject's encrypted casts + its
     * optional $auditExclude list. Encrypted casts are included automatically
     * so an incomplete exclude list can never leak a sensitive value.
     *
     * @return list<string>
     */
    public function denylistFor(Model $subject): array
    {
        $encrypted = array_keys(array_filter(
            $subject->getCasts(),
            fn (string $cast): bool => str_starts_with($cast, 'encrypted'),
        ));

        /** @var list<string> $modelExclude */
        $modelExclude = property_exists($subject, 'auditExclude') ? $subject->auditExclude : [];

        return array_values(array_unique([...self::DEFAULT_DENYLIST, ...$encrypted, ...$modelExclude]));
    }

    /**
     * Strips denylisted keys recursively (covers the attributes/old nesting
     * spatie uses for before/after diffs) — field names survive as a marker,
     * raw values never do. company_id in properties is dropped outright: the
     * row's tenant column comes from context, never from the caller.
     *
     * @param  array<string, mixed>  $properties
     * @param  list<string>  $denylist
     * @return array<string, mixed>
     */
    public function sanitize(array $properties, array $denylist): array
    {
        unset($properties['company_id']);

        foreach ($properties as $key => $value) {
            if (in_array((string) $key, $denylist, true)) {
                $properties[$key] = '[redacted]';

                continue;
            }

            if (is_array($value)) {
                $properties[$key] = $this->sanitize($value, $denylist);
            }
        }

        return $properties;
    }
}
