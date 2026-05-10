---
type: adr
date: 2026-05-10
status: decided
color: "#F97316"
---

# Decision: Activity log is immutable — no `updated_at` column

## Context

The audit log is a compliance record. Records must never be modified. The `spatie/laravel-activitylog` ORM was trying to write `updated_at` on every log entry even though the migration did not include that column.

## Options Considered

1. **Add `updated_at` column** — easy fix, but allows Eloquent to update audit records. Violates immutability requirement.
2. **`$timestamps = false` on ActivityLog model** — disables Eloquent timestamp management entirely. Still need `created_at` readable.
3. **Override `setUpdatedAt()`** — no-op the setter. More complex than needed.

## Decision

Custom `ActivityLog` model extends `Spatie\Activitylog\Models\Activity` with:
```php
public $timestamps = false;
protected $dates = ['created_at'];
```

Migration has only `created_at` (no `updated_at`). This matches the spec: "No `updated_at`, no `deleted_at` — this table is immutable."

## Consequences

- Audit records cannot be updated via Eloquent (any `->save()` call on an existing ActivityLog will skip timestamps, which is correct)
- `created_at` is still cast to Carbon for readable timestamps
- GDPR erasure must handle actor fields directly via raw SQL or a dedicated anonymisation job (not via standard `->update()` path)

## Related Left Brain

- [[audit-log]] — spec explicitly states immutability
