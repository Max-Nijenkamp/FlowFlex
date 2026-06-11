<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('serves every marketing page as an Inertia response', function (string $path, string $component) {
    $this->get($path)
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component($component));
})->with([
    ['/', 'Marketing/Home'],
    ['/pricing', 'Marketing/Pricing'],
    ['/features', 'Marketing/Features'],
    ['/about', 'Marketing/About'],
    ['/contact', 'Marketing/Contact'],
    ['/terms', 'Marketing/Terms'],
    ['/privacy', 'Marketing/Privacy'],
]);

it('shares live module data with the homepage', function () {
    $this->get('/')->assertInertia(fn ($page) => $page
        ->component('Marketing/Home')
        ->has('domains')
        ->has('module_count')
        ->has('sample_modules'));
});

it('shares per-domain flows with the product page', function () {
    $this->get('/features')->assertInertia(fn ($page) => $page
        ->component('Marketing/Features')
        ->has('domains.0.flows'));
});

it('serves each domain product page with its modules and flows', function (string $domain) {
    $this->get("/product/{$domain}")
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Marketing/Domain')
            ->where('domain.key', $domain)
            ->has('modules'));
})->with(['hr', 'finance', 'crm', 'core']);

it('404s unknown domains', function () {
    $this->get('/product/warehouse')->assertNotFound();
});

it('passes the pricing deep-link through', function () {
    $this->get('/pricing?domain=finance')->assertInertia(fn ($page) => $page
        ->where('open_domain', 'finance'));
    $this->get('/pricing?domain=bogus')->assertInertia(fn ($page) => $page
        ->where('open_domain', null));
});

it('serves the sitemap', function () {
    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml')
        ->assertSee('/product/hr');
});

it('shares the module catalog with the pricing calculator', function () {
    $this->get('/pricing')->assertInertia(fn ($page) => $page
        ->component('Marketing/Pricing')
        ->has('modules')
        ->has('base_price_cents'));
});

it('accepts a contact submission and blocks honeypot bots', function () {
    $this->post('/contact', [
        'name' => 'Max', 'email' => 'max@lead.test', 'message' => 'Tell me more',
    ])->assertRedirect('/contact')->assertSessionHas('success');

    $this->post('/contact', [
        'name' => 'Bot', 'email' => 'bot@spam.test', 'message' => 'spam', 'website' => 'http://spam.test',
    ])->assertRedirect('/contact')->assertSessionMissing('success');
});

it('logs in through the public Vue login and lands in the workspace', function () {
    $company = Company::factory()->create();
    $this->setCompany($company);
    $user = User::factory()->forCompany($company)->create(['password' => 'super-secret-password']);

    $this->get('/login')->assertInertia(fn ($page) => $page->component('Auth/Login'));

    $this->post('/login', ['email' => $user->email, 'password' => 'super-secret-password'])
        ->assertRedirect('/app');
    $this->assertAuthenticatedAs($user, 'web');

    $this->post('/logout')->assertRedirect('/');
    $this->assertGuest('web');
});

it('rejects bad credentials without enumeration detail', function () {
    $this->post('/login', ['email' => 'nobody@nowhere.test', 'password' => 'wrong-password-123'])
        ->assertSessionHasErrors('email');
});

it('serves the invite registration as a Vue page', function () {
    $company = Company::factory()->create();
    $this->setCompany($company);
    $invitation = UserInvitation::factory()->forCompany($company)->create();

    $this->get("/register/invite/{$invitation->token}")
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Auth/InviteRegister')
            ->where('email', $invitation->email));
});

it('sends a password reset link without account enumeration', function () {
    $this->post('/forgot-password', ['email' => 'ghost@nowhere.test'])
        ->assertSessionHas('success'); // same response either way
});
