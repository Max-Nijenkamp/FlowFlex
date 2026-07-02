---
domain: customer-success
module: nps
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# NPS — DTOs & API

## DTOs

### CreateNpsSurveyData (input)

| Field | Type | Validation |
|---|---|---|
| name | string | required |
| audience | array | required; exactly one of `{segment_id}` (exists, tenant-scoped) or `{account_ids: []}` (min 1, exist) |

### NpsResponseData (public input)

| Field | Type | Validation |
|---|---|---|
| token | uuid | required; exists, **unanswered** (`score IS NULL`), tenant resolved from token |
| score | int | required; 0–10 |
| comment | string | nullable |

Rate-limited at the controller boundary; token is single-use.

### NpsResponseResult (output)

`account_id`, `contact_id`, `score`, `category`, `comment`, `responded_at`

---

## Internal Read API

`NpsService::latestForAccount(accountId)` — the latest answered response per account, pulled by `cs.health` for its sentiment factor and by `cs.analytics` for NPS trend. In-process, tenant-scoped; no HTTP.

---

## Public / Portal Endpoints

| Route | Method | Guard | Notes |
|---|---|---|---|
| `/nps/{token}` | GET | none (public) | Renders `Respond.vue` if token valid + unanswered; else a "already responded / expired" state |
| `/nps/{token}` | POST | none (public) | `RecordNpsResponseAction`; token-scoped, single-use, rate-limited |

No Sanctum session; the tenant is derived from the token, not from an authenticated user. Hardening detailed in [[./security]].
