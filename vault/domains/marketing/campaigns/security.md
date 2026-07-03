---
domain: marketing
module: campaigns
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Campaigns — Security

Parent: [[_module]]

Bulk outbound email + three public token endpoints — the highest-exposure surface in `/marketing`.

## Permissions

| Permission | Grants |
|---|---|
| `marketing.campaigns.view-any` | Campaign list + stats widget |
| `marketing.campaigns.create` | Draft a campaign |
| `marketing.campaigns.update` | Edit a draft campaign |
| `marketing.campaigns.delete` | Soft-delete a campaign |
| `marketing.campaigns.send` | Trigger `draft → scheduled` (schedule / send-now) **and** test-send |

Send is separated from create so a marketer can draft without send rights. The `scheduled → sending → sent`/`failed` transitions are system-driven (scheduler + batch job), not user permissions. Seeded in `PermissionSeeder`. Resources/widgets gate on `canAccess()` ([[../../../architecture/patterns/policy]], [[../../../security/authn-authz]]).

**Verb-per-command check:** the only user-triggered state transition is `send` (covers schedule / send-now); test-send reuses `marketing.campaigns.send`. Both are covered above.

## Public token endpoints (HIGH)

`TrackOpenController`, `TrackClickController`, `UnsubscribeController` run **outside the Sanctum session guard**. Each URL carries a **signed route or per-recipient opaque token**; the token resolves company + recipient, never the session. No token → 404. Documented token scheme required before build ([[../../../security/webhooks-signing]] for the analogous signing pattern).

## Rate limiting

| Action | Category | Limiter |
|---|---|---|
| Send / schedule campaign (panel action) | sends comms (bulk) | `panel-action` ([[../../../architecture/security]]) |
| Test-send (panel action) | sends comms | `panel-action` |
| Public Track / Click / Unsubscribe endpoints | public token endpoint | `api` *(assumed — no dedicated public-endpoint limiter exists yet; per-IP/per-token throttle to blunt pixel/scan abuse is an open reconciliation item, see [[unknowns]])* |

The batched `SendCampaignBatchJob` is itself throttled at the queue/transport layer ([[../../../architecture/queue-jobs]], [[../../../architecture/email]]); the `panel-action` limiter guards the user-facing trigger. See [[../../../architecture/security]].

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
