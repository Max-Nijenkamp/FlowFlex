---
type: module
domain: Subscription Billing & RevOps
panel: subscriptions
phase: 3
status: complete
cssclasses: domain-subscriptions
migration_range: 975500–975699
last_updated: 2026-05-12
---

# Dunning Management

Automated retry schedules and recovery emails for failed payments. Prevents involuntary churn — industry average 20–40% of churn is payment failures recoverable with good dunning.

---

## Retry Schedule

Configurable retry cadence (default Smart Retry — optimised timing):

| Attempt | Default Timing | Email Sent |
|---|---|---|
| Initial failure | Day 0 | "Payment failed — will retry" |
| Retry 1 | Day 3 | "We're trying again" |
| Retry 2 | Day 7 | "Action required — update payment" |
| Retry 3 | Day 14 | "Final attempt" |
| Exhausted | Day 21 | "Subscription cancelled — sorry to see you go" |

Smart Retry: ML-based timing — retry on day of week/time when success rate highest for this card type. Stripe Radar provides this natively.

---

## Recovery Emails

Branded email sequence sent to billing contact:
1. **Soft failure** — "We tried but couldn't process your payment. No action needed yet, we'll retry."
2. **Update card** — "Please update your payment method" + link to self-service billing portal
3. **Urgent** — "Your subscription will be cancelled in X days if not resolved"
4. **Final** — "Last chance" + direct link to update + phone number to call

Email templates editable per tenant. Optionally include: invoice PDF, update card link, reason for failure.

---

## Self-Service Billing Portal

Customer-facing portal (no login required — magic link from email):
- View failed invoices
- Update payment method (hosted Stripe/Mollie card form — PCI compliant, no card data touches FlowFlex)
- Download invoice history
- Cancel subscription (with exit survey)

---

## Metrics

- **Involuntary churn recovery rate**: % of dunning sequences that recover payment
- **Failed payment rate**: % of billing attempts that fail
- **Average recovery time**: days from first failure to payment recovered
- **Revenue recovered this month**: €/$ value saved from dunning

---

## Data Model

### `sub_dunning_sequences`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| subscription_id | ulid | FK |
| invoice_id | ulid | FK |
| status | enum | active/recovered/exhausted/cancelled |
| started_at | timestamp | |
| recovered_at | timestamp | nullable |
| exhausted_at | timestamp | nullable |
| attempts_made | int | |

---

## Migration

```
975500_create_sub_dunning_sequences_table
975501_create_sub_dunning_attempts_table
975502_create_sub_dunning_email_log_table
```

---

## Related

- [[MOC_SubscriptionBilling]]
- [[recurring-billing-engine]] — payment failures trigger dunning
- [[subscription-lifecycle-management]] — dunning exhausted → cancel subscription
- [[MOC_CustomerSuccess]] — dunning in progress flags CS at-risk
