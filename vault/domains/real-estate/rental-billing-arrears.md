---
type: module
domain: Real Estate & Property
panel: realestate
module-key: realestate.billing
status: planned
color: "#4ADE80"
---

# Rental Billing & Arrears

> Rental invoice generation, payment tracking, arrears management, and automated dunning for commercial property portfolios.

**Panel:** `realestate`
**Module key:** `realestate.billing`

---

## What It Does

Rental Billing & Arrears automates the rent collection cycle. Based on lease terms, the system generates rent invoices on the correct billing frequency (monthly, quarterly, annually) for the correct amount including any stepped rents or recharge items. Payments are recorded against invoices, and outstanding amounts are tracked as arrears. An automated dunning workflow sends reminder notices at configurable intervals and escalates persistent arrears to the property manager for action.

---

## Features

### Core
- Automated invoice generation: create rent invoices from lease terms on the correct charge date
- Invoice line items: headline rent, service charge, building insurance recharge, and maintenance recharge
- Payment recording: log payment receipts against invoices with date and payment method
- Arrears tracking: outstanding invoice amounts flagged as arrears with age analysis (30/60/90 days)
- Dunning workflow: automated reminder emails at 7, 14, and 28 days overdue; escalation to property manager at 30 days
- Tenant statement: generate a statement of all invoices and payments for a tenant

### Advanced
- Quarterly in advance billing: generate and send invoices one quarter ahead of the due date (UK commercial norm)
- Rent deposit offset: apply deposit against overdue rent with manager approval
- Credit notes: issue credit notes for rent-free periods, error corrections, or negotiated concessions
- Multi-currency: invoice and receive payments in the tenant's currency
- Legal action flag: mark an arrears case as referred to solicitors for escalated tracking

### AI-Powered
- Payment prediction: estimate the probability of a tenant paying on time based on payment history
- Arrears risk scoring: rank tenants by arrears risk considering payment history and lease proximity to break
- Dunning tone optimisation: suggest escalating dunning email tone based on tenant's past response patterns

---

## Data Model

```erDiagram
    rental_invoices {
        ulid id PK
        ulid lease_id FK
        ulid tenant_id FK
        ulid company_id FK
        string invoice_number
        date invoice_date
        date due_date
        decimal amount
        string currency
        string status
        json line_items
        timestamps created_at_updated_at
    }

    rental_payments {
        ulid id PK
        ulid invoice_id FK
        ulid company_id FK
        decimal amount
        date payment_date
        string payment_method
        string reference
        timestamps created_at_updated_at
    }

    rental_invoices ||--o{ rental_payments : "settled by"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `rental_invoices` | Rent invoices | `id`, `lease_id`, `tenant_id`, `invoice_number`, `due_date`, `amount`, `status` |
| `rental_payments` | Payment records | `id`, `invoice_id`, `amount`, `payment_date`, `payment_method` |

---

## Permissions

```
realestate.billing.generate-invoices
realestate.billing.record-payments
realestate.billing.view-arrears
realestate.billing.manage-dunning
realestate.billing.export
```

---

## Filament

- **Resource:** `App\Filament\Realestate\Resources\RentalInvoiceResource`
- **Pages:** `ListRentalInvoices`, `CreateRentalInvoice`, `ViewRentalInvoice`
- **Custom pages:** `ArrearsPage`, `TenantStatementPage`, `DunningQueuePage`
- **Widgets:** `TotalArrearsWidget`, `InvoicesDueWidget`, `CollectionRateWidget`
- **Nav group:** Finance

---

## Displaces

| Feature | FlowFlex | Yardi | Re-Leased | MRI |
|---|---|---|---|---|
| Automated invoice generation | Yes | Yes | Yes | Yes |
| Arrears age analysis | Yes | Yes | Yes | Yes |
| Automated dunning | Yes | Yes | Yes | Yes |
| AI payment prediction | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[lease-management]] — rent terms drive invoice generation
- [[tenant-occupancy-management]] — invoices sent to tenant contacts
- [[ifrs-16-lease-accounting]] — lease liability schedule aligns with billing
- [[finance/INDEX]] — invoices posted to the finance ledger
