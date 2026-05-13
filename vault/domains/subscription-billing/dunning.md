---
type: module
domain: Subscription Billing & RevOps
panel: billing
module-key: billing.dunning
status: planned
color: "#4ADE80"
---

# Dunning

> Failed payment recovery — configurable retry schedules, email sequences, and automatic suspension logic.

**Panel:** `billing`
**Module key:** `billing.dunning`

---

## What It Does

Dunning manages the automated process of recovering failed subscription payments. When a Stripe payment fails, the dunning engine retries the charge according to a configurable schedule (e.g. immediately, then after 3 days, then after 7 days), while sending personalised reminder emails to the billing contact at each stage. If payment is not recovered after the final retry, the subscription can be automatically suspended or cancelled. The dunning dashboard shows all accounts currently in a dunning cycle with their status and next action.

---

## Features

### Core
- Dunning rule configuration: define retry intervals (day 0, day 3, day 7, day 14)
- Email sequence: configure the content of the email sent at each retry stage (polite reminder, urgent notice, final warning)
- Smart Retries: use Stripe Adaptive Retry to optimise the retry timing based on card issuer data
- Auto-suspension: automatically suspend the subscription if all retries fail
- Manual recovery: CSM can manually trigger a retry or mark the invoice as uncollectable
- Dunning dashboard: all accounts in a dunning cycle with stage, days in cycle, and amount at risk

### Advanced
- Plan-based dunning rules: enterprise accounts get more retries and more time before suspension than SMB accounts
- Dunning pause: pause dunning for an account while a payment issue is being resolved manually (e.g. card replacement)
- Win-back flow: after suspension, send a sequence of win-back emails with a reactivation link
- Revenue at risk: total value of subscriptions currently in a dunning cycle
- Dunning resolution reporting: track what percentage of dunning cases are recovered vs churned

### AI-Powered
- Optimal retry timing: AI predicts the best time of day to retry a card based on historical success patterns
- Early dunning detection: identify accounts with a pattern of late payments before a dunning event occurs
- Personalised dunning copy: AI tailors email copy based on the account's size and relationship history

---

## Data Model

```erDiagram
    dunning_configs {
        ulid id PK
        ulid company_id FK
        string name
        string target_plan_tier
        json retry_schedule
        integer max_retries
        string post_dunning_action
        boolean is_active
        timestamps created_at_updated_at
    }

    dunning_cycles {
        ulid id PK
        ulid invoice_id FK
        ulid account_id FK
        ulid company_id FK
        ulid config_id FK
        integer current_retry
        string status
        timestamp last_retry_at
        timestamp next_retry_at
        timestamp resolved_at
        string resolution
    }

    dunning_configs ||--o{ dunning_cycles : "governs"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `dunning_configs` | Dunning rule sets | `id`, `company_id`, `name`, `retry_schedule`, `max_retries`, `post_dunning_action` |
| `dunning_cycles` | Active dunning cases | `id`, `invoice_id`, `account_id`, `current_retry`, `status`, `next_retry_at`, `resolution` |

---

## Permissions

```
billing.dunning.view-any
billing.dunning.configure
billing.dunning.manual-retry
billing.dunning.pause
billing.dunning.export
```

---

## Filament

- **Resource:** `App\Filament\Billing\Resources\DunningCycleResource`
- **Pages:** `ListDunningCycles`, `ViewDunningCycle`
- **Custom pages:** `DunningDashboardPage`, `DunningConfigPage`, `RecoveryReportPage`
- **Widgets:** `ActiveDunningWidget`, `RevenueAtRiskWidget`, `RecoveryRateWidget`
- **Nav group:** Invoicing

---

## Displaces

| Feature | FlowFlex | Chargebee | Zuora | Stripe Billing |
|---|---|---|---|---|
| Configurable retry schedules | Yes | Yes | Yes | Yes |
| Email sequence automation | Yes | Yes | Yes | Partial |
| Auto-suspension | Yes | Yes | Yes | Yes |
| AI optimal retry timing | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[invoicing]] — failed invoice payment triggers a dunning cycle
- [[subscription-plans]] — plan tier determines which dunning config applies
- [[mrr-analytics]] — recovered revenue tracked in churn MRR movement
- [[customer-success/churn-risk]] — accounts in dunning appear as churn risk
