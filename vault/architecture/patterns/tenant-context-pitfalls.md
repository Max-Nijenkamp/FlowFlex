---
type: architecture
category: pattern
color: "#A78BFA"
---

# Tenant-Context Pitfalls — the null-team family

Spatie permission with `teams = company_id` means **every** `can()` / `hasRole()` / `getAllPermissions()` silently depends on `setPermissionsTeamId()` having run first. When it hasn't, roles load **empty and get cached on the model instance** (`loadMissing` keeps the empty set for the whole request) — symptoms are 403s that make no sense because the user demonstrably has the permission.

Three production bugs came from this exact mechanism (2026-06-11/12). This file exists so there is never a fourth.

---

## The Rule

> Any spatie check that can run **before** `SetCompanyContext` middleware — or outside a panel request entirely — must set the team id itself first, and unset stale relations.

```php
if (getPermissionsTeamId() !== $user->company_id) {
    setPermissionsTeamId($user->company_id);
    $user->unsetRelation('roles');
    $user->unsetRelation('permissions');
}
```

Places this applies:
- **Filament `canAccessPanel()`** — runs inside Filament's Authenticate middleware, BEFORE SetCompanyContext (bug #3: every domain-panel switch 403'd)
- **Inertia `HandleInertiaRequests::share()`** — runs in the global web group, which ALSO wraps Filament's Livewire update route (bug #2: eager `getAllPermissions()` poisoned every Livewire request). Fix: `auth` shared prop is a LAZY closure; never call permission APIs eagerly in share()
- **Queued jobs/listeners** — covered by the `WithCompanyContext` job middleware; never remove it
- Any artisan command, webhook handler, or scheduled task touching tenant permissions

## Livewire persistence (bug #1)

Filament `->authMiddleware([...])` is **NOT persistent by default**. Livewire update POSTs (deferred tables, every action) only re-run persistent middleware. All panels must register:

```php
->authMiddleware([...], isPersistent: true)
```

Regression test (PanelAuthTest): GET a panel page, then assert `app(Livewire\Mechanisms\PersistentMiddleware\PersistentMiddleware::class)->getPersistentMiddleware()` contains `SetCompanyContext`.

## Debug recipe (found bug #2 in minutes)

```php
DB::listen(function ($q): void {
    if (str_contains($q->sql, 'model_has_roles') && getPermissionsTeamId() === null) {
        logger()->info('null-team role query', ['trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20)]);
    }
});
```

## Verification that actually catches this

Plain curl GETs and the Pest suite stayed green through ALL three bugs. Only two things catch this class:
1. A **real scripted Livewire POST**: page GET → extract `wire:snapshot` + `data-csrf` + the `/livewire-{hash}/update` URL → POST with `X-Livewire: 1` header → expect 200
2. Regression tests that force `setPermissionsTeamId(null)` + `unsetRelation('roles')` before the request

Both live in `tests/Feature/PanelAuthTest.php` + `StaffConsoleTest.php` — copy the pattern for new panels.

## Related

- [[architecture/multi-tenancy]] — CompanyContext + CompanyScope
- [[architecture/patterns/testing-pattern]] — Livewire test idioms
