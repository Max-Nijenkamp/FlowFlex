<?php

declare(strict_types=1);

use App\Filament\App\Pages\SetupWizard;
use App\Models\Company;
use App\Models\Core\SetupWizardProgress;
use App\Models\User;
use App\Support\Services\CompanyContext;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

describe('Setup Wizard', function () {
    beforeEach(function () {
        auth()->guard('web')->logout();
        Filament::setCurrentPanel(Filament::getPanel('app'));

        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);

        setPermissionsTeamId($this->company->id);
        $ownerRole = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $this->user->assignRole($ownerRole);

        app(CompanyContext::class)->set($this->company);
        $this->actingAs($this->user, 'web');
    });

    it('is accessible when wizard not completed', function () {
        expect(SetupWizard::canAccess())->toBeTrue();
    });

    it('is not accessible after wizard is completed', function () {
        SetupWizardProgress::create([
            'company_id'      => $this->company->id,
            'completed'       => true,
            'completed_at'    => now(),
            'current_step'    => 'done',
            'completed_steps' => SetupWizardProgress::steps(),
        ]);

        expect(SetupWizard::canAccess())->toBeFalse();
    });

    it('renders with welcome step by default', function () {
        Livewire::test(SetupWizard::class)
            ->assertSet('currentStep', 'welcome');
    });

    it('advances step when completeStep is called', function () {
        Livewire::test(SetupWizard::class)
            ->call('completeStep', 'welcome')
            ->assertSet('currentStep', 'company');
    });

    it('persists step completion to database', function () {
        Livewire::test(SetupWizard::class)
            ->call('completeStep', 'welcome');

        $progress = SetupWizardProgress::where('company_id', $this->company->id)->first();
        expect($progress)->not->toBeNull();
        expect($progress->hasStep('welcome'))->toBeTrue();
        expect($progress->current_step)->toBe('company');
    });

    it('marks wizard as completed after last step', function () {
        $test = Livewire::test(SetupWizard::class);

        foreach (['welcome', 'company', 'team', 'modules', 'branding'] as $step) {
            $test->call('completeStep', $step);
        }

        $progress = SetupWizardProgress::where('company_id', $this->company->id)->first();
        expect($progress->completed)->toBeTrue();
        expect($progress->completed_at)->not->toBeNull();
    });
});
