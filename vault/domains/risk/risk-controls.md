---
type: module
domain: Risk Management
panel: risk
module-key: risk.controls
status: planned
color: "#4ADE80"
---

# Risk Controls

> Control library — risk-to-control mapping, control ownership, effectiveness testing, and deficiency tracking.

**Panel:** `risk`
**Module key:** `risk.controls`

---

## What It Does

Risk Controls maintains a library of all controls in place to mitigate identified risks. Each control is described, classified (preventive/detective/corrective), assigned an owner, and linked to one or more risks in the register. Controls are subject to periodic effectiveness testing — the owner (or an independent tester) records test results and supporting evidence. Controls that fail their effectiveness test generate a deficiency that must be tracked to remediation. The control library provides auditors with a complete view of the control environment.

---

## Features

### Core
- Control library: name, description, control type (preventive/detective/corrective), owner, and frequency
- Risk-to-control mapping: link each control to the risks it mitigates (many-to-many)
- Control status: active, under review, ineffective, retired
- Effectiveness testing: record a test of the control with date, tester, method, result (pass/fail/partial), and evidence
- Deficiency logging: failed tests automatically create a deficiency record with remediation owner and deadline
- Evidence attachment: attach test evidence files to the control test record

### Advanced
- Control categories: IT general controls, financial controls, operational controls, HR controls (configurable)
- Testing schedule: set the required testing frequency per control; alert owner when test is due
- Test sampling: for large-population controls, configure the required sample size for the test
- Control gap analysis: identify risks in the register that have no mapped controls
- Shared controls: mark a control as shared across multiple business units to avoid duplicate testing
- Regulatory mapping: link controls to regulatory clauses they satisfy (e.g. ISO 27001 A.9.1.1)

### AI-Powered
- Control adequacy scoring: assess whether the number and type of controls mapped to a high-risk item are sufficient
- Deficiency pattern detection: flag when the same control type is repeatedly failing across the organisation
- Test evidence quality check: AI reviews uploaded evidence descriptions for completeness before the test is submitted

---

## Data Model

```erDiagram
    controls {
        ulid id PK
        ulid company_id FK
        ulid owner_id FK
        string name
        text description
        string control_type
        string category
        string frequency
        string status
        timestamps created_at_updated_at
    }

    risk_control_mappings {
        ulid id PK
        ulid company_id FK
        ulid risk_id FK
        ulid control_id FK
        timestamps created_at_updated_at
    }

    control_tests {
        ulid id PK
        ulid company_id FK
        ulid control_id FK
        ulid tester_id FK
        date test_date
        string result
        text notes
        timestamps created_at_updated_at
    }

    control_deficiencies {
        ulid id PK
        ulid company_id FK
        ulid control_test_id FK
        ulid remediation_owner_id FK
        string severity
        text description
        date remediation_deadline
        string status
        timestamps created_at_updated_at
    }

    controls ||--o{ risk_control_mappings : "mitigates"
    controls ||--o{ control_tests : "tested via"
    control_tests ||--o{ control_deficiencies : "generates"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `controls` | Control library | `id`, `company_id`, `owner_id`, `name`, `control_type`, `category`, `frequency`, `status` |
| `risk_control_mappings` | Risk-to-control links | `id`, `risk_id`, `control_id` |
| `control_tests` | Test execution records | `id`, `control_id`, `tester_id`, `test_date`, `result` |
| `control_deficiencies` | Failed test follow-ups | `id`, `control_test_id`, `remediation_owner_id`, `severity`, `remediation_deadline`, `status` |

---

## Permissions

```
risk.controls.view
risk.controls.manage
risk.controls.test
risk.controls.manage-deficiencies
risk.controls.export
```

---

## Filament

- **Resource:** `App\Filament\Risk\Resources\ControlResource`
- **Pages:** `ListControls`, `CreateControl`, `EditControl`, `ViewControl`
- **Custom pages:** `ControlTestingSchedulePage`, `DeficiencyTrackerPage`, `ControlGapAnalysisPage`
- **Widgets:** `ControlsOverdueTestWidget`, `OpenDeficienciesWidget`
- **Nav group:** Controls

---

## Displaces

| Feature | FlowFlex | Archer | LogicManager | ServiceNow GRC |
|---|---|---|---|---|
| Control library | Yes | Yes | Yes | Yes |
| Effectiveness testing | Yes | Yes | Yes | Yes |
| Deficiency tracking | Yes | Yes | Yes | Yes |
| AI adequacy scoring | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[risk-register]] — controls linked to risks to demonstrate treatment
- [[risk-assessments]] — control effectiveness determines residual risk score
- [[risk-reporting]] — control test results included in board risk reports
- [[compliance-monitoring]] — regulatory-mapped controls tracked for compliance evidence
