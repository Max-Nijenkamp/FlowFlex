<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Contracts\BillingServiceInterface;
use App\Models\ModuleCatalogEntry;
use App\Models\User;
use App\Support\Services\CompanyContext;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Workspace Hub (core.hub/domain-launcher): one tile per domain the
 * company has active AND the user may access. Pure read/compose — owns
 * no tables. Ordering: alphabetical (recency/favourites deferred, spec
 * *(assumed)* item).
 */
class WorkspaceHubPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Hub';

    protected static ?string $title = 'Workspace';

    protected static ?string $slug = 'hub';

    protected static ?int $navigationSort = -1;

    protected string $view = 'filament.app.pages.workspace-hub';

    /** Domain tiles: name, descriptor, colour (design palette), panel path. */
    public const DOMAINS = [
        'hr' => ['name' => 'HR & people', 'blurb' => 'Profiles, leave and onboarding', 'color' => '#8B5CF6'],
        'finance' => ['name' => 'Finance', 'blurb' => 'Ledger, invoicing and payments', 'color' => '#10B981'],
        'crm' => ['name' => 'CRM', 'blurb' => 'Contacts, deals and pipeline', 'color' => '#F43F5E'],
        'projects' => ['name' => 'Projects', 'blurb' => 'Tasks, boards and time', 'color' => '#6366F1'],
        'comms' => ['name' => 'Communications', 'blurb' => 'Announcements and channels', 'color' => '#3B82F6'],
        'support' => ['name' => 'Support', 'blurb' => 'Tickets and SLAs', 'color' => '#F97316'],
    ];

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->can('core.hub.view');
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);
    }

    /** @return Collection<int, array{key: string, name: string, blurb: string, color: string, url: string}> */
    public function getTilesProperty(): Collection
    {
        /** @var User $user */
        $user = Auth::user();
        $companyId = app(CompanyContext::class)->current()->id;

        $activeDomains = ModuleCatalogEntry::query()
            ->whereIn('module_key', app(BillingServiceInterface::class)->activeModules($companyId))
            ->where('domain', '!=', 'core')
            ->pluck('domain')
            ->unique();

        return collect(self::DOMAINS)
            ->filter(fn (array $meta, string $domain): bool => $activeDomains->contains($domain)
                && $user->can("access.{$domain}"))
            ->map(fn (array $meta, string $domain): array => [
                'key' => $domain,
                'name' => $meta['name'],
                'blurb' => $meta['blurb'],
                'color' => $meta['color'],
                'url' => url('/'.$domain),
            ])
            ->sortBy('name')
            ->values();
    }

    public function getIsOwnerProperty(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->hasRole('owner');
    }
}
