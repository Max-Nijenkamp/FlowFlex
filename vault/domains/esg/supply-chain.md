---
type: module
domain: ESG & Sustainability
panel: esg
module-key: esg.supply-chain
status: planned
color: "#4ADE80"
---

# Supply Chain ESG

> Supplier ESG assessment — questionnaire distribution, scoring, risk flagging, and engagement tracking.

**Panel:** `esg`
**Module key:** `esg.supply-chain`

---

## What It Does

Supply Chain ESG extends the company's sustainability programme to its supplier base, which often represents the majority of Scope 3 emissions and social and governance risk. Procurement and sustainability teams design ESG questionnaires, distribute them to suppliers, and score their responses against configurable criteria. Suppliers with low scores or red-flag responses are flagged for engagement or remediation. The module links to procurement supplier records and feeds Scope 3 upstream emissions calculations in the Carbon Footprints module.

---

## Features

### Core
- Questionnaire builder: create ESG assessment questionnaires with environmental, social, and governance sections
- Supplier distribution: send questionnaires to suppliers via email with a secure, no-login response link
- Response collection: suppliers fill in the questionnaire online; responses stored against the supplier record
- Automated scoring: calculate an ESG score for each supplier based on response weightings
- Risk flagging: automatically flag suppliers who score below a threshold or answer red-flag questions
- Supplier ESG profile: all assessment results visible on the supplier record

### Advanced
- Assessment cycles: create annual or periodic assessment cycles and track which suppliers have responded
- Tiered assessment: send a lightweight questionnaire to low-spend suppliers and a detailed one to strategic suppliers
- Evidence requests: ask suppliers to upload supporting documents alongside questionnaire responses
- Improvement plans: create a tracked remediation plan for at-risk suppliers
- Industry benchmarking: compare a supplier's score against the average for their industry category

### AI-Powered
- Question suggestion: AI suggests relevant questionnaire questions based on supplier industry and risk profile
- Response summarisation: AI summarises long free-text supplier responses into key points
- Risk pattern detection: identify systemic risks appearing across multiple supplier responses

---

## Data Model

```erDiagram
    esg_questionnaires {
        ulid id PK
        ulid company_id FK
        string name
        json sections
        boolean is_active
        timestamps created_at_updated_at
    }

    supplier_esg_assessments {
        ulid id PK
        ulid questionnaire_id FK
        ulid supplier_id FK
        ulid company_id FK
        string status
        json responses
        decimal esg_score
        string risk_level
        date sent_at
        date responded_at
        timestamps created_at_updated_at
    }

    esg_questionnaires ||--o{ supplier_esg_assessments : "used in"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `esg_questionnaires` | Assessment templates | `id`, `company_id`, `name`, `sections`, `is_active` |
| `supplier_esg_assessments` | Supplier responses | `id`, `questionnaire_id`, `supplier_id`, `status`, `esg_score`, `risk_level`, `responded_at` |

---

## Permissions

```
esg.supply-chain.view
esg.supply-chain.send-assessments
esg.supply-chain.manage-questionnaires
esg.supply-chain.view-responses
esg.supply-chain.export
```

---

## Filament

- **Resource:** `App\Filament\Esg\Resources\SupplierEsgAssessmentResource`
- **Pages:** `ListSupplierEsgAssessments`, `ViewSupplierEsgAssessment`
- **Custom pages:** `QuestionnaireBuilderPage`, `SupplierRiskPage`, `AssessmentCyclePage`
- **Widgets:** `SupplierRiskSummaryWidget`, `ResponseRateWidget`
- **Nav group:** Governance

---

## Displaces

| Feature | FlowFlex | EcoVadis | Sedex | Supplier.io |
|---|---|---|---|---|
| Custom questionnaires | Yes | No (fixed) | Partial | Yes |
| Automated scoring | Yes | Yes | Yes | Yes |
| Risk flagging | Yes | Yes | Yes | Yes |
| AI question suggestions | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[carbon-footprints]] — supplier assessment data feeds Scope 3 upstream emissions
- [[esg-kpis]] — supplier ESG coverage is an ESG KPI
- [[procurement/supplier-catalog]] — supplier records linked from procurement
- [[esg-reports]] — supply chain ESG performance reported in framework disclosures
