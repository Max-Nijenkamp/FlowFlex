---
type: module
domain: HR & People
panel: hr
cssclasses: domain-hr
phase: 4
status: planned
migration_range: 100000–149999
last_updated: 2026-05-09
---

# Global Payroll

Multi-country payroll processing, cross-border contractor payments, employer-of-record (EOR) integrations, and country-specific tax/compliance rules. Replaces Deel, Remote, Rippling Global, and Papaya Global.

**Panel:** `hr`  
**Phase:** 4

---

## Problem

Domestic payroll (Phase 2) handles one company in one country. The moment a company hires in a second country — or pays contractors internationally — they need:
- Correct local tax calculations (PAYE, URSSAF, Loonheffing, etc.)
- Local pension / social contribution rules
- Multi-currency salary disbursement
- Employer-of-record (EOR) for countries where the company isn't registered
- Contractor-vs-employee misclassification protection (IR35, B.V. rules, 1099)

---

## Features

### Multi-Country Payroll Engine
- Country profiles (NL, UK, DE, FR, BE, ES, US, IE — priority list)
- Per-country tax tables (updated quarterly)
- Gross-to-net calculation per country (income tax, social security, pension)
- Currency conversion at payrun date (from company base currency to employee local currency)
- Multi-currency payslips (amount in company currency + employee local currency)
- Net salary calculation respects local deductions

### Country Payroll Specs (Priority)

| Country | Tax Authority | Pension | Social Contributions |
|---|---|---|---|
| Netherlands | Belastingdienst | AOW / Pensioen | WW, WAO, ZVW |
| United Kingdom | HMRC | Auto-enrolment (The Pensions Regulator) | NI employer + employee |
| Germany | Finanzamt | Deutsche Rentenversicherung | KV, PV, RV, AV |
| France | URSSAF | ARRCO/AGIRC | ~40+ contribution lines |
| Belgium | SPF Finances | RSZ | |
| United States | IRS/state | 401k | FICA, FUTA, SUTA |

### Contractor Management
- Contractor profiles (name, country, rate, currency, payment schedule)
- Statement of work / contracts per engagement
- Invoice approval workflow (contractor submits → finance approves)
- Payment via Wise Business, Stripe Treasury, or SEPA/SWIFT transfer
- 1099/IR35/ZZP-status checks and flagging
- Self-billing (FlowFlex generates invoice on contractor's behalf)

### Employer-of-Record (EOR) Integration
- Direct integration with Deel, Remote, Papaya Global APIs
- Employee managed locally in FlowFlex HR, payroll executed via EOR provider
- Costs posted back to Finance (payroll expense, employer cost breakdown)
- EOR invoice reconciliation against headcount

### Payrun Processing
- Country-segmented payruns (NL payrun, UK payrun)
- Pre-payrun checklist (verify bank accounts, check leavers/joiners since last run)
- Gross-to-net calculation with full breakdown
- Multi-currency bank file (SEPA XML, BACS, ACH per country)
- Payslips in employee's language + currency
- Year-end tax forms (P60/P11D UK, Jaaropgave NL, Lohnsteuerbescheinigung DE)

### Compliance
- Auto-alerts for statutory minimums (minimum wage checks)
- Gender pay gap analysis (EU Pay Transparency Directive 2026)
- Country deadline calendar (payrun, tax filing, P60 deadlines)
- Right-to-work document tracking per employee

---

## Data Model

```erDiagram
    global_payroll_configs {
        ulid id PK
        ulid company_id FK
        string country_code
        string currency
        json tax_settings
        json pension_settings
        boolean is_active
    }

    contractor_profiles {
        ulid id PK
        ulid company_id FK
        string first_name
        string last_name
        string email
        string country_code
        string currency
        decimal rate
        string rate_type
        string tax_id
        string contractor_type
    }

    global_payrun_lines {
        ulid id PK
        ulid payrun_id FK
        ulid employee_id FK
        string country_code
        string currency
        decimal gross_amount
        decimal tax_deducted
        decimal pension_deducted
        decimal social_contribution
        decimal net_amount
        decimal fx_rate
        decimal net_amount_base_currency
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `GlobalPayrunProcessed` | Payrun complete | Finance (post payroll journal), Notifications (employees) |
| `ContractorPaymentSent` | Payment dispatched | Finance (record expense), Notifications (contractor) |
| `MinimumWageViolationDetected` | Gross below statutory minimum | Notifications (HR manager, legal alert) |
| `EORCostInvoiceReceived` | Deel/Remote invoice arrives | Finance (match to headcount, approve) |

---

## Permissions

```
hr.global-payroll.view-any
hr.global-payroll.run-payroll
hr.global-payroll.manage-contractors
hr.global-payroll.manage-configs
hr.global-payroll.view-payslips-all
```

---

## Competitors Displaced

Deel · Remote · Rippling Global · Papaya Global · Oyster · Multiplier

---

## Related

- [[MOC_HR]]
- [[entity-employee]]
- [[MOC_Finance]] — payroll costs → Finance GL
- [[concept-multi-tenancy]] — currency per company
