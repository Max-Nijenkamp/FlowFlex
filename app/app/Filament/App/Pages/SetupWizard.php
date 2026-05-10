<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\Core\SetupWizardProgress;
use App\Support\Services\CompanyContext;
use Filament\Pages\Page;

class SetupWizard extends Page
{
    protected static ?string $slug = 'setup';

    public string $currentStep = 'welcome';

    public array $completedSteps = [];

    public function getTitle(): string
    {
        return 'Setup Wizard';
    }

    public function getView(): string
    {
        return 'filament.app.pages.setup-wizard';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-rocket-launch';
    }

    public static function getNavigationLabel(): string
    {
        return 'Setup';
    }

    public static function canAccess(): bool
    {
        $ctx = app(CompanyContext::class);
        if (! $ctx->hasCompany()) {
            return false;
        }
        $company = $ctx->current();

        $progress = SetupWizardProgress::where('company_id', $company->id)->first();

        return $progress === null || ! $progress->completed;
    }

    public function mount(): void
    {
        $company = app(CompanyContext::class)->current();
        $progress = SetupWizardProgress::firstOrCreate(
            ['company_id' => $company->id],
            ['current_step' => 'welcome', 'completed_steps' => []],
        );

        $this->currentStep = $progress->current_step;
        $this->completedSteps = $progress->completed_steps ?? [];
    }

    public function completeStep(string $step): void
    {
        $company = app(CompanyContext::class)->current();
        $progress = SetupWizardProgress::where('company_id', $company->id)->firstOrFail();

        $progress->completeStep($step);

        $steps = SetupWizardProgress::steps();
        $currentIndex = array_search($step, $steps, true);
        $nextStep = $steps[$currentIndex + 1] ?? 'done';

        if ($nextStep === 'done') {
            $progress->update(['completed' => true, 'completed_at' => now(), 'current_step' => 'done']);
        } else {
            $progress->update(['current_step' => $nextStep]);
        }

        $this->currentStep = $progress->fresh()->current_step;
        $this->completedSteps = $progress->fresh()->completed_steps ?? [];
    }

    public function getSteps(): array
    {
        return SetupWizardProgress::steps();
    }

    public function getStepConfig(): array
    {
        return [
            'welcome'  => ['icon' => 'heroicon-o-rocket-launch',  'label' => 'Welcome',  'title' => 'Welcome to FlowFlex',           'description' => 'Let\'s get your workspace set up in a few quick steps. It only takes a few minutes.'],
            'company'  => ['icon' => 'heroicon-o-building-office', 'label' => 'Company',  'title' => 'Company information',           'description' => 'Review and complete your company profile so your team knows who you are.'],
            'team'     => ['icon' => 'heroicon-o-users',           'label' => 'Team',     'title' => 'Invite your team',              'description' => 'Add your colleagues so they can start collaborating with you right away.'],
            'modules'  => ['icon' => 'heroicon-o-squares-2x2',     'label' => 'Modules',  'title' => 'Enable modules',                'description' => 'Choose which modules to activate. You can always change this later from the Module Marketplace.'],
            'branding' => ['icon' => 'heroicon-o-paint-brush',     'label' => 'Branding', 'title' => 'Customise your workspace',      'description' => 'Add your logo and brand colours to make FlowFlex feel like home.'],
        ];
    }
}
