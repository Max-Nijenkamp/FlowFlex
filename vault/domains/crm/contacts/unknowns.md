---
domain: crm
module: contacts
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Contacts — Unknowns & Assumed Items

## Assumed Items (*(assumed)* markers from spec)

- **Merge audit trail** — `merge(keepId, mergeId)` is described as "audited *(assumed)*". It is not confirmed whether activitylog records the merge event or whether a custom audit table is needed. Assumed: activitylog via `spatie/laravel-activitylog` records the merge with both IDs.

---

## Open Questions

None explicitly listed in the original spec beyond the assumed items above.

---

## Implementation Notes (tense-softened)

The following notes from the original spec described the module as if it were built (2026-06-12 session). They are retained here as **intended design** pending actual build:

- `AccountResource` (Organisations) is intended for the `/crm` panel: CRUD + contacts/deals counts + lifetime value + attachments
- `ContactResource` is intended to have lifecycle tabs (All / Leads / Opportunities / Customers), organisation link + filter, attachments collection
- Contact + Account are intended to implement `HasMedia` (`attachments` collection)
