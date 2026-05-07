---
tags: [flowflex, domain/legal, risk, compliance, phase/7]
domain: Legal
panel: legal
color: "#DC2626"
status: planned
last_updated: 2026-05-07
---

# Risk Register

Identify, score, assign, and mitigate business risks. Board-ready risk heat maps and PDF packs — separate from the HSE operational risk assessments module.

**Who uses it:** Legal team, compliance officer, senior management, board
**Filament Panel:** `legal`
**Depends on:** Core
**Phase:** 7
**Build complexity:** Medium — 3 resources, 1 page, 3 tables

---

## Features

- **Risk identification** — create risks with title, description, and category (operational/financial/legal/reputational/strategic)
- **Risk scoring** — likelihood (unlikely/possible/likely/almost_certain) × consequence (minor/moderate/major/catastrophic); `risk_score` computed automatically as integer 1–16
- **Risk heat map** — 4×4 matrix visualisation in the `legal` panel; colour-coded by score (green/amber/red/dark red)
- **Risk ownership** — assign a `owner_id` tenant per risk; owner receives notifications and is responsible for mitigation
- **Status workflow** — open → mitigated → accepted → closed; accepted risks require a rationale note
- **Review cycle** — `review_date` per risk; `RiskFlagRaised` event fires when review date passes without the risk being updated
- **Mitigation actions** — linked to each risk in `risk_mitigations`; each action has an owner, due date, and status (planned/in_progress/completed)
- **Risk reviews** — formal periodic review records in `risk_reviews`; store new score after review with reviewer notes; show score trend over time
- **`RiskFlagRaised` event** — fires when a new high/critical risk is created, or when review date is overdue; notifies risk owner and legal team
- **Board report export** — generate a formatted PDF heat map + risk table for board packs; filtered to active risks above a configurable threshold
- **Audit trail** — all risk score changes and status updates logged via `LogsActivity`

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `legal_risks`
| Column | Type | Notes |
|---|---|---|
| `title` | string | |
| `description` | text | |
| `category` | enum | `operational`, `financial`, `legal`, `reputational`, `strategic` |
| `likelihood` | enum | `unlikely`, `possible`, `likely`, `almost_certain` |
| `consequence` | enum | `minor`, `moderate`, `major`, `catastrophic` |
| `risk_score` | integer | computed: likelihood_int × consequence_int |
| `status` | enum | `open`, `mitigated`, `accepted`, `closed` |
| `acceptance_rationale` | text nullable | required when status = accepted |
| `owner_id` | ulid FK nullable | → tenants |
| `review_date` | date nullable | |

### `risk_mitigations`
| Column | Type | Notes |
|---|---|---|
| `legal_risk_id` | ulid FK | → legal_risks |
| `description` | text | |
| `action_owner_id` | ulid FK nullable | → tenants |
| `due_date` | date nullable | |
| `status` | enum | `planned`, `in_progress`, `completed` |
| `completed_at` | timestamp nullable | |

### `risk_reviews`
| Column | Type | Notes |
|---|---|---|
| `legal_risk_id` | ulid FK | → legal_risks |
| `reviewed_by` | ulid FK | → tenants |
| `reviewed_at` | timestamp | |
| `new_score` | integer nullable | risk score after review |
| `new_likelihood` | enum nullable | |
| `new_consequence` | enum nullable | |
| `notes` | text nullable | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `RiskFlagRaised` | `legal_risk_id`, `risk_score` | Notification to risk owner and legal team |

---

## Events Consumed

None — risks are managed manually by the legal/compliance team.

---

## Permissions

```
legal.legal-risks.view
legal.legal-risks.create
legal.legal-risks.edit
legal.legal-risks.delete
legal.legal-risks.close
legal.risk-mitigations.view
legal.risk-mitigations.create
legal.risk-mitigations.edit
legal.risk-mitigations.complete
legal.risk-reviews.view
legal.risk-reviews.create
legal.risk-register.export
```

---

## Related

- [[Legal Overview]]
- [[Security & Compliance]]
- [[HSE]]
- [[Notifications & Alerts]]
