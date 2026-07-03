---
domain: legal
module: matter-management
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Matter Management — Security

## Access contract

`canAccess() = Auth::user()->can('legal.matters.view-any') && BillingService::hasModule('legal.matters')` per [[../../../architecture/filament-patterns]] #1.

## Confidentiality — second gate

`view-any` does **not** bypass confidential matters. A confidential matter is visible only to its owner and users in `access_list`, enforced centrally in `MatterService::accessibleFor`. Legal spend inherits this scope.

## Permissions

| Permission | Grants |
|---|---|
| `legal.matters.view-any` | List page (still filtered by confidentiality gate) |
| `legal.matters.view` | View a single matter (subject to confidentiality gate) |
| `legal.matters.create` | Open a matter |
| `legal.matters.update` | Edit matter, timeline events, confidentiality/access-list |
| `legal.matters.delete` | Soft-delete a matter |
| `legal.matters.change-status` *(assumed)* | Activate / put on hold / resume transitions |
| `legal.matters.close` | Close transition |

Verb-per-transition: `change-status` covers `open→active`, `active→on_hold`, `on_hold→active`; `close` covers
`active→closed` (both in the [[./architecture]] state machine). Seeded in `PermissionSeeder`.

## Rate Limiting

- Matter-document upload panel action (Media Library file write) applies the named `panel-action` rate limiter per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]].

## Upload hardening (medium — per [[../../../_archive/build-history/security-audit-2026-06-11]])

- Matter-document Media Library collection: allowed document types, max size, `companies/{id}/`-scoped path.

## Data ownership

Writes only `legal_matters`, `legal_matter_events`; all links read-only ([[../../../security/data-ownership]]).
