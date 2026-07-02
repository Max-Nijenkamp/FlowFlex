---
domain: workplace
module: visitor-management
feature: pre-registration
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Pre-registration

A host registers an expected visitor with a date/time before they arrive.

## Behaviour

- Host fills `PreRegisterVisitorData` (name, company, email?, expected_at, purpose).
- A confirmation mail is sent to the visitor *(assumed)*.
- Recurring visitors (contractors) can be re-registered from history.
- Name + email stored encrypted.

## UI

- **Kind**: simple-resource
- **Page**: `VisitorResource` create/edit at `/workplace/visitors`.
- **Layout**: table (visitor, company, host, expected_at, status); section form for the visitor fields; "pre-register" primary action.
- **Key interactions**: create expected visitor → confirmation mail dispatched; re-register from a past record.
- **States**: empty (no visitors → "pre-register your first visitor" CTA) · loading (table skeleton) · error (toast) · selected (row → edit).
- **Gating**: `workplace.visitors.pre-register` (all users); manage others' via `workplace.visitors.manage`.

## Data

- Owns / writes: `wp_visitors` only (encrypted `name` / `email`).
- Reads: `hr.profiles` to resolve the host (read-only).
- Cross-domain writes: none — confirmation mail via `foundation.email` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: expected-visitor rows drive the kiosk lookup ([[check-in]]).
- Shared entity: `hr_employees` (host) — owned by [[../../../hr/employee-profiles/_module|hr.profiles]], read-only.

## Test Checklist

### Unit
- [ ] `PreRegisterVisitorData` validates name/expected_at; email optional

### Feature (Pest)
- [ ] Pre-register stores encrypted `name`/`email` (plaintext absent from DB dump) + dispatches confirmation mail (faked)
- [ ] Re-register from history copies visitor fields with a new `expected_at`
- [ ] Host resolves read-only from hr.profiles

### Livewire
- [ ] Create form validates; denied without `workplace.visitors.pre-register`

## Related

- [[../_module|Visitor Management]] · [[check-in]] · [[../api]]
