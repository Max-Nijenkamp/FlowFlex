---
type: gap
severity: high
category: bug
status: resolved
color: "#F97316"
discovered: 2026-05-10
discovered_in: phase0-phase1-audit
last_updated: 2026-05-10
---

# Gap: ActivityLogResource allowed edits and deletes on audit records

## Context

Found during Phase 0+1 full audit. `ActivityLogResource` had `canCreate()` returning `false` but `canEdit()` and `canDelete()` were not overridden. Filament 5 defaults both to `true` based on user permissions.

## The Problem

Any admin panel user with `update` and `delete` permissions on the `ActivityLog` model could edit or delete audit log records — directly violating the immutability requirement of the audit trail (ISO 27001, SOX, GDPR).

This is a compliance and security issue. The audit log spec explicitly states: "Audit records are append-only — no update or delete via application."

## Resolution ✅

Added to `ActivityLogResource`:
```php
public static function canEdit(Model $record): bool { return false; }
public static function canDelete(Model $record): bool { return false; }
public static function canDeleteAny(): bool { return false; }
```

## Links

- Source builder log: [[core-platform-phase1]]
- Related spec: [[audit-log]]
