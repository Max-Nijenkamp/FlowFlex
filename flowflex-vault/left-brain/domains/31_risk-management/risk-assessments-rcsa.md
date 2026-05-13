---
type: module
domain: Enterprise Risk Management
panel: risk
module: Risk Assessments & RCSA
phase: 5
status: complete
cssclasses: domain-risk
migration_range: 1150500–1150999
last_updated: 2026-05-12
---

# Risk Assessments & RCSA

Risk & Control Self-Assessment (RCSA) workflow. Periodic structured review of business processes to identify new risks and reassess existing ones. Collaborative — department heads self-assess, risk manager reviews.

---

## Key Tables

```sql
CREATE TABLE risk_assessment_cycles (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    name            VARCHAR(100),        -- e.g. "Q2 2026 RCSA"
    type            ENUM('rcsa','process','project','vendor','annual'),
    period_start    DATE,
    period_end      DATE,
    status          ENUM('draft','in_progress','review','completed'),
    facilitator_id  ULID NOT NULL REFERENCES users(id),
    due_date        DATE,
    created_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE risk_assessment_participants (
    id              ULID PRIMARY KEY,
    cycle_id        ULID NOT NULL REFERENCES risk_assessment_cycles(id),
    user_id         ULID NOT NULL REFERENCES users(id),
    department      VARCHAR(100) NULL,
    status          ENUM('invited','in_progress','submitted','reviewed'),
    submitted_at    TIMESTAMP NULL
);

CREATE TABLE risk_assessment_responses (
    id              ULID PRIMARY KEY,
    cycle_id        ULID NOT NULL REFERENCES risk_assessment_cycles(id),
    participant_id  ULID NOT NULL REFERENCES risk_assessment_participants(id),
    risk_id         ULID NULL REFERENCES risk_risks(id),  -- NULL if new risk identified
    is_new_risk     BOOLEAN DEFAULT FALSE,
    new_risk_title  VARCHAR(255) NULL,
    new_risk_desc   TEXT NULL,
    assessed_impact     TINYINT,
    assessed_likelihood TINYINT,
    existing_controls   TEXT NULL,
    gaps_identified     TEXT NULL,
    treatment_suggestion TEXT NULL,
    created_at      TIMESTAMP DEFAULT NOW()
);
```

---

## RCSA Process

1. Risk Manager creates assessment cycle, sets due date
2. Participants (department heads) invited via email
3. Each participant reviews their process risks:
   - For known risks: update impact/likelihood assessment
   - New risks: describe and score
   - For each risk: describe existing controls, identify gaps
4. Risk Manager reviews all submissions → consolidates into risk register
5. New risks auto-created in `risk_risks`, existing risks updated
6. Cycle closed, summary report generated

---

## Completion Tracking

Dashboard shows: N/N participants submitted, overdue participants highlighted.  
Auto-reminder email 7 days and 1 day before deadline.  
Risk Manager can reassign participants or extend deadline.

---

## Integration with Risk Register

On cycle completion:
- New risks created in `risk_risks` (status = `open`)
- Existing risk scores updated (creates new `risk_risk_reviews` record)
- Risk owner notified of updated score

---

## Related

- [[MOC_RiskManagement]]
- [[risk-register]]
- [[controls-library]]
- [[heat-maps-risk-reporting]]
