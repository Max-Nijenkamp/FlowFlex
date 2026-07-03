---
domain: crm
module: contacts
type: feature
feature: duplicate-detection
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Duplicate Detection

## Purpose

Prevent multiple contact records for the same person within a company. Detects duplicates at create-time (UI and API) and during CSV import.

---

## Detection Logic

- **Email uniqueness**: `unique(company_id, email)` partial index (where email is not null) on `crm_contacts`.
- On create/import: if a contact with the same `(company_id, email)` exists, the form/import returns the message: `"A contact with this email already exists."`
- Cross-company: same email is allowed across different companies.

---

## Merge

`ContactService::merge(string $keepId, string $mergeId): ContactData`

- Reassigns activities, deals, and contact-account links from `mergeId` to `keepId`
- Soft-deletes the merged record
- Audited *(assumed)* — see [[../unknowns]] for the open question on audit method
- Requires `crm.contacts.merge` permission

---

## Import Deduplication

During CSV import (via `core.import`):
- `findOrCreateByEmail(string $email, array $attributes = []): ContactData` is the idempotent entry point
- Two calls with the same email return the same contact record (no duplicate created)
- Throttle/dedupe planned on event-listener contact creation to prevent spam records

---

## UI

- **Kind**: simple-resource — duplicate detection surfaces as an inline warning + merge action within `ContactResource`, not a standalone screen.
- **Page**: `ContactResource` create/edit form and list at `/crm/contacts`; merge is a row action on the list/view.
- **Layout**: standard resource form; on duplicate email the field shows an inline validation error. Merge opens a slide-over listing the duplicate pair with a "keep this / merge that" choice.
- **Key interactions**: inline validation on `email` blur/save; merge action modal (pick keep vs merge record) → confirm; CSV import shows a per-row dedupe summary.
- **States**: empty (no contacts yet) · loading (import progress bar) · error (`"A contact with this email already exists."`) · selected (merge pair highlighted before confirm).
- **Gating**: create/edit `crm.contacts.update`; merge `crm.contacts.merge`.

## Data

- Owns / writes: `crm_contacts`, `crm_accounts`, `crm_contact_accounts` (unique `(company_id, email)` partial index enforced here).
- Reads: none hard; import feeds rows via `core.import`.
- Cross-domain writes: none — merge reassigns only this module's own related links; other domains react to their own events ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing for detection itself.
- Feeds: merge reassigns `crm_deals` / `crm_activities` references via those modules' own relations, not by writing their tables.
- Shared entity: `crm_contacts` is the shared person record — support/events/marketing find-or-create via `ContactService::findOrCreateByEmail`, so idempotent dedup protects every domain.

## Test Checklist

### Unit
- [ ] `findOrCreateByEmail` is idempotent — two calls with the same email return one contact
- [ ] Same `(company_id, email)` is rejected; the same email across two companies is allowed

### Feature (Pest)
- [ ] `merge(keepId, mergeId)` reassigns activities / deals / contact-account links to `keepId` and soft-deletes the merged record, audited
- [ ] Merge requires `crm.contacts.merge`; denied for a user without it
- [ ] Concurrent merge/edit of the same record is serialised (pessimistic lock) — no lost reassignment
- [ ] CSV import dedupes per row and never creates a duplicate for an existing email

### Livewire
- [ ] Duplicate email shows the inline validation error `"A contact with this email already exists."` on the resource form
- [ ] Merge slide-over lists the duplicate pair and confirms keep-vs-merge; `canAccess` denies the action without `crm.contacts.merge`
