---
tags: [flowflex, domain/operations, hse, health-safety, phase/4]
domain: Operations
panel: operations
color: "#D97706"
status: planned
last_updated: 2026-05-07
---

# Health, Safety & Environment (HSE)

Incident reporting, risk assessments, and safety compliance — all on mobile. Capture near-misses before they become accidents, and build an evidence base for audits.

**Who uses it:** All staff (incident/near-miss reporting), HSE managers, operations managers, HR
**Filament Panel:** `operations`
**Depends on:** Core, [[HR — Employee Profiles]]
**Phase:** 4
**Build complexity:** High — 4 resources, 2 pages, 4 tables

---

## Features

- **Incident reporting** — mobile and desktop form for all incident types: near miss, injury, illness, property damage, environmental; available to all tenants regardless of role
- **Near-miss culture** — low-barrier near-miss reporting form (3 fields) to encourage proactive safety reporting
- **Severity classification** — incidents classified as low/medium/high/critical; critical incidents trigger immediate multi-manager alert
- **Incident investigation workflow** — assign an investigator, document root cause and corrective actions, mark investigation complete
- **Operational risk assessments** — separate from Legal risk register; focused on area/activity safety risks with likelihood × consequence scoring
- **Risk score matrix** — computed `risk_score` (likelihood × consequence); colour-coded red/amber/green; auto-flag high scores for review
- **Safety observations** — positive and negative safety observations logged by any employee; feed into safety culture KPIs
- **RIDDOR report generation** — auto-formatted UK RIDDOR report for qualifying incidents (injury requiring 7+ day absence); export as PDF
- **Hazmat / COSHH register** — chemical substance register with risk assessment and handling instructions per substance
- **Toolbox talk logging** — record safety briefing attendance; link to relevant risk assessment or policy
- **Safety induction sign-off** — new employee safety induction checklist; mark complete per employee
- **Dashboard KPIs** — LTIFR (Lost Time Injury Frequency Rate), near-miss to incident ratio, open investigations count, risk assessment review due count
- **Notification escalation** — `IncidentReported` notifies HSE manager; `CriticalIncidentRaised` notifies all managers and HR

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `incidents`
| Column | Type | Notes |
|---|---|---|
| `title` | string | |
| `description` | text | |
| `type` | enum | `near_miss`, `injury`, `illness`, `property_damage`, `environmental` |
| `severity` | enum | `low`, `medium`, `high`, `critical` |
| `tenant_id` | ulid FK | who reported → tenants |
| `injured_tenant_id` | ulid FK nullable | → tenants (if injury/illness) |
| `location` | string | |
| `occurred_at` | timestamp | |
| `reported_at` | timestamp | |
| `status` | enum | `reported`, `under_investigation`, `resolved` |
| `witnesses` | text nullable | |
| `is_riddor_reportable` | boolean default false | |
| `riddor_exported_at` | timestamp nullable | |

### `risk_assessments_hse`
| Column | Type | Notes |
|---|---|---|
| `title` | string | |
| `area` | string | physical location or department |
| `activity` | string | task or work activity being assessed |
| `hazards` | text | identified hazards |
| `likelihood` | integer | 1-5 score |
| `consequence` | integer | 1-5 score |
| `risk_score` | integer | computed: likelihood × consequence |
| `controls` | text | control measures in place |
| `residual_likelihood` | integer nullable | after controls |
| `residual_consequence` | integer nullable | after controls |
| `residual_risk_score` | integer nullable | |
| `review_date` | date nullable | |
| `tenant_id` | ulid FK | owner → tenants |
| `is_active` | boolean default true | |

### `safety_observations`
| Column | Type | Notes |
|---|---|---|
| `tenant_id` | ulid FK | observer → tenants |
| `type` | enum | `safe`, `unsafe` |
| `description` | text | |
| `location` | string | |
| `observed_at` | timestamp | |
| `status` | enum | `open`, `actioned`, `closed` |
| `action_taken` | text nullable | |
| `actioned_by` | ulid FK nullable | → tenants |
| `actioned_at` | timestamp nullable | |

### `incident_investigations`
| Column | Type | Notes |
|---|---|---|
| `incident_id` | ulid FK | → incidents |
| `investigator_id` | ulid FK | → tenants |
| `root_cause` | text nullable | |
| `contributing_factors` | text nullable | |
| `corrective_actions` | text nullable | |
| `preventive_actions` | text nullable | |
| `started_at` | timestamp nullable | |
| `completed_at` | timestamp nullable | |
| `status` | enum | `in_progress`, `completed` |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `IncidentReported` | `incident_id`, `severity` | Notification to HSE manager |
| `CriticalIncidentRaised` | `incident_id` | Notification to all managers and HR manager |

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `EmployeeHired` | [[HR — Employee Profiles]] | Creates safety induction checklist item for new employee |

---

## Permissions

```
operations.incidents.view
operations.incidents.create
operations.incidents.edit
operations.incidents.delete
operations.incidents.investigate
operations.risk-assessments-hse.view
operations.risk-assessments-hse.create
operations.risk-assessments-hse.edit
operations.risk-assessments-hse.delete
operations.safety-observations.view
operations.safety-observations.create
operations.safety-observations.action
operations.incident-investigations.view
operations.incident-investigations.create
operations.incident-investigations.complete
```

---

## Related

- [[Operations Overview]]
- [[Quality Control & Inspections]]
- [[HR Compliance]]
- [[HR — Employee Profiles]]
- [[Risk Register]]
