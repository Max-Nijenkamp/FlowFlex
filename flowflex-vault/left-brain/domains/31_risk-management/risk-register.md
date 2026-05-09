---
type: module
domain: Enterprise Risk Management
panel: risk
module: Risk Register
phase: 5
status: planned
cssclasses: domain-risk
migration_range: 1150000–1150499
last_updated: 2026-05-09
---

# Risk Register

Central inventory of all identified risks across the business. Each risk has an owner, category, inherent score, residual score (after controls), and review schedule.

---

## Key Tables

```sql
CREATE TABLE risk_categories (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    name            VARCHAR(100) NOT NULL,  -- e.g. "Operational", "Financial", "Cyber"
    parent_id       ULID NULL REFERENCES risk_categories(id),
    color           CHAR(7) NULL
);

CREATE TABLE risk_risks (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    risk_number     VARCHAR(20) UNIQUE,    -- e.g. RISK-2026-0042
    title           VARCHAR(255) NOT NULL,
    description     TEXT,
    category_id     ULID NOT NULL REFERENCES risk_categories(id),
    owner_id        ULID NOT NULL REFERENCES users(id),
    status          ENUM('open','under_review','mitigated','accepted','closed'),
    -- Inherent risk (before controls)
    inherent_impact      TINYINT CHECK (inherent_impact BETWEEN 1 AND 5),
    inherent_likelihood  TINYINT CHECK (inherent_likelihood BETWEEN 1 AND 5),
    inherent_score       TINYINT GENERATED ALWAYS AS (inherent_impact * inherent_likelihood) STORED,
    -- Residual risk (after controls)
    residual_impact      TINYINT CHECK (residual_impact BETWEEN 1 AND 5),
    residual_likelihood  TINYINT CHECK (residual_likelihood BETWEEN 1 AND 5),
    residual_score       TINYINT GENERATED ALWAYS AS (residual_impact * residual_likelihood) STORED,
    -- Target risk (desired state)
    target_score    TINYINT NULL,
    appetite        ENUM('avoid','reduce','transfer','accept'),
    treatment_plan  TEXT NULL,
    next_review_at  DATE,
    last_reviewed_at DATE NULL,
    tags            JSON NULL,
    created_at      TIMESTAMP DEFAULT NOW(),
    updated_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE risk_risk_reviews (
    id              ULID PRIMARY KEY,
    risk_id         ULID NOT NULL REFERENCES risk_risks(id),
    reviewed_by     ULID NOT NULL REFERENCES users(id),
    notes           TEXT,
    residual_impact     TINYINT NULL,
    residual_likelihood TINYINT NULL,
    status_change   VARCHAR(50) NULL,
    reviewed_at     TIMESTAMP DEFAULT NOW()
);
```

---

## Risk Scoring Matrix (5×5)

| Score | Severity |
|---|---|
| 1–4 | Low |
| 5–9 | Medium |
| 10–16 | High |
| 17–25 | Critical |

Score = Impact × Likelihood (each 1–5)

---

## Risk Categories (Default)

| Code | Category |
|---|---|
| OPR | Operational |
| FIN | Financial |
| CYB | Cyber & Technology |
| COM | Compliance & Legal |
| REP | Reputational |
| STR | Strategic |
| ESG | Environmental & Social |
| EXT | External / Macro |

---

## Review Reminders

`RiskReviewDueJob` runs weekly → notifies risk owners of overdue reviews.  
Risk owner can update scores, add treatment notes, change status.  
Review history immutable for audit trail.

---

## Related

- [[MOC_RiskManagement]]
- [[risk-assessments-rcsa]]
- [[controls-library]]
- [[heat-maps-risk-reporting]]
