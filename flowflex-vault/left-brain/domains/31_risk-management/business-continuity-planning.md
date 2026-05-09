---
type: module
domain: Enterprise Risk Management
panel: risk
module: Business Continuity Planning
phase: 6
status: planned
cssclasses: domain-risk
migration_range: 1152500–1152999
last_updated: 2026-05-09
---

# Business Continuity Planning

BCP and disaster recovery plan management, tabletop exercise coordination, RTO/RPO definition per business process, and plan activation workflows for actual incidents.

---

## Key Tables

```sql
CREATE TABLE risk_bcp_plans (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    name            VARCHAR(100) NOT NULL,
    version         VARCHAR(20) DEFAULT '1.0',
    scope           TEXT,
    owner_id        ULID NOT NULL REFERENCES users(id),
    status          ENUM('draft','active','archived','under_review'),
    approved_by     ULID NULL REFERENCES users(id),
    approved_at     TIMESTAMP NULL,
    next_review_at  DATE,
    dms_document_id ULID NULL,  -- links to DMS for full plan document
    created_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE risk_bcp_processes (
    id              ULID PRIMARY KEY,
    plan_id         ULID NOT NULL REFERENCES risk_bcp_plans(id),
    process_name    VARCHAR(255),
    criticality     ENUM('critical','high','medium','low'),
    rto_hours       INT NULL,     -- Recovery Time Objective
    rpo_hours       INT NULL,     -- Recovery Point Objective
    mto_hours       INT NULL,     -- Maximum Tolerable Outage
    dependencies    TEXT NULL,
    recovery_steps  TEXT NULL,
    responsible_id  ULID NULL REFERENCES users(id)
);

CREATE TABLE risk_bcp_exercises (
    id              ULID PRIMARY KEY,
    plan_id         ULID NOT NULL REFERENCES risk_bcp_plans(id),
    type            ENUM('tabletop','walkthrough','simulation','full_test'),
    scenario        TEXT,
    scheduled_at    DATE,
    conducted_at    DATE NULL,
    facilitator_id  ULID NOT NULL REFERENCES users(id),
    outcome         ENUM('pass','fail','partial') NULL,
    findings        TEXT NULL,
    improvements    TEXT NULL,
    report_path     VARCHAR(500) NULL
);

CREATE TABLE risk_bcp_activations (
    id              ULID PRIMARY KEY,
    plan_id         ULID NOT NULL REFERENCES risk_bcp_plans(id),
    incident_id     ULID NULL REFERENCES risk_incidents(id),
    activated_by    ULID NOT NULL REFERENCES users(id),
    activated_at    TIMESTAMP NOT NULL,
    deactivated_at  TIMESTAMP NULL,
    notes           TEXT NULL
);
```

---

## RTO / RPO Tracking

| Criticality | Typical RTO | Typical RPO |
|---|---|---|
| Critical | < 4 hours | < 1 hour |
| High | < 24 hours | < 4 hours |
| Medium | < 72 hours | < 24 hours |
| Low | < 1 week | < 72 hours |

Process owners define their RTO/RPO during BIA (Business Impact Analysis).  
IT aligns backup schedules and failover capacity to meet defined RPO.

---

## Plan Activation

Activation triggered manually by authorised user (or automatically from `risk_incidents` severity = critical).  
Activated plan sends notification to all process owners with their recovery steps.  
Deactivation records actual recovery time → compared against RTO to identify gaps.

---

## Related

- [[MOC_RiskManagement]]
- [[incident-management-risk]]
- [[risk-register]]
- [[MOC_IT]] — IT DR plans
