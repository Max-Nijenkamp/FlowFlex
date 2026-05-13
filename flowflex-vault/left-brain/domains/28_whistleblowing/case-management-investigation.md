---
type: module
domain: Whistleblowing & Ethics Hotline
panel: whistleblowing
module: Case Management & Investigation
phase: 4
status: complete
cssclasses: domain-whistleblowing
migration_range: 1000500–1000999
last_updated: 2026-05-12
---

# Case Management & Investigation

Internal workflow for managing ethics reports from intake through investigation to resolution. Includes investigator assignment, evidence management, status tracking, and audit log.

---

## Key Tables

```sql
CREATE TABLE ethics_cases (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    report_id       ULID NOT NULL REFERENCES ethics_reports(id),
    case_number     VARCHAR(20) UNIQUE,  -- e.g. ETH-2026-0042
    status          ENUM('received','acknowledged','in_review','escalated','resolved','closed','declined'),
    priority        ENUM('low','medium','high','critical') DEFAULT 'medium',
    assigned_to     ULID NULL REFERENCES users(id),
    acknowledged_at TIMESTAMP NULL,
    resolved_at     TIMESTAMP NULL,
    resolution      ENUM('substantiated','unsubstantiated','inconclusive','outside_scope') NULL,
    resolution_notes TEXT NULL,
    confidential_notes TEXT NULL,  -- never shown to reporter
    sla_acknowledge_at TIMESTAMP,  -- must acknowledge within 7 days (EU Directive)
    sla_resolve_at     TIMESTAMP,  -- must resolve within 3 months (EU Directive)
    created_at      TIMESTAMP DEFAULT NOW(),
    updated_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE ethics_case_investigators (
    id          ULID PRIMARY KEY,
    case_id     ULID NOT NULL REFERENCES ethics_cases(id),
    user_id     ULID NOT NULL REFERENCES users(id),
    role        ENUM('lead','support','external','legal_counsel'),
    assigned_at TIMESTAMP DEFAULT NOW()
);

CREATE TABLE ethics_case_activities (
    id          ULID PRIMARY KEY,
    case_id     ULID NOT NULL REFERENCES ethics_cases(id),
    user_id     ULID NOT NULL REFERENCES users(id),
    activity    TEXT NOT NULL,
    activity_type ENUM('note','status_change','assignment','escalation','message','evidence'),
    metadata    JSON NULL,
    created_at  TIMESTAMP DEFAULT NOW()
);

CREATE TABLE ethics_case_evidence (
    id          ULID PRIMARY KEY,
    case_id     ULID NOT NULL REFERENCES ethics_cases(id),
    added_by    ULID NOT NULL REFERENCES users(id),
    source      ENUM('reporter_upload','investigator','external','interview'),
    description TEXT,
    storage_path VARCHAR(500) NULL,
    created_at  TIMESTAMP DEFAULT NOW()
);
```

---

## Case Lifecycle

```
RECEIVED ──(7 days)──► ACKNOWLEDGED ──► IN_REVIEW
                                              │
                                    ┌─────────┼─────────┐
                                    │         │         │
                               ESCALATED   RESOLVED   DECLINED
                                    │         │
                                    └────►  CLOSED
```

EU Directive SLA timers:
- **Acknowledge**: 7 calendar days from receipt
- **Resolve**: 3 months (extendable to 6 months with notice)

---

## Permissions (Role Isolation)

```
ethics.cases.view     — case managers can see case list
ethics.cases.manage   — assign investigators, change status
ethics.cases.notes    — add confidential investigation notes
ethics.reports.view   — see raw report text (senior only)
ethics.cases.escalate — escalate to legal / external authority
```

Reporter can NEVER be linked to a user account. Investigator cannot see other investigators' confidential notes unless `ethics.cases.senior`.

---

## Filament Resources

- `EthicsCaseResource` — table with status filter, SLA badge (green/amber/red), assign action
- `CaseInvestigationPage` — custom page: timeline, evidence list, send-message button, resolution form
- SLA widget on dashboard: overdue cases count, approaching SLA count

---

## Related

- [[MOC_Whistleblowing]]
- [[anonymous-intake-portal]]
- [[eu-whistleblower-directive-compliance]]
- [[reporter-communication-portal]]
