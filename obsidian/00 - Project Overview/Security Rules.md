---
tags: [flowflex, security, rules]
domain: Platform
status: built
last_updated: 2026-05-07
---

# Security Rules

Non-negotiable security rules for the FlowFlex platform. Every one of these applies to every module, every endpoint, every piece of code.

> **Bug history** (past security bugs found and fixed) → [[Bug Registry]] in `_Brain/`

---

## Rule 1 — Never Trust User Input

All form data goes through Laravel Form Requests with explicit validation rules.

- Every controller action that accepts user input uses a dedicated `FormRequest` class
- No `$request->all()` mass-assignment without explicit validation
- Validate types, lengths, formats, and enum values explicitly

---

## Rule 2 — All API Endpoints Require Authentication

No public endpoints unless explicitly intended (e.g. booking page, storefront).

- Every API route is behind `AuthenticateApiKey` middleware
- Public endpoints are explicitly marked and reviewed
- The `/health` endpoint is public but rate-limited (60 req/min)

---

## Rule 3 — All Queries Are Tenant-Scoped

`BelongsToCompany` adds `WHERE company_id = :current_company` to every query automatically. **Never bypass this in module code.**

```php
// WRONG — exposes all companies' data
Employee::withoutGlobalScopes()->all();

// CORRECT — global scope applies automatically when tenant guard is active
Employee::all();

// Admin panel only — always pair with explicit company_id filter
Company::withoutGlobalScopes()->count();

// Queue jobs only — always pair with explicit company_id
Payslip::withoutGlobalScopes()->where('company_id', $this->payRun->company_id)->firstOrCreate([...]);
```

---

## Rule 4 — Sensitive Fields Are Encrypted at Rest

Bank details, government IDs, API keys, OAuth tokens use the `encrypted` cast. Password-type fields use the `hashed` cast.

```php
protected function casts(): array
{
    return [
        'bank_account_number'    => 'encrypted',
        'national_id_encrypted'  => 'encrypted',   // column name includes _encrypted
        'tax_reference_encrypted'=> 'encrypted',
        'password_hash'          => 'hashed',
    ];
}
```

**Never include encrypted fields in `logOnly()`** — Spatie logs the pre-cast (plaintext) value.

---

## Rule 5 — Audit Every Write

Use `LogsActivity` on all models. Use `logOnly([...])` with an explicit whitelist — never `logFillable()` or `logAll()`.

Exclude from activity logs: `*_encrypted` fields, `password_hash`, `gross_pay`, `net_pay`, salary amounts.

```php
public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->logOnly(['first_name', 'last_name', 'email', 'status'])
        ->logOnlyDirty();
}
```

---

## Rule 6 — File Access Is Signed and Expiring

Never expose raw S3 URLs. Always use `FileStorageService::temporaryUrl()` or `$file->url()`.

```php
// WRONG
return Storage::url($file->path); // permanent public URL, exposes bucket structure

// CORRECT
return FileStorageService::temporaryUrl($file, now()->addMinutes(15));
```

File permissions are checked before generating signed URLs.

---

## Rule 7 — Rate Limit All APIs

Default: 60 requests per minute per API key.

All authenticated API routes are already wrapped in `AuthenticateApiKey` middleware which handles key validation. Rate limiting is configurable per company tier.

---

## Rule 8 — RBAC on Every Filament Resource

Every `Resource` implements all four methods explicitly — never rely on Filament defaults:

```php
public static function canViewAny(): bool  { return auth()->user()?->can('hr.employees.view') ?? false; }
public static function canCreate(): bool   { return auth()->user()?->can('hr.employees.create') ?? false; }
public static function canEdit($record): bool   { return auth()->user()?->can('hr.employees.edit') ?? false; }
public static function canDelete($record): bool { return auth()->user()?->can('hr.employees.delete') ?? false; }
```

Permission naming: `{module}.{resource}.{action}` — e.g. `finance.invoices.send`, `crm.tickets.resolve`.

---

## Rule 9 — Tenant Dropdowns Must Be Company-Scoped

`Tenant` is the auth model — it has no `BelongsToCompany` global scope. Any `Select` listing tenants must scope manually:

```php
->options(fn () => Tenant::where('company_id', auth()->user()?->company_id)
    ->get()
    ->mapWithKeys(fn ($t) => [$t->id => "{$t->first_name} {$t->last_name}"]))
```

`Tenant` has no `name` column — labels must be built from `first_name` + `last_name`.

---

## Rule 10 — Activity Logs Must Not Contain Sensitive Data

`logFillable()` will write encrypted fields and financial amounts in plaintext. Always use an explicit `logOnly([...])` whitelist. Exclude: `*_encrypted`, `password_hash`, `gross_pay`, `net_pay`, `salary`.

---

## Rule 11 — Queue Jobs Have No Auth Context

`auth()->user()` returns null in queue workers. `BelongsToCompany` global scope does not fire. Always use `withoutGlobalScopes()` + explicit `company_id`, and add a comment explaining why.

---

## Rule 12 — Password/Hash Fields Use `hashed` Cast

Fields storing hashed values use the `hashed` cast — never hash manually. Exclude from `logOnly`.

---

## Rule 13 — All Models Get `LogsActivity` — Including Marketing/CMS

Marketing models (`BlogPost`, `DemoRequest`, `NewsletterSubscriber`, etc.) are not tenant-scoped but still receive writes. Every model in `app/Models/Marketing/` gets `LogsActivity` + `getActivitylogOptions()`.

---

## Related

- [[Architecture]]
- [[Multi-Tenancy]]
- [[Roles & Permissions (RBAC)]]
- [[API & Integrations Layer]]
- [[Patterns]] _(in `_Brain/`)_
- [[Bug Registry]] _(in `_Brain/`)_
