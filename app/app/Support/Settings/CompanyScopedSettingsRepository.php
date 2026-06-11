<?php

declare(strict_types=1);

namespace App\Support\Settings;

use App\Support\Services\CompanyContext;
use Illuminate\Database\Eloquent\Builder;
use Spatie\LaravelSettings\SettingsRepositories\DatabaseSettingsRepository;

/**
 * Scopes every settings row to the current company (CompanyContext) and merges
 * class-declared defaults for properties a company has not written yet — so a
 * fresh company reads defaults without per-company settings migrations.
 */
class CompanyScopedSettingsRepository extends DatabaseSettingsRepository
{
    public function getBuilder(): Builder
    {
        return parent::getBuilder()->where('company_id', $this->companyId());
    }

    public function getPropertiesInGroup(string $group): array
    {
        $stored = parent::getPropertiesInGroup($group);

        return array_merge(SettingsDefaults::forGroup($group), $stored);
    }

    public function createProperty(string $group, string $name, $payload, bool $locked = false): void
    {
        parent::getBuilder()->create([
            'company_id' => $this->companyId(),
            'group' => $group,
            'name' => $name,
            'payload' => $this->encode($payload),
            'locked' => $locked,
        ]);
    }

    public function updatePropertiesPayload(string $group, array $properties): void
    {
        $companyId = $this->companyId();

        $batch = collect($properties)->map(fn ($payload, $name) => [
            'company_id' => $companyId,
            'group' => $group,
            'name' => $name,
            'payload' => $this->encode($payload),
        ])->values()->all();

        parent::getBuilder()->upsert($batch, ['company_id', 'group', 'name'], ['payload']);
    }

    private function companyId(): ?string
    {
        return app(CompanyContext::class)->currentId();
    }
}
