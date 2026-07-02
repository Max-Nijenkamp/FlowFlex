---
domain: support
module: support-analytics
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Support Analytics — DTOs & API

## DTOs

### CsatResponseData (public input)

| Field | Type | Validation |
|---|---|---|
| token | string | valid, unanswered |
| rating | int | 1–5 |
| comment | ?string | nullable |

Rate-limited.

### SupportMetricsData (output)

Period series (created vs resolved), first-response/resolution averages, breakdowns (category/priority/channel), agent-performance table, SLA compliance (when SLA active), backlog trend, busy-hours heat-map.

---

## Public / Portal Endpoints

Token-only guard (no panel session), rate-limited:

| Route | Purpose |
|---|---|
| `GET /csat/{token}` | CSAT response page (Vue + Inertia, `CsatController`) |
| `POST /csat/{token}` | `RecordCsatAction` — one response per token/ticket |

See [[./security]].
