---
type: concept
category: data
last_updated: 2026-05-09
---

# Concept: Soft Deletes

---

## Rule

Every Eloquent model uses the `SoftDeletes` trait. No hard deletes occur in normal application paths.

---

## Why

- **Data recovery**: Accidental deletions can be undone by a super-admin without a database restore.
- **Audit trail**: Soft-deleted records remain visible in activity logs and historical reports (e.g. an invoice referencing a deleted contact still renders correctly).
- **GDPR erasure**: Erasure is a separate, controlled flow (see below) — not a simple `DELETE` query.
- **Accidental deletion recovery**: Users who delete something by mistake can have it restored by their company owner without a support ticket.

---

## What It Does

Soft deletes add a `deleted_at` timestamp column to the table. Records with `deleted_at IS NOT NULL` are considered deleted. Eloquent automatically excludes them from all queries via a global scope.

```php
// Normal query — deleted records excluded automatically
Employee::where('company_id', $companyId)->get();

// Include soft-deleted records
Employee::withTrashed()->where('company_id', $companyId)->get();

// Only soft-deleted records
Employee::onlyTrashed()->where('company_id', $companyId)->get();

// Restore a soft-deleted record
$employee->restore();
```

---

## Hard Delete Exceptions

Hard deletes happen only in these three controlled circumstances:

1. **Super-admin explicit purge**: A `super_admin` FlowFlex staff member triggers a manual purge action in the `/admin` panel. Requires confirmation modal. Logged in `spatie/laravel-activitylog`.

2. **Scheduled purge job**: A Laravel scheduled command permanently deletes records that have been soft-deleted for more than 90 days. This keeps the database lean while still providing a 90-day recovery window.

3. **GDPR erasure request**: Handled by the Data Privacy module. The flow anonymises PII fields first (`name → "Deleted User"`, `email → "deleted_{ulid}@deleted.invalid"`), then hard-deletes after any applicable legal hold period has passed. Cascades are handled explicitly — not by database foreign key `ON DELETE CASCADE`.

---

## Migration Pattern

Always include `softDeletes()` in every module migration:

```php
Schema::create('employees', function (Blueprint $table) {
    $table->ulid('id')->primary();
    $table->foreignUlid('company_id')->references('id')->on('companies');
    // ... columns ...
    $table->timestamps();
    $table->softDeletes(); // always last
});
```

---

## Model Pattern

Always include `use SoftDeletes;` on every module model:

```php
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;
    // ...
}
```

---

## Company Soft-Delete Cascade Behaviour

When a `Company` record is soft-deleted (e.g. cancelled subscription), its users and data are NOT automatically soft-deleted via Eloquent observers. Instead:

- `companies.status` is set to `suspended`
- All `users.status` for that company are set to `suspended`
- Users' active sessions are invalidated
- Portal users are prevented from logging in

Full company erasure (for GDPR or explicit cancellation + erasure request) goes through the Data Privacy module's erasure flow, which handles cascades explicitly in a queued job.

---

## Related

- `[[data-architecture]]` — overall data model conventions
- `[[concept-multi-tenancy]]` — company_id scoping and what happens when a company is suspended
