<?php

declare(strict_types=1);

namespace App\Support\Import;

use App\Contracts\Core\BillingServiceInterface;

/**
 * Each domain module registers its importer in its ServiceProvider:
 *   app(ImporterRegistry::class)->register('hr.employees', EmployeeImporter::class);
 * available() filters by module activation — inactive targets never appear.
 */
class ImporterRegistry
{
    /** @var array<string, class-string<ImporterInterface>> */
    private array $importers = [];

    /** @param class-string<ImporterInterface> $importer */
    public function register(string $key, string $importer): void
    {
        $this->importers[$key] = $importer;
    }

    /** @return array<string, class-string<ImporterInterface>> */
    public function available(): array
    {
        $billing = app(BillingServiceInterface::class);

        // Importer key = module key of the target (e.g. hr.employees).
        return array_filter(
            $this->importers,
            fn (string $key) => $billing->hasModule($key),
            ARRAY_FILTER_USE_KEY,
        );
    }

    public function resolve(string $key): ImporterInterface
    {
        return app($this->importers[$key] ?? throw new \InvalidArgumentException("Unknown importer [{$key}]."));
    }

    public function has(string $key): bool
    {
        return isset($this->importers[$key]);
    }
}
