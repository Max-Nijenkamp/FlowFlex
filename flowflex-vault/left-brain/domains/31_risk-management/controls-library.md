---
type: module
domain: Enterprise Risk Management
panel: risk
module: Controls Library
phase: 5
status: complete
cssclasses: domain-risk
migration_range: 1151000–1151499
last_updated: 2026-05-12
---

# Controls Library

Central catalogue of all internal controls mapped to risks. Tracks control design effectiveness and operating effectiveness testing. Feeds into residual risk scoring.

---

## Key Tables

```sql
CREATE TABLE risk_controls (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    control_number  VARCHAR(20) UNIQUE,    -- e.g. CTRL-FIN-042
    title           VARCHAR(255) NOT NULL,
    description     TEXT,
    category        ENUM('preventive','detective','corrective','directive'),
    type            ENUM('manual','automated','it_dependent'),
    frequency       ENUM('continuous','daily','weekly','monthly','quarterly','annual','ad_hoc'),
    owner_id        ULID NOT NULL REFERENCES users(id),
    status          ENUM('active','inactive','under_review'),
    design_effectiveness ENUM('effective','partially_effective','ineffective') NULL,
    operating_effectiveness ENUM('effective','partially_effective','ineffective') NULL,
    last_tested_at  DATE NULL,
    next_test_at    DATE NULL,
    framework_refs  JSON NULL,  -- [{framework: "ISO27001", control: "A.9.1.1"}]
    created_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE risk_control_risk_mappings (
    id              ULID PRIMARY KEY,
    control_id      ULID NOT NULL REFERENCES risk_controls(id),
    risk_id         ULID NOT NULL REFERENCES risk_risks(id),
    reduction_score TINYINT NULL,  -- how much this control reduces risk score
    UNIQUE(control_id, risk_id)
);

CREATE TABLE risk_control_tests (
    id              ULID PRIMARY KEY,
    control_id      ULID NOT NULL REFERENCES risk_controls(id),
    tester_id       ULID NOT NULL REFERENCES users(id),
    test_date       DATE NOT NULL,
    result          ENUM('pass','pass_with_exceptions','fail'),
    exceptions      TEXT NULL,
    evidence_path   VARCHAR(500) NULL,     -- link to DMS document
    remediation_due DATE NULL,
    remediation_notes TEXT NULL,
    created_at      TIMESTAMP DEFAULT NOW()
);
```

---

## Control Categories

| Type | Description |
|---|---|
| Preventive | Stops the risk from occurring (e.g. access controls, approvals) |
| Detective | Identifies when risk has occurred (e.g. reconciliations, audits) |
| Corrective | Fixes issues after detection (e.g. backup restoration, rollback) |
| Directive | Guides correct behaviour (e.g. policies, training) |

---

## Framework Mapping

Controls tagged to compliance frameworks:
- ISO 27001 control references (e.g. A.9.1.1)
- SOX financial controls
- NIST Cybersecurity Framework
- GDPR technical/organisational measures

When IT domain raises security audit evidence, links to relevant controls.

---

## Operating Effectiveness Testing

Scheduled tests: `ControlTestDueJob` notifies tester N days before `next_test_at`.  
Failed test: `ControlTestFailed` event → notifies control owner, risk manager, IT (if security).  
Failed control → risk residual score auto-recalculated upward.

---

## Related

- [[MOC_RiskManagement]]
- [[risk-register]]
- [[risk-assessments-rcsa]]
- [[MOC_IT]] — security controls
- [[MOC_Legal]] — compliance evidence
