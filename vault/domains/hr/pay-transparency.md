---
type: module
domain: HR & People
panel: hr
module-key: hr.pay-transparency
status: planned
color: "#4ADE80"
---

# Pay Transparency

> Publish salary ranges, comply with pay transparency legislation, and run equal pay audits with automated remediation workflows — all without a separate compliance tool.

**Panel:** `/hr`
**Module key:** `hr.pay-transparency`

## What It Does

Pay Transparency helps companies comply with an expanding patchwork of pay transparency laws (Colorado EPEWA, California SB 1162, EU Pay Transparency Directive 2023/970, UK Gender Pay Gap reporting) while building internal trust through proactive disclosure. HR configures what is disclosed and to whom, generates the gender and ethnicity pay gap reports required by regulators, runs structured equal pay audits to identify employees paid below their band minimum with a documented remediation plan, and generates salary range statements ready for job postings.

## Features

### Core
- Publication scope control: per-band or global setting to publish salary ranges to `public` (visible on job postings), `employees-only` (visible to all internal employees in the self-service portal), or `off` (internal HR use only)
- Pay transparency policy settings: jurisdiction selector (Colorado, California, EU, UK, custom) that applies the relevant disclosure rules and reminds HR of upcoming compliance deadlines
- Salary range on job postings: when a salary band is published for a role, the range is automatically appended to any job posting created in the Recruitment module
- Gender pay gap report: mean and median gender pay gap (as % difference) calculated per department, per pay grade, and company-wide — uses employee salary records from Compensation & Benefits
- Ethnicity pay gap report: same calculation broken down by ethnicity (requires ethnicity data collection to be enabled in Employee Profiles with appropriate consent)

### Advanced
- Equal pay audit workflow: a structured audit that identifies every employee paid below their band minimum — each flagged employee gets a remediation entry with proposed increase, responsible HR owner, and target date; audit is saved as a dated snapshot
- Audit approval trail: equal pay audit findings can be reviewed and signed off by the CHRO with a digital signature and timestamp — creates an immutable record for regulatory inspection
- Compliance calendar: upcoming reporting deadlines per jurisdiction with days-until-due — surfaces as a dashboard widget and sends a notification 60 days before deadline
- Pay equity trend: year-over-year comparison of mean/median pay gap per department — shows whether the gap is narrowing or widening
- Privacy-preserving reporting: where a department has fewer than 5 employees in a group, the breakdown is suppressed and replaced with "insufficient data" to protect individual privacy

### AI-Powered
- Pay gap narrative generation: AI drafts the plain-English narrative that accompanies the gender pay gap report, explaining causes (e.g. distribution of women vs men across pay grades rather than like-for-like pay differences) — reviewable and editable before publication
- Remediation prioritisation: given the full list of below-minimum employees from the audit, AI recommends a prioritised remediation sequence based on severity of gap, employee tenure, and budget impact
- Jurisdiction change monitoring: AI monitors publicly available regulatory updates (via periodic web search) and flags when new pay transparency legislation is enacted in jurisdictions where the company has employees

## Data Model

```erDiagram
    hr_pay_transparency_settings {
        ulid id PK
        ulid company_id FK
        enum publication_scope
        boolean include_in_job_postings
        enum audit_frequency
        json jurisdiction_config
        timestamps created_at/updated_at
    }

    hr_equal_pay_audits {
        ulid id PK
        ulid company_id FK
        timestamp ran_at
        ulid ran_by FK
        json findings
        text remediation_plan
        enum status
        ulid approved_by FK
        timestamp approved_at
        timestamps created_at/updated_at
    }

    hr_equal_pay_remediation_entries {
        ulid id PK
        ulid audit_id FK
        ulid employee_id FK
        decimal current_salary
        decimal band_minimum
        decimal gap_amount
        decimal proposed_increase
        ulid owner_id FK
        date target_date
        enum status
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `hr_pay_transparency_settings.publication_scope` | enum: `public` / `employees_only` / `off` |
| `hr_pay_transparency_settings.audit_frequency` | enum: `annual` / `biannual` / `quarterly` |
| `hr_equal_pay_audits.findings` | JSON blob: `{total_flagged, total_gap_cost, departments: [{name, count, gap}]}` |
| `hr_equal_pay_audits.status` | enum: `draft` / `pending_approval` / `approved` / `archived` |
| `hr_equal_pay_remediation_entries.status` | enum: `open` / `in_progress` / `resolved` / `deferred` |
| Pay gap reports | Computed at query time from `hr_salary_bands` + `employee_compensation` — not stored, only audit snapshots are persisted |

**Note:** This module has no standalone salary data model. It reads exclusively from `hr_salary_bands` and `employee_compensation` tables defined in the Salary Benchmarking module, and from `hr_employees` (gender, ethnicity fields) in the Employee Profiles module.

## Permissions

```
hr.pay-transparency.view-settings
hr.pay-transparency.manage-settings
hr.pay-transparency.view-reports
hr.pay-transparency.run-audit
hr.pay-transparency.approve-audit
```

## Filament

- **Custom settings page:** `PayTransparencySettingsPage` — a Filament custom page (not a standard resource) with a settings form: publication scope radio, jurisdiction checkboxes, job posting toggle, audit frequency selector, and a compliance calendar widget showing upcoming deadlines
- **Custom report page:** `EqualPayAuditPage` — custom Filament page with three tabs: (1) Gender Pay Gap (mean/median gap by department, trend chart), (2) Ethnicity Pay Gap (same structure), (3) Equal Pay Audit (run new audit button, list of past audits with status, drill-down into findings and remediation entries)
- **No standard CRUD resource** — all functionality is delivered through the two custom pages above
- **Widgets:** `PayTransparencyComplianceWidget` on HR dashboard — shows publication status (published / not published) and next audit due date
- **Nav group:** Payroll (hr panel) — appears below Salary Benchmarking

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Syndio | Pay equity analysis and equal pay audit workflow |
| Trusaic | Equal pay compliance software and gap remediation |
| Brightmine (XpertHR) | Pay equity reporting and jurisdiction compliance tracking |
| Beqom | Compensation compliance and transparency management |

## Related

- [[salary-benchmarking]]
- [[compensation-benefits]]
- [[dei-metrics]]
- [[employee-profiles]]
- [[recruitment]]

## Implementation Notes

### Jurisdiction Coverage
The initial build should cover the three highest-urgency jurisdictions for FlowFlex's target market (EU SMBs):
1. **EU Pay Transparency Directive (2023/970)** — effective from 2026 for companies with 250+ employees; member state transposition varies
2. **UK Gender Pay Gap Reporting** — mandatory for 250+ employees, annual reporting to HMRC
3. **Colorado EPEWA + California SB 1162** — salary range disclosure on job postings

Jurisdiction logic is stored in `jurisdiction_config` JSON so new jurisdictions can be added without schema migrations. Each jurisdiction config specifies: required disclosures, reporting frequency, minimum employee threshold, and deadline month/day.

### Privacy Requirements
Ethnicity pay gap reporting requires ethnicity data — which is sensitive personal data under GDPR. The Employee Profiles module must have ethnicity collection off by default, with an explicit opt-in per company and per employee. The Pay Transparency module must only process ethnicity data if the company has (a) enabled it in company settings and (b) obtained appropriate consent records in the Privacy module.

### Audit Immutability
Once an equal pay audit is in `approved` status, its `findings` JSON and all `hr_equal_pay_remediation_entries` rows must be immutable — any changes require a new audit run. This is enforced at the model layer with a guard that prevents writes to approved audit records. The audit snapshot must include the date of the `hr_salary_bands` data used, so the snapshot is reproducible.
