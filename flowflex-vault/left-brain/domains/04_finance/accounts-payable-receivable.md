---
type: module
domain: Finance & Accounting
panel: finance
cssclasses: domain-finance
phase: 3
status: complete
migration_range: 200008–200009
last_updated: 2026-05-12
right_brain_log: "[[builder-log-finance-phase3]]"
---

# Accounts Payable & Receivable

Manage supplier bills (AP) and track outstanding customer invoices (AR). Includes approval workflows, aging reports, and GL posting. Replaces Xero Bills, QuickBooks AP.

**Panel:** `finance`  
**Phase:** 3  
**Module key:** `finance.ap-ar`

---

## Data Model

```erDiagram
    bills {
        ulid id PK
        ulid company_id FK
        ulid contact_id FK
        string number
        date issue_date
        date due_date
        string status
        decimal amount
        decimal tax_amount
        decimal total
        string currency
        text description
        ulid approved_by FK
        timestamp approved_at
        timestamp paid_at
    }

    bill_items {
        ulid id PK
        ulid bill_id FK
        string description
        decimal quantity
        decimal unit_price
        decimal tax_rate
        decimal total
        ulid account_id FK
    }

    bill_payments {
        ulid id PK
        ulid bill_id FK
        ulid bank_account_id FK
        decimal amount
        date payment_date
        string method
        string reference
    }
```

**Bill status flow:** `draft` → `approved` → `paid` | `overdue`

**AR** is tracked via the `invoices` table (see [[invoicing]]). This module adds AP (bills) and the combined aging view.

---

## Service: BillService

```php
createBill(CreateBillData $data): Bill
approveBill(Bill $bill, User $approver): void   // posts GL entry: debit Expense, credit AP
markBillPaid(Bill $bill, PaymentData $payment): void  // debit AP, credit Bank
getApAgingReport(string $companyId): array
getArAgingReport(string $companyId): array
```

---

## GL Integration

On bill approved → post journal:
- Debit appropriate expense/asset account
- Credit `2000 Accounts Payable`

On bill paid → post journal:
- Debit `2000 Accounts Payable`
- Credit `1000 Cash & Bank`

---

## Permissions

```
finance.bills.view
finance.bills.create
finance.bills.approve
finance.bills.pay
finance.bills.delete
```

---

## Related

- [[MOC_Finance]]
- [[general-ledger-chart-of-accounts]] — GL posting
- [[invoicing]] — AR side of AP/AR
- [[bank-reconciliation]] — match bill payments to bank transactions
- [[MOC_Operations]] — purchase order approval → auto-create bill
