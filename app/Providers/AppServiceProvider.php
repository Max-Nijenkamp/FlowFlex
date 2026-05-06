<?php

namespace App\Providers;

use App\Models\ApiKey;
use App\Models\Company;
use App\Models\File;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Policies\ApiKeyPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\FilePolicy;
use App\Policies\ModulePolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\TenantPolicy;
use App\Policies\UserPolicy;
use App\Services\FileStorageService;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FileStorageService::class);
    }

    public function boot(): void
    {
        $this->registerPolicies();
        $this->configureDefaults();
        $this->configureLanguageSwitch();
    }

    protected function registerPolicies(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Company::class, CompanyPolicy::class);
        Gate::policy(Tenant::class, TenantPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);
        Gate::policy(Module::class, ModulePolicy::class);
        Gate::policy(ApiKey::class, ApiKeyPolicy::class);
        Gate::policy(File::class, FilePolicy::class);
    }

    protected function configureLanguageSwitch(): void
    {
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en', 'nl', 'de', 'fr', 'es'])
                ->visible(insidePanels: true)
                ->nativeLabel()
                ->flags([
                    'en' => asset('flags/gb.svg'),
                    'nl' => asset('flags/nl.svg'),
                    'de' => asset('flags/de.svg'),
                    'fr' => asset('flags/fr.svg'),
                    'es' => asset('flags/es.svg'),
                ]);
        });
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }


}
