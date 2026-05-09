---
type: module
domain: Finance & Accounting
panel: finance
cssclasses: domain-finance
phase: 3
status: planned
migration_range: 200000–249999
last_updated: 2026-05-09
---

# Payroll Tax Filing

Automated submission of payroll tax data to government authorities. Payroll *calculates* taxes; this module *files* them. Without this, payroll is legally incomplete — companies still need to manually file returns.

**Panel:** `finance`  
**Phase:** 3 — required for payroll compliance from day one

---

## Problem

Phase 2 Payroll calculates:
- UK: PAYE + NI → payslip
- NL: Loonheffing → payslip
- DE: Lohnsteuer → payslip

But the *statutory filing* — RTI submission to HMRC, Loonaangifte to Belastingdienst — is a separate step that must happen on a defined schedule. Missing a filing = penalties.

---

## Features

### Filing Engine per Country

**United Kingdom (HMRC RTI)**
- Full Payment Submission (FPS) — per payrun, due on or before pay date
- Employer Payment Summary (EPS) — monthly, for statutory recovery
- Earlier Year Update (EYU) — correct prior year errors
- P60 year-end submission
- P11D (benefits in kind) — annual
- Direct API connection to HMRC Gateway (OAuth 2.0 MTD)
- Auto-send FPS on payrun completion (configurable)

**Netherlands (Belastingdienst)**
- Loonaangifte — monthly payroll tax declaration
- DigiPoort API submission (SOAP/XML)
- Jaaropgave generation per employee
- SV-aangifte (social insurance contributions)

**Germany (ELSTER)**
- Lohnsteueranmeldung — monthly wage tax return
- DEÜV social insurance notifications (GKV-Monatsmeldung)
- Lohnsteuerbescheinigung — annual
- ELSTER API integration

**United States (IRS)**
- Form 941 — quarterly employer tax return
- Form 940 — FUTA annual return
- W-2 / W-3 — annual employee earnings + social security
- State tax filing per state (API per state agency — 50 different systems)
- E-file via IRS Modernized e-File (MeF)

**France (DSN)**
- Déclaration Sociale Nominative — monthly social declaration
- Replaces 24 legacy declarations
- Net-Entreprises DSN API

### Filing Calendar
- Upcoming filing deadlines per country per company
- RAG status (green = filed, amber = due within 7 days, red = overdue)
- Calendar view and list view
- Push notification when deadline approaches

### Filing History
- Record of every submission: date, period, authority, reference number, acknowledgement
- Download submission XML/PDF
- View authority response (acknowledgement or error codes)
- Resubmission flow for rejected filings

### Error Resolution
- Authority rejection codes mapped to plain-English explanations
- Link to the payrun data that caused the error
- Correction workflow: amend payrun → re-file

---

## Data Model

```erDiagram
    tax_filings {
        ulid id PK
        ulid company_id FK
        string country_code
        string filing_type
        string period
        string status
        string authority_reference
        json submission_payload
        json authority_response
        timestamp submitted_at
        timestamp due_at
        ulid submitted_by FK
    }

    tax_filing_deadlines {
        ulid id PK
        ulid company_id FK
        string country_code
        string filing_type
        date due_date
        boolean is_filed
        ulid tax_filing_id FK
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `TaxFilingSubmitted` | Submission sent | Notifications (confirmation to finance manager) |
| `TaxFilingRejected` | Authority returns error | Notifications (urgent — finance manager) |
| `TaxFilingDeadlineApproaching` | 7 days before due | Notifications (finance manager) |
| `TaxFilingOverdue` | Past due date, not filed | Notifications (urgent — company owner) |

---

## Permissions

```
finance.tax-filing.view
finance.tax-filing.submit
finance.tax-filing.manage-credentials
```

---

## Related

- [[MOC_Finance]]
- [[MOC_HR]] — payroll run data feeds filings
- [[global-payroll]] — multi-country filing coordination
