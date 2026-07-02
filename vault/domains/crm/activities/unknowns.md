---
domain: crm
module: activities
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Activities — Unknowns & Assumptions

All items below are unverified. They function as authoritative defaults at build time but are overridable via ADR.

---

## Open Questions

1. **Reminder window: how many minutes before `due_at`?**
   The `TaskReminderCommand` runs every 15 minutes and fires a reminder once. The spec does not define how far in advance the reminder is sent (e.g. 15 min before, 1 hour before, at the moment `due_at` passes). Needs a product decision.

2. **Outcome field: free text or enum?**
   `outcome` is `string nullable` — no validation in the spec. Should it be free text or constrained to a set of values per type (e.g. "Interested", "No answer", "Left voicemail" for calls)?

3. **Duration: enforced only for certain types?**
   `duration_minutes` is nullable. Is it displayed/required only for Call and Meeting types, or free on all?

4. **Account vs. Company relationship**
   The spec references `account_id` as a nullable FK but the table name is not stated. Assumed to be `crm_accounts`. Confirm the FK target table name.

---

## Assumed Items (verbatim from spec, unverified)

No explicit `*(assumed)*` markers were present in this spec. The following are implicitly assumed:
- `account_id` FK references `crm_accounts` table (table name not stated in spec)
- Cursor pagination page size: not specified — defaults to Laravel's default (15) unless overridden
- `TimelineQuery` returns activities across all types — no type filter at the shared scope level (filtering is per-consumer)
