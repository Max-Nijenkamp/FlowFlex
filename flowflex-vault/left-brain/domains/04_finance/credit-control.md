---
type: module
domain: Finance & Accounting
panel: finance
phase: 3
status: complete
cssclasses: domain-finance
migration_range: 258500–258999
last_updated: 2026-05-12
---

# Credit Control

Manage outstanding debts. Automated dunning sequences, credit limits, dispute management, and collections workflow. Reduces DSO (Days Sales Outstanding) and bad debt.

---

## Credit Limits

Set credit limits per customer:
- Default limit by customer tier (new customer: €5,000, established: €50,000)
- Custom limit per account
- On new invoice: system checks if invoice would breach credit limit → block or warn

---

## Aged Debtor Report

Standard AR ageing:
| Bucket | Definition |
|---|---|
| Current | Not yet due |
| 1–30 days | Overdue up to 30 days |
| 31–60 days | |
| 61–90 days | |
| 90+ days | Likely bad debt |

Export for CFO review. Per customer and aggregated.

---

## Dunning Sequences

Configurable automated chasing per overdue bucket:
| Day | Action |
|---|---|
| Due date | Friendly payment reminder (auto-email) |
| +7 days | Follow-up email: "Did you receive our invoice?" |
| +14 days | Escalation email: CC account manager |
| +30 days | Formal notice: late payment interest begins |
| +60 days | Collections alert: hand to senior credit controller |
| +90 days | Bad debt write-off consideration |

Each email templated and personalised. Paused if customer raises a dispute.

---

## Dispute Management

Customer disputes invoice:
1. Dispute logged: reason, amount in dispute
2. Account manager + finance notified
3. Invoice dunning paused while under dispute
4. Resolution: credit note issued / dispute rejected / partial settlement
5. Invoice updated and dunning resumes if partial

---

## Payment Plans

For struggling customers:
- Agree payment plan (e.g., pay €1,000/month over 6 months)
- System tracks plan adherence
- Missed instalment → alert → escalate

---

## Bad Debt Write-Off

When debt irrecoverable:
- Credit controller proposes write-off → CFO approval
- Journal: DR Bad Debt Expense / CR Accounts Receivable
- Sent to debt collection agency (manual)

---

## Data Model

### `fin_credit_limits`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| customer_id | ulid | FK |
| limit_amount | decimal(14,2) | |
| currency | char(3) | |

### `fin_dunning_actions`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| invoice_id | ulid | FK |
| customer_id | ulid | FK |
| action_type | enum | email/call/notice/writeoff |
| scheduled_at | timestamp | |
| sent_at | timestamp | nullable |
| outcome | varchar(200) | nullable |

---

## Migration

```
258500_create_fin_credit_limits_table
258501_create_fin_dunning_actions_table
258502_create_fin_payment_plans_table
258503_create_fin_invoice_disputes_table
```

---

## Related

- [[MOC_Finance]]
- [[accounts-receivable-automation]]
- [[cash-flow-forecasting]]
