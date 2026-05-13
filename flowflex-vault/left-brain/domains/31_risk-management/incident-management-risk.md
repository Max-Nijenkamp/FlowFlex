---
type: module
domain: Enterprise Risk Management
panel: risk
module: Incident Management
phase: 5
status: complete
cssclasses: domain-risk
migration_range: 1152000–1152499
last_updated: 2026-05-12
---

# Incident Management

Operational incident recording, root cause analysis, loss tracking, and near-miss management. Distinct from IT helpdesk tickets — this captures business risk events that actually occurred.

---

## Key Tables

```sql
CREATE TABLE risk_incidents (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    incident_number VARCHAR(20) UNIQUE,    -- e.g. INC-2026-0014
    title           VARCHAR(255) NOT NULL,
    description     TEXT,
    category_id     ULID NULL REFERENCES risk_categories(id),
    related_risk_id ULID NULL REFERENCES risk_risks(id),
    type            ENUM('operational','financial','compliance','safety','reputational','near_miss'),
    severity        ENUM('minor','moderate','major','critical'),
    status          ENUM('open','investigating','resolved','closed'),
    occurred_at     TIMESTAMP NOT NULL,
    reported_by     ULID NOT NULL REFERENCES users(id),
    owner_id        ULID NULL REFERENCES users(id),
    -- Financial impact
    estimated_loss  DECIMAL(12,2) NULL,
    actual_loss     DECIMAL(12,2) NULL,
    loss_currency   CHAR(3) DEFAULT 'EUR',
    -- Regulatory
    is_reportable   BOOLEAN DEFAULT FALSE,  -- must be reported to regulator
    reported_to_authority_at TIMESTAMP NULL,
    -- Resolution
    root_cause      TEXT NULL,
    corrective_actions TEXT NULL,
    resolved_at     TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT NOW(),
    updated_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE risk_incident_timeline (
    id              ULID PRIMARY KEY,
    incident_id     ULID NOT NULL REFERENCES risk_incidents(id),
    recorded_by     ULID NOT NULL REFERENCES users(id),
    event           TEXT NOT NULL,
    event_time      TIMESTAMP NOT NULL,
    created_at      TIMESTAMP DEFAULT NOW()
);
```

---

## Incident Types

| Type | Examples |
|---|---|
| Operational | System outage, process failure, supplier failure |
| Financial | Fraud loss, write-off, financial error |
| Compliance | GDPR breach, regulatory fine, policy violation |
| Safety | Workplace accident, near-miss, equipment failure |
| Reputational | Press incident, social media crisis, product recall |
| Near Miss | Almost-incident — valuable for proactive risk management |

---

## Near-Miss Culture

Near-misses tracked as `type = near_miss` — no loss, but incident almost occurred.  
Near-misses linked to related risks → triggers risk review.  
Dashboard shows near-miss count vs actual incident count — high near-miss rate = good reporting culture.

---

## Regulatory Reporting

For `is_reportable = TRUE`:
- GDPR data breaches → 72-hour notification to supervisory authority
- Financial fraud → FIU (Financial Intelligence Unit) reporting
- Health & Safety → labour authority reporting

Deadline tracker with amber/red countdown badge in UI.

---

## Root Cause Analysis

5-Whys template built into incident detail page.  
`corrective_actions` field links to task assignments in Projects domain.  
Closed incident creates review in `risk_risk_reviews` for the related risk.

---

## Related

- [[MOC_RiskManagement]]
- [[risk-register]]
- [[MOC_Legal]] — reportable incidents
- [[MOC_IT]] — security incidents
