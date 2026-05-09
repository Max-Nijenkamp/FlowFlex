---
type: module
domain: Subscription Billing & RevOps
panel: subscriptions
phase: 4
status: planned
cssclasses: domain-subscriptions
migration_range: 975850–975924
last_updated: 2026-05-09
---

# Usage-Based Billing

Track metered consumption, calculate overage charges, and bundle usage into invoices. Enables API-call billing, seat-hour billing, storage billing, and hybrid flat+usage models.

---

## Meter Definitions

Each meter tracks one type of usage:
- `api_calls` — REST API requests per month
- `active_seats` — highest concurrent seat count in billing period
- `storage_gb` — storage consumed at end of period
- `emails_sent` — transactional emails delivered
- `ai_tokens` — LLM tokens consumed

Meters have: aggregation method (sum / max / unique count) and reset period (monthly / annually).

---

## Usage Records

Events posted from the customer's product (or auto-tracked by FlowFlex):
```
POST /api/subscriptions/usage
{
  "subscription_id": "sub_xxx",
  "meter": "api_calls",
  "quantity": 1,
  "timestamp": "2026-05-09T14:23:00Z"
}
```

Idempotency key prevents double-counting. Records stored in append-only usage log.

---

## Overage Calculation

At billing period end:
1. Sum usage records per meter
2. Compare to included quantity in plan (e.g., plan includes 10,000 API calls)
3. Overage = max(0, usage − included)
4. Charge = overage × per-unit rate
5. Add to invoice as line item

---

## Data Model

### `sub_meters`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| key | varchar(100) | e.g., "api_calls" |
| display_name | varchar(200) | |
| aggregation | enum | sum/max/unique_count |
| reset_period | enum | monthly/quarterly/annually |

### `sub_usage_records`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| subscription_id | ulid | FK |
| meter_id | ulid | FK |
| quantity | decimal(14,4) | |
| occurred_at | timestamp | |
| idempotency_key | varchar(200) | unique |

---

## Migration

```
975850_create_sub_meters_table
975851_create_sub_usage_records_table
975852_create_sub_usage_period_totals_table
```

---

## Related

- [[MOC_SubscriptionBilling]]
- [[subscription-lifecycle-management]] — plan defines included quantities
- [[recurring-billing-engine]] — usage charges added to invoice
