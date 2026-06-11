<?php

declare(strict_types=1);

use App\Contracts\Core\BillingServiceInterface;
use App\Data\Core\ActivateModuleData;
use App\Exceptions\Core\CannotDeactivateCoreModuleException;
use App\Exceptions\Core\ModuleAlreadyActiveException;
use App\Http\Middleware\SetCompanyContext;
use App\Models\Company;
use App\Models\Core\CompanyModuleSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function () {
    Cache::flush();
    $this->billing = app(BillingServiceInterface::class);
});

it('reports hasModule true only for active subscriptions of the current company', function () {
    $a = Company::factory()->create();
    $b = Company::factory()->create();
    CompanyModuleSubscription::factory()->forCompany($a)->module('core.settings')->create();
    CompanyModuleSubscription::factory()->forCompany($b)->module('core.audit')->create();

    $this->setCompany($a);
    expect($this->billing->hasModule('core.settings'))->toBeTrue()
        ->and($this->billing->hasModule('core.audit'))->toBeFalse();
});

it('returns false without company context instead of crashing', function () {
    expect($this->billing->hasModule('core.settings'))->toBeFalse();
});

it('seeds all free core modules and they gate true', function () {
    $company = Company::factory()->create();
    $this->billing->seedFreeCoreModules($company->id);

    $this->setCompany($company);
    expect($this->billing->hasModule('core.settings'))->toBeTrue()
        ->and($this->billing->hasModule('core.rbac'))->toBeTrue()
        ->and($this->billing->hasModule('core.marketplace'))->toBeTrue();
});

it('activates a module and busts the cache within the same request', function () {
    $company = Company::factory()->create();
    $this->setCompany($company);

    expect($this->billing->hasModule('core.settings'))->toBeFalse();

    $this->billing->activateModule(new ActivateModuleData('core.settings'));

    expect($this->billing->hasModule('core.settings'))->toBeTrue();
});

it('rejects double activation', function () {
    $company = Company::factory()->create();
    $this->setCompany($company);
    $this->billing->activateModule(new ActivateModuleData('core.settings'));

    $this->billing->activateModule(new ActivateModuleData('core.settings'));
})->throws(ModuleAlreadyActiveException::class);

it('refuses to deactivate a free core module', function () {
    $company = Company::factory()->create();
    $this->setCompany($company);
    $this->billing->seedFreeCoreModules($company->id);

    $this->billing->deactivateModule('core.settings');
})->throws(CannotDeactivateCoreModuleException::class);

it('rejects unknown module keys', function () {
    $company = Company::factory()->create();
    $this->setCompany($company);

    $this->billing->activateModule(new ActivateModuleData('nope.nothing'));
})->throws(InvalidArgumentException::class);

it('gates API routes via the module middleware alias', function () {
    Route::middleware(['web', SetCompanyContext::class, 'module:core.settings'])
        ->get('/_test/gated', fn () => response()->json(['ok' => true]));

    $company = Company::factory()->create();
    $user = User::factory()->forCompany($company)->create();

    // Not active -> 403
    $this->actingAs($user, 'web')->get('/_test/gated')->assertForbidden();

    // Activate -> 200
    $this->setCompany($company);
    $this->billing->activateModule(new ActivateModuleData('core.settings'));
    $this->actingAs($user, 'web')->get('/_test/gated')->assertOk();
});
