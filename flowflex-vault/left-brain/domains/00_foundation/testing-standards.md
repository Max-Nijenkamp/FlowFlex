---
type: module
domain: Foundation
panel: n/a
phase: 0
status: complete
last_updated: 2026-05-09
right_brain_log: "[[builder-log-testing-standards]]"
---

# Testing Standards

Every phase of FlowFlex must have a test suite that proves the phase is correct before moving on. Tests are not optional — they are the gate that allows Phase N+1 to begin.

---

## Test Framework

- **Pest PHP** (v4+) — all tests written using Pest syntax
- **pestphp/pest-plugin-laravel** — Laravel testing helpers
- **pestphp/pest-plugin-livewire** — Livewire/Filament component testing
- **RefreshDatabase** — all Feature tests use DB transactions

---

## Test Categories Per Phase

### 1. Unit Tests (`tests/Unit/`)

For every new model, service, trait, or utility class added in the phase:

- **Models** — test all custom methods (status checks, accessors, computed properties)
- **Services** — test business logic with isolated inputs/outputs
- **Traits** — test behavior without DB involvement where possible
- **DTOs** — test validation rules and transformations

Unit tests do NOT use `RefreshDatabase`. They use `new ModelClass([...])` directly.

### 2. Feature Tests (`tests/Feature/`)

For every flow, panel, or integration point added in the phase:

#### Auth & Guard Tests
- All login/logout flows for guards used in the phase
- Guard isolation: guard A cannot authenticate guard B
- Test using `Livewire::test(\Filament\Auth\Pages\Login::class)` for Filament panels
- Set `Filament::setCurrentPanel(Filament::getPanel('panel-id'))` in `beforeEach`
- Clear auth state in `beforeEach`: `auth()->guard('web')->logout(); auth()->guard('admin')->logout();`
- CSRF: use `->withoutMiddleware(VerifyCsrfToken::class)` for POST logout tests

#### Multi-Tenancy Tests
- Company scope applies correctly (records from other companies are invisible)
- CompanyContext singleton sets/clears correctly
- Data created within a request is scoped to the current company

#### Filament Panel Tests
- Unauthenticated → redirects to login
- Authenticated → dashboard loads (200)
- Resource list pages load (200)
- Resources only show data scoped to current user/company

#### Seeder Tests (for local seeders)
- Seeder creates expected records
- Seeder is idempotent (safe to run twice)
- Credentials in seeder are correct

---

## Known Patterns and Gotchas

### Filament Login via Livewire
Do NOT post to `/admin/login` or `/app/login` — Filament 5 has no POST route for login.
Use Livewire component testing:
```php
Livewire::test(Login::class)
    ->set('data.email', 'user@example.com')
    ->set('data.password', 'password')
    ->call('authenticate')
    ->assertHasNoFormErrors()
    ->assertRedirect();
```

### Filament Form Error Keys
`assertHasFormErrors(['email'])` checks for the key AFTER statePath prefix is applied.
The Login form uses `->statePath('data')`, so validation errors are `data.email`.
Pass just `'email'` to `assertHasFormErrors` — Filament applies the prefix automatically.

### Rate Limiter Isolation
Filament's Login component has a built-in rate limiter (`rateLimit(5)`).
Running many login tests in sequence will trigger it and cause silent failures.
The global `Pest.php` clears the rate limiter before each Feature test:
```php
RateLimiter::clear(
    'livewire-rate-limiter:' . sha1(Login::class . '|authenticate|127.0.0.1')
);
```

### Auth State Isolation
`RefreshDatabase` rolls back the DB but does NOT reset in-memory auth state.
Always clear auth guards in `beforeEach` when testing login flows:
```php
auth()->guard('admin')->logout();
auth()->guard('web')->logout();
```

### Filament Panel Context
`Livewire::test(Login::class)` needs the correct panel set before running.
Always call `Filament::setCurrentPanel(Filament::getPanel('panel-id'))` in `beforeEach`.

### Company Context in Filament
`SetCompanyContext` middleware must be in the Filament panel's `authMiddleware` list.
Without it, `CompanyScope` never applies and all tenant data leaks across companies.
Check `WorkspacePanelProvider::panel()` for `->authMiddleware([..., SetCompanyContext::class])`.

### Testing Horizon Auth
`actingAs($admin, 'admin')` + `->get('/horizon')` returns 403 in tests — this is a
known limitation of how Horizon's controller-level middleware resolves the admin guard.
Test Horizon auth via the callback directly:
```php
$callback = Horizon::$authUsing;
$this->actingAs($admin, 'admin');
expect($callback(request()))->toBeTrue();
```

---

## Phase Completion Checklist

Before marking a phase complete, ALL tests must pass:

```
php artisan test --no-ansi
```

- [ ] All unit tests pass
- [ ] All feature tests pass
- [ ] No skipped/risky tests (except intentionally)
- [ ] `Tests: N passed` with 0 failures

---

## File Structure Convention

```
tests/
├── Unit/
│   ├── Models/           # One file per model (AdminTest, UserTest, etc.)
│   └── Services/         # One file per service
└── Feature/
    ├── Auth/             # Auth flows per guard
    ├── MultiTenancy/     # Company scope and context tests
    ├── Filament/         # Panel smoke tests per panel
    └── Seeders/          # Local seeder verification
```

For each new domain added (Phase 1+), add tests under:
```
tests/Feature/{DomainName}/
```
