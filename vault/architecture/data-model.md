---
type: architecture
category: data-model
color: "#A78BFA"
---

# Core Data Model

---

## Principles

- **ULID PKs everywhere** — `ulid('id')->primary()` on all tables. Sortable, URL-safe, 26 chars, no enumeration attacks.
- **`company_id` on all tenant tables** — non-nullable ULID FK to `companies.id`. Global scope via `BelongsToCompany` trait.
- **Soft deletes on all models** — `deleted_at` on every model. Hard delete only via purge jobs or GDPR erasure.
- **Backed string enums** — status columns use PHP 8.1+ backed string enums, cast in the model.
- **Unique constraints scoped to company** — `UNIQUE (company_id, email)` not `UNIQUE (email)`.

---

## Standard Table Schema

```php
Schema::create('hr_employees', function (Blueprint $table) {
    $table->ulid('id')->primary();
    $table->foreignUlid('company_id')->references('id')->on('companies');

    // business columns
    $table->string('first_name');
    $table->string('last_name');
    $table->string('email')->nullable();
    $table->string('status')->default('active');

    // audit columns (optional but standard)
    $table->foreignUlid('created_by')->nullable()->references('id')->on('users');
    $table->foreignUlid('updated_by')->nullable()->references('id')->on('users');

    $table->timestamps();
    $table->softDeletes();

    $table->index('company_id');                      // mandatory
    $table->unique(['company_id', 'email']);           // uniqueness scoped to company
});
```

---

## Core Entities

| Table | Tenant Scoped | Description |
|---|---|---|
| `companies` | No (anchor) | Tenant record. Every other table's `company_id` points here. |
| `users` | Yes | Platform users — people who log in to Filament panels |
| `admins` | No | FlowFlex staff — separate model and guard for `/admin` |
| `user_invitations` | Yes | Pending invitations to join a workspace |
| `company_module_subscriptions` | Yes | Which modules a company has activated |
| `module_catalog` | No (platform-level) | All available modules — keys, domain, price |
| `activity_log` | Yes | Spatie activitylog — full audit trail |
| `notifications` | Yes | Laravel notification table — in-app inbox |
| `media` | Yes | Spatie media-library — file attachments |
| `personal_access_tokens` | Yes | Sanctum API tokens |

---

## Core ERD

```mermaid
erDiagram
    companies {
        ulid id PK
        string name
        string slug
        string email
        string subscription_status
        string timezone
        string locale
        string currency
        timestamp trial_ends_at
        timestamp deleted_at
    }

    users {
        ulid id PK
        ulid company_id FK
        string first_name
        string last_name
        string email
        string password
        boolean two_factor_enabled
        timestamp email_verified_at
        timestamp last_login_at
        timestamp deleted_at
    }

    admins {
        ulid id PK
        string name
        string email
        string password
        timestamp deleted_at
    }

    user_invitations {
        ulid id PK
        ulid company_id FK
        string email
        string token
        string role
        timestamp accepted_at
        timestamp expires_at
    }

    module_catalog {
        ulid id PK
        string module_key
        string domain
        string name
        decimal per_user_monthly_price
        boolean is_active
    }

    company_module_subscriptions {
        ulid id PK
        ulid company_id FK
        string module_key
        timestamp activated_at
        timestamp deactivated_at
        ulid activated_by FK
    }

    companies ||--o{ users : "has many"
    companies ||--o{ user_invitations : "has many"
    companies ||--o{ company_module_subscriptions : "subscribes to"
    module_catalog ||--o{ company_module_subscriptions : "referenced by"
    users ||--o{ company_module_subscriptions : "activated_by"
```

---

## Non-Tenant Models

Platform-level models (no `company_id`) use `HasUlids` only — no `BelongsToCompany`, no `SoftDeletes` unless explicitly needed:

```php
// Module catalog — static data backed by Sushi
class ModuleCatalog extends Model
{
    use HasUlids;
    // No BelongsToCompany — not tenant-scoped
    // No SoftDeletes — platform data, not deleted
}
```
