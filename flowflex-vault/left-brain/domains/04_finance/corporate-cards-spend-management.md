---
type: module
domain: Finance & Accounting
panel: finance
cssclasses: domain-finance
phase: 6
status: complete
migration_range: 200000–249999
last_updated: 2026-05-12
---

# Corporate Cards & Spend Management

Company-issued virtual and physical cards with real-time spend controls, auto-receipt matching, and policy enforcement. Replaces Spendesk, Payhawk, and Ramp.

**Panel:** `finance`  
**Phase:** 6

---

## Features

### Cards
- Issue virtual Visa/Mastercard (via Stripe Issuing or Marqeta)
- Physical card fulfilment
- Per-card spend limits (daily, monthly, per-transaction)
- Category restrictions (e.g. no gambling, no cash withdrawal)
- Instant freeze/unfreeze
- Merchant whitelist/blacklist

### Expense Capture
- Auto-match transaction to uploaded receipt (AI OCR)
- Mobile push on every transaction — employee submits receipt instantly
- WhatsApp/SMS receipt submission
- Mileage claims with map-based km calculation
- Per diem calculator by country

### Policy & Approval
- Company-wide spend policy builder (rule-based)
- Out-of-policy flagging with manager approval flow
- Budget envelope per department/project
- Pre-approval for large transactions

### Reporting
- Real-time spend dashboard by person/department/category
- Budget vs actual by cost centre
- Merchant analysis — top vendors, anomalies
- Auto-post to Finance GL on approval
- VAT/tax reclaim tagging

### Integrations
- Auto-sync with Bank Reconciliation module
- Expense posted to relevant project (Projects module)
- Department budget deducted automatically

---

## Data Model

```erDiagram
    corporate_cards {
        ulid id PK
        ulid company_id FK
        ulid user_id FK
        string card_number_last4
        string type
        string status
        decimal daily_limit
        decimal monthly_limit
        string issuer_card_id
    }

    card_transactions {
        ulid id PK
        ulid card_id FK
        decimal amount
        string currency
        string merchant_name
        string merchant_category
        string status
        string receipt_url
        boolean policy_compliant
        ulid approved_by FK
    }

    spend_policies {
        ulid id PK
        ulid company_id FK
        string name
        json rules
        boolean is_default
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `CardTransactionPosted` | New transaction | Finance (bank sync), Notifications (push to user) |
| `ExpenseApproved` | Manager approves | Finance (GL post), Projects (deduct budget) |
| `PolicyViolationDetected` | Transaction exceeds policy | Notifications (manager alert) |

---

## Permissions

```
finance.cards.view-any
finance.cards.create
finance.cards.update
finance.cards.freeze
finance.expenses.approve
finance.spend-policy.manage
```

---

## Competitors Displaced

| Feature | FlowFlex | Spendesk | Payhawk | Ramp |
|---|---|---|---|---|
| Virtual cards | ✅ | ✅ | ✅ | ✅ |
| Auto receipt match | ✅ AI | ✅ | ✅ | ✅ |
| Policy engine | ✅ | ✅ | ✅ | ✅ |
| Integrated with invoicing | ✅ | ❌ | ❌ | ❌ |
| Integrated with payroll | ✅ | ❌ | ❌ | ❌ |
| Pricing | Included | €8/user/mo | €8/user/mo | Free+% |

---

## Related

- [[MOC_Finance]]
- [[entity-company]]
- [[travel-expense-management]]
