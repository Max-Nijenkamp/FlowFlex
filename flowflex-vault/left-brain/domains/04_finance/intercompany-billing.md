---
type: module
domain: Finance & Accounting
panel: finance
cssclasses: domain-finance
phase: 6
status: planned
migration_range: 200000–249999
last_updated: 2026-05-09
---

# Intercompany Billing

Automate billing flows between entities in a group structure — management fees, shared service recharges, cost allocations, intercompany loans. Sits alongside Multi-Entity Consolidation.

**Panel:** `finance`  
**Phase:** 6 — requires Multi-Entity foundation from Phase 6

---

## Features

### Intercompany Transaction Types
- **Management fee** — parent charges subsidiary a % of revenue or flat monthly fee for central services (finance, HR, IT, legal)
- **Cost recharge** — subsidiary A incurs cost on behalf of B, charges B for its share
- **Intercompany loan** — one entity lends cash to another with interest
- **Transfer pricing** — goods/services sold at arm's length price between related entities (OECD rules)
- **Dividend** — dividend payment from subsidiary to parent (equity accounting)

### Automated Billing Rules
- Define rules once: "HoldCo charges OpCo NL 2.5% of revenue as management fee, monthly, invoice on last day of month"
- Auto-generate intercompany invoices on schedule
- Both sides auto-posted: issuing entity posts revenue, receiving entity posts expense
- Matching engine: mark intercompany invoice as matched on both sides

### Transfer Pricing Compliance
- Document transfer pricing policy per transaction type
- Store supporting calculations (CUP, TNMM, profit split methods)
- Generate transfer pricing file for tax authority review (OECD BEPS Action 13)
- Flag transactions above materiality threshold for review

### Intercompany Reconciliation
- Confirm both sides match before period close
- Unmatched transactions flagged (common problem: FX timing differences)
- One-click post-elimination journals for consolidation

### Intercompany Loan Management
- Principal + interest schedule
- Imputed interest calculation (market rate or agreed rate)
- Loan repayment tracking

---

## Data Model

```erDiagram
    intercompany_rules {
        ulid id PK
        ulid company_id FK
        ulid issuing_entity_id FK
        ulid receiving_entity_id FK
        string transaction_type
        string calculation_method
        decimal rate_or_amount
        string frequency
        boolean auto_post
    }

    intercompany_transactions {
        ulid id PK
        ulid rule_id FK
        ulid issuing_invoice_id FK
        ulid receiving_bill_id FK
        string status
        decimal amount
        string currency
        date transaction_date
        boolean is_matched
        boolean is_eliminated
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `IntercompanyInvoiceGenerated` | Auto-billing rule fires | Finance (both entities), Notifications |
| `IntercompanyMismatchDetected` | Amounts don't match | Notifications (group finance manager) |

---

## Permissions

```
finance.intercompany.view
finance.intercompany.manage-rules
finance.intercompany.post-transactions
finance.intercompany.reconcile
```

---

## Related

- [[MOC_Finance]]
- [[multi-entity-consolidation]]
- [[general-ledger-chart-of-accounts]]
