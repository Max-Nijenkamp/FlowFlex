---
domain: marketing
module: forms
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Forms — Architecture

Parent: [[_module]]

## Services & Actions

| Class | Signature | Responsibility |
|---|---|---|
| `FormService::submit` | `submit(slug, values, ip): void` | Validate values against the form definition; drop if honeypot filled; store `mkt_form_submissions`; fire `FormSubmissionReceived`; (soft) enrol in sequence; notify users. |
| Embed endpoint | `PublicFormController` | Serves cached form-definition JSON + JS renderer for iframe/snippet embeds; hosted page at `/f/{slug}`. |

## Events

Fires `FormSubmissionReceived` on each successful submit. Consumers: CRM (find-or-create contact), sequences (enrol), UTM (record touch). Consumes none. See [[../../../architecture/event-bus]].

## Filament Artifacts

**Nav group:** Forms

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `FormResource` | #1 CRUD resource | tweaks: inline-relation-repeater (fields), relation-manager-timeline (submissions tab) *(assumed)* | embed-code copy box; active toggle; submit-action panel |
| `FormSubmissionResource` | #1 CRUD resource | tweaks: read-only-flow-owned (rows written by the public submit path), custom-header-actions (export) | export gated on `marketing.forms.view-submissions` + names the `exports` limiter ([[./security]]) |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('marketing.forms.view-any') && BillingService::hasModule('marketing.forms')`
per [[../../../architecture/filament-patterns]] #1. This module has no custom Filament pages. The hosted form page (`/f/{slug}`, Vue + Inertia, ui-strategy row #16) and the embed JS renderer are public/guest surfaces resolving company **by slug** — no session — with honeypot + throttle + allowed-origin handling ([[./security]]), not Filament artifacts.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Form CRUD (builder form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Public submission insert (`POST /f/{slug}`) | n/a | Append-only public insert — one row per submission, no concurrent editor of the same row |
| `mkt_forms.view_count` increment | n/a | Atomic `increment()` (single `UPDATE`), concurrency-safe under many public views without a lock |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Public route

`POST /f/{slug}` — no auth, resolves company by slug, CSRF-exempt with allowed-origin handling for cross-site embeds, per-IP throttle. See [[security]].

## Related

- [[_module]] · [[data-model]] · [[../../../architecture/event-bus]] · [[../../../architecture/security]]
