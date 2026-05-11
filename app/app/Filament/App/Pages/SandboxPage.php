<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\Core\Sandbox;
use App\Support\Services\CompanyContext;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;

class SandboxPage extends Page
{
    protected static ?string $slug = 'sandbox';

    public ?Sandbox $sandbox = null;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-beaker';
    }

    public static function getNavigationLabel(): string
    {
        return 'Sandbox';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Tools';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function canAccess(): bool
    {
        return auth()->check()
            && app(CompanyContext::class)->hasCompany();
    }

    public function getTitle(): string
    {
        return 'Sandbox';
    }

    public function getView(): string
    {
        return 'filament.app.pages.sandbox';
    }

    public function mount(): void
    {
        $companyId     = app(CompanyContext::class)->currentId();
        $this->sandbox = Sandbox::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->first();
    }

    public function provisionSandbox(): void
    {
        $ctx       = app(CompanyContext::class);
        $companyId = $ctx->currentId();

        if (Sandbox::withoutGlobalScopes()->where('company_id', $companyId)->exists()) {
            Notification::make()
                ->title('A sandbox already exists for this company.')
                ->warning()
                ->send();

            return;
        }

        $company = $ctx->current();

        $this->sandbox = Sandbox::create([
            'company_id' => $companyId,
            'status'     => 'provisioning',
            'subdomain'  => Str::slug($company->name) . '-sandbox',
            'seed_type'  => 'blank',
        ]);

        Notification::make()
            ->title('Sandbox provisioning started.')
            ->body('This may take a few minutes.')
            ->success()
            ->send();
    }

    public function resetSandbox(): void
    {
        if ($this->sandbox === null) {
            Notification::make()
                ->title('No sandbox to reset.')
                ->danger()
                ->send();

            return;
        }

        $this->sandbox->update([
            'status'     => 'resetting',
            'updated_at' => now(),
        ]);

        $this->sandbox->refresh();

        Notification::make()
            ->title('Sandbox reset initiated.')
            ->body('Your sandbox data will be cleared shortly.')
            ->warning()
            ->send();
    }
}
