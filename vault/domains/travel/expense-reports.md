---
type: module
domain: Business Travel
panel: travel
module-key: travel.expenses
status: planned
color: "#4ADE80"
---

# Expense Reports

> Post-trip expense reports linked to travel bookings — receipt attachment, per-diem calculation, and approval workflow.

**Panel:** `travel`
**Module key:** `travel.expenses`

---

## What It Does

Expense Reports allows employees to claim reimbursement for costs incurred during a business trip. After returning, the employee creates an expense report linked to their travel request, adds individual expense line items (meals, transport, accommodation top-ups, incidentals), uploads receipts, and submits for approval. Per-diem allowances from the travel policy are automatically calculated and applied. Approved reports are pushed to the Finance panel for payment processing.

---

## Features

### Core
- Expense report creation: linked to a travel request, with trip dates pre-filled
- Expense line items: category (meal, transport, accommodation, incidental), date, amount, currency, description
- Receipt upload: photo or PDF receipt attached per line item
- Per-diem calculation: system auto-calculates the applicable per-diem allowance based on trip dates and destination
- Manager approval: submitted reports routed to the employee's line manager for review and approval
- Finance handoff: approved reports pushed to the Finance panel for reimbursement processing

### Advanced
- Multi-currency support: enter expenses in local currency; converted to base currency at current rate on submission date
- OCR receipt scanning: scan a receipt and auto-populate the amount, date, and vendor
- Mileage claims: log personal vehicle mileage with automatic rate calculation
- Report templates: pre-populate line items based on typical expense categories for a trip type
- Bulk receipt upload: upload all receipts at once and match to line items

### AI-Powered
- Policy compliance check: AI flags expense line items that exceed policy limits before submission
- Duplicate receipt detection: identify when the same receipt has been submitted in a previous report
- Category suggestion: AI categorises an expense from the receipt description automatically

---

## Data Model

```erDiagram
    expense_reports {
        ulid id PK
        ulid request_id FK
        ulid company_id FK
        ulid employee_id FK
        ulid approver_id FK
        string title
        date period_from
        date period_to
        decimal total_amount
        decimal per_diem_amount
        string currency
        string status
        timestamp approved_at
        timestamp paid_at
        timestamps created_at_updated_at
    }

    expense_line_items {
        ulid id PK
        ulid report_id FK
        string category
        date expense_date
        decimal amount
        string currency
        decimal converted_amount
        text description
        string receipt_url
        boolean is_per_diem
        timestamps created_at_updated_at
    }

    expense_reports ||--o{ expense_line_items : "contains"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `expense_reports` | Expense report headers | `id`, `request_id`, `company_id`, `employee_id`, `total_amount`, `per_diem_amount`, `status` |
| `expense_line_items` | Individual expenses | `id`, `report_id`, `category`, `expense_date`, `amount`, `currency`, `receipt_url` |

---

## Permissions

```
travel.expenses.submit-own
travel.expenses.view-own
travel.expenses.approve
travel.expenses.view-all
travel.expenses.export
```

---

## Filament

- **Resource:** `App\Filament\Travel\Resources\ExpenseReportResource`
- **Pages:** `ListExpenseReports`, `CreateExpenseReport`, `EditExpenseReport`, `ViewExpenseReport`
- **Custom pages:** `ExpenseApprovalQueuePage`, `ReimbursementSummaryPage`
- **Widgets:** `PendingExpenseReportsWidget`, `ReimbursementAmountWidget`
- **Nav group:** Requests

---

## Displaces

| Feature | FlowFlex | Concur | Expensify | TravelPerk |
|---|---|---|---|---|
| Receipt upload and matching | Yes | Yes | Yes | Yes |
| Per-diem auto-calculation | Yes | Yes | No | No |
| OCR receipt scanning | Yes | Yes | Yes | No |
| Approval workflow | Yes | Yes | Yes | Partial |
| Included in platform | Yes | No | No | No |

---

## Related

- [[travel-requests]] — expense reports link to the originating request
- [[bookings]] — actual booking costs imported as expense line items
- [[travel-policies]] — per-diem rates and policy limits applied in expense validation
- [[finance/INDEX]] — approved reports pushed to finance for payment
