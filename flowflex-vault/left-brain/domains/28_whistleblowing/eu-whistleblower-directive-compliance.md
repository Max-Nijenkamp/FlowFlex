---
type: module
domain: Whistleblowing & Ethics Hotline
panel: whistleblowing
module: EU Directive Compliance
phase: 4
status: planned
cssclasses: domain-whistleblowing
migration_range: 1001000–1001499
last_updated: 2026-05-09
---

# EU Whistleblower Directive Compliance

Automated compliance checks for EU Directive 2019/1937: SLA enforcement, data retention policies, mandatory reporting to national authorities, and compliance audit trail.

---

## Directive Requirements Mapped to Features

| Directive Requirement | FlowFlex Implementation |
|---|---|
| Confidential reporting channel | Anonymous Intake Portal (no IP, no session tracking) |
| Acknowledge within 7 days | SLA timer on ethics_cases, escalation if breached |
| Respond/resolve within 3 months | SLA timer, extendable to 6 months with audit log |
| Prohibit retaliation | Retaliation report flag on ethics_cases |
| Annual report to competent authority | Auto-generated statistics report (count by category, resolution) |
| Data retention ≤ 5 years | Automated purge schedule, right-to-erasure workflow |
| Reporter identity protection | Architecture: no IP stored, token-only access |
| External reporting fallback | Links to national authority URLs per country |

---

## Key Tables

```sql
CREATE TABLE ethics_compliance_settings (
    id              ULID PRIMARY KEY,
    company_id      ULID UNIQUE NOT NULL REFERENCES companies(id),
    acknowledge_sla_days   INT DEFAULT 7,
    resolve_sla_days       INT DEFAULT 90,   -- 3 months
    resolve_sla_extended   INT DEFAULT 180,  -- 6 months if extended
    retention_years        INT DEFAULT 5,
    reporting_authority    VARCHAR(255) NULL, -- national authority URL
    annual_report_due_date DATE NULL,
    last_annual_report_at  TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT NOW(),
    updated_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE ethics_annual_reports (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    report_year     INT NOT NULL,
    period_start    DATE NOT NULL,
    period_end      DATE NOT NULL,
    total_reports   INT DEFAULT 0,
    by_category     JSON,   -- {fraud: 3, harassment: 2, ...}
    by_resolution   JSON,   -- {substantiated: 2, unsubstantiated: 3}
    average_days_to_resolve DECIMAL(5,1),
    submitted_to_authority  BOOLEAN DEFAULT FALSE,
    submitted_at    TIMESTAMP NULL,
    generated_at    TIMESTAMP DEFAULT NOW()
);

CREATE TABLE ethics_retaliation_flags (
    id          ULID PRIMARY KEY,
    case_id     ULID NOT NULL REFERENCES ethics_cases(id),
    description TEXT NOT NULL,
    reported_at TIMESTAMP DEFAULT NOW()
);
```

---

## Country-Specific Authority Links

| Country | Authority | URL |
|---|---|---|
| Netherlands | Huis voor Klokkenluiders | `https://www.huisvoorklokkenluiders.nl` |
| Germany | BAG / BaFin (sector specific) | Varies |
| France | Défenseur des droits | `https://www.defenseurdesdroits.fr` |
| Belgium | Institute for the Equality of Women and Men (for harassment) | Varies |
| EU general | European Anti-Fraud Office (OLAF) | `https://anti-fraud.ec.europa.eu` |

---

## Automated Compliance Jobs

```php
// Scheduled daily
EthicsComplianceCheckJob::dispatch();
// Checks:
// 1. Cases not acknowledged within SLA → escalate + notify
// 2. Cases not resolved within SLA → notify senior + flag
// 3. Reports older than retention_years → queue for deletion review
// 4. Annual report due within 30 days → notify compliance officer
```

---

## Audit Trail

All case activities are immutable (append-only `ethics_case_activities`).  
SLA breaches logged with exact timestamp. Cannot be deleted by admins.  
Annual compliance report auto-generated from immutable data.

---

## Related

- [[MOC_Whistleblowing]]
- [[case-management-investigation]]
- [[anonymous-intake-portal]]
- [[MOC_Legal]]
