---
domain: marketing
module: campaigns
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Campaigns — Security

Parent: [[_module]]

Bulk outbound email + three public token endpoints — the highest-exposure surface in `/marketing`.

## Permissions

`marketing.campaigns.view-any` · `marketing.campaigns.create` · `marketing.campaigns.send`. Send is separated from create so a marketer can draft without send rights. Resources/widgets gate on `canAccess()` ([[../../../architecture/patterns/policy]], [[../../../security/authn-authz]]).

## Public token endpoints (HIGH)

`TrackOpenController`, `TrackClickController`, `UnsubscribeController` run **outside the Sanctum session guard**. Each URL carries a **signed route or per-recipient opaque token**; the token resolves company + recipient, never the session. No token → 404. Documented token scheme required before build ([[../../../security/webhooks-signing]] for the analogous signing pattern).

## Rate limiting (medium)

Public Track/Unsubscribe routes carry a throttle (per-IP or per-token) to blunt pixel/scan abuse. See [[../../../architecture/security]].

## Suppression & consent

- Every marketing send injects a working unsubscribe footer; sending without one is a defect.
- `mkt_unsubscribes` is enforced at **materialisation** (schedule time) and again defensively at send — a suppressed or undeliverable address never receives a send.
- Marketing consent state per contact is a known gap — see [[unknowns]] and [[../_opportunities]] (GDPR consent ledger).

## Tenant scoping

All three tables carry `company_id`; recipients + unsubscribes are company-scoped. Public token endpoints resolve company from the token only — the tenant boundary holds without a session ([[../../../security/tenancy-isolation]]).

## Data ownership

Writes only its own three tables. Audiences read from CRM via `SegmentService` — never a CRM write ([[../../../security/data-ownership]]).

## Related

- [[_module]] · [[data-model]] · [[api]] · [[../../../security/authn-authz]] · [[../../../architecture/security]]
