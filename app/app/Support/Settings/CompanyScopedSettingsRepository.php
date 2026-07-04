<?php

declare(strict_types=1);

namespace App\Support\Settings;

use App\Support\Services\CompanyContext;
use Illuminate\Database\Eloquent\Builder;
use Spatie\LaravelSettings\SettingsRepositories\DatabaseSettingsRepository;

/**
 * Tenant-scoped spatie/laravel-settings repository (core.company-settings):
 * every row carries company_id from CompanyContext, and reads fall back to
 * the config-declared defaults (config/company-settings.php) so no
 * per-company seeding or spatie settings-migrations are needed.
 * Spatie's cache stays DISABLED — its cache keys ignore the tenant.
 */
class CompanyScopedSettingsRepository extends DatabaseSettingsRepository
{
    public function getPropertiesInGroup(string $group): array
    {
        /** @var array<string, mixed> $defaults */
        $defaults = config("company-settings.defaults.{$group}", []);

        return array_merge($defaults, parent::getPropertiesInGroup($group));
    }

    public function checkIfPropertyExists(string $group, string $name): bool
    {
        return parent::checkIfPropertyExists($group, $name)
            || array_key_exists($name, config("company-settings.defaults.{$group}", []));
    }

    public function createProperty(string $group, string $name, $payload, bool $locked = false): void
    {
        $this->getBuilder()->create([
            'group' => $group,
            'name' => $name,
            'payload' => $this->encode($payload),
            'locked' => $locked,
            'company_id' => $this->companyId(),
        ]);
    }

    public function updatePropertiesPayload(string $group, array $properties): void
    {
        $companyId = $this->companyId();

        $batch = collect($properties)->map(fn ($payload, string $name): array => [
            'group' => $group,
            'name' => $name,
            'payload' => $this->encode($payload),
            'company_id' => $companyId,
        ])->values()->all();

        $this->getBuilder()
            ->where('group', $group)
            ->upsert($batch, ['group', 'name', 'company_id'], ['payload']);
    }

    public function getBuilder(): Builder
    {
        return parent::getBuilder()->where('company_id', $this->companyId());
    }

    private function companyId(): ?string
    {
        return app(CompanyContext::class)->currentId();
    }
}
