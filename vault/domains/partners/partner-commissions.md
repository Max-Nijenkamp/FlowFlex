---
type: module
domain: Partner & Channel
panel: partners
module-key: partners.commissions
status: planned
color: "#4ADE80"
---

# Partner Commissions

> Commission rule engine (% of deal value, fixed amount, recurring %, tiered by partner tier), calculation on deal close, approval workflow, payout scheduling, and PDF statements — with Stripe Connect or CSV export for payouts.

**Panel:** `/partners`
**Module key:** `partners.commissions`

## What It Does

Partner Commissions automates the calculation and management of commissions owed to partner organisations for deals they bring in. The company defines commission rules — percentage of deal value, fixed amount per deal, recurring percentage of subscription revenue — that can vary by partner type, tier, and deal characteristics. When a registered deal closes (the linked CRM deal reaches "Closed Won"), a commission record is calculated automatically based on the matching rule. A company finance reviewer approves the commission before it enters the payout queue. Payouts are batched monthly or quarterly and processed via Stripe Connect or exported as a CSV for manual bank transfer. Partners see their balance, individual commission line items, and downloadable PDF statements in the partner portal.

## Features

### Core
- Commission rule builder: name, type (percentage of deal value / fixed amount per deal / recurring percentage of subscription MRR), rate, applicable partner types, applicable partner tiers, deal value thresholds (e.g. only applies if deal > $10,000)
- Automatic calculation: triggered when `partner_deal_registrations.is_converted` is set to true. `CommissionCalculator` service evaluates all matching rules and calculates the commission amount.
- Commission record created with status `pending` — awaiting review
- Approval workflow: finance reviewer in Filament approves or rejects each commission. Rejection requires a reason. Partner notified on both outcomes.
- Payout scheduling: bulk payout run (monthly or quarterly) — groups all `approved` commissions into a payout batch. Each batch has a reference number.
- Partner commission statement: PDF generated per partner per payout period using `dompdf/dompdf` or `barryvdh/laravel-dompdf`. Downloadable from the partner portal.

### Advanced
- Stripe Connect payouts: on payout run, the system transfers funds to each partner's connected Stripe Connect account. Requires the company to operate as a Stripe Connect platform. Partners complete Stripe Connect onboarding (Express account) in the portal.
- CSV export fallback: for companies not using Stripe Connect, the payout run exports a CSV of (partner name, bank reference, amount, currency) for manual processing via banking platform.
- Recurring commission: for SaaS deals with recurring revenue, a recurring commission rule generates a commission record each month (triggered by subscription renewal events from the Subscription Billing domain). Recurring commissions auto-approve (no manual review required — configurable).
- Clawback: if a deal is refunded or churns within 90 days of close, the related commission record is flagged for clawback review. Finance can approve the clawback (creates a negative commission record that offsets the next payout).
- Multi-currency support: commission amounts calculated in the deal currency. Payout currency is the partner's preferred payout currency with a configurable exchange rate snapshot at the time of the payout run.
- Commission forecast: projection of commissions payable in the next quarter based on the current registered deal pipeline and their estimated close dates

### AI-Powered
- Rule gap detection: AI analyses recent deal registrations where no commission rule matched and suggests new rules to cover the gap
- Payout anomaly detection: flags commission amounts that are statistical outliers (e.g. a commission 5× the average for that partner tier) before the approval step

## Data Model

```erDiagram
    partner_commission_rules {
        ulid id PK
        ulid company_id FK
        string name
        string type
        decimal rate
        decimal fixed_amount
        string currency
        decimal min_deal_value
        json applicable_types
        json applicable_tiers
        boolean auto_approve_recurring
        boolean is_active
        timestamps created_at/updated_at
    }

    partner_commissions {
        ulid id PK
        ulid company_id FK
        ulid partner_id FK
        ulid deal_registration_id FK
        ulid commission_rule_id FK
        decimal deal_value
        decimal commission_amount
        string currency
        string status
        ulid approved_by FK
        timestamp approved_at
        string rejection_reason
        ulid payout_batch_id FK
        timestamp paid_at
        string payout_reference
        boolean is_clawback
        timestamps created_at/updated_at
    }

    partner_payout_batches {
        ulid id PK
        ulid company_id FK
        string reference
        string method
        string status
        decimal total_amount
        string currency
        timestamp run_at
        ulid run_by FK
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `type` | percentage / fixed / recurring_percentage |
| `rate` | For `percentage` type: 0.00–100.00. For `recurring_percentage`: same. Null for `fixed`. |
| `fixed_amount` | For `fixed` type only. Null for percentage types. |
| `status` on commissions | pending / approved / rejected / paid / clawback_pending / clawbacked |
| `method` on payout batches | stripe_connect / csv_export / manual |
| `is_clawback` | true = negative commission created to offset a clawback |
| `applicable_types` | JSON array of partner types this rule applies to (empty = all types) |
| `applicable_tiers` | JSON array of tier names (empty = all tiers) |

## Permissions

```
partners.commissions.view
partners.commissions.approve
partners.commissions.payout
partners.commissions.configure-rules
partners.commissions.export
```

## Filament

- **Resource:** `CommissionRuleResource` (CRUD for rules), `PartnerCommissionResource` (list/view commissions with approve/reject actions, filter by partner, status, date)
- **Pages:** `ListCommissionRules`, `CreateCommissionRule`, `EditCommissionRule`, `ListPartnerCommissions`, `ViewPartnerCommission`
- **Custom pages:** `PartnerPayoutPage` — custom Filament page for running payout batches. Shows all approved unpaid commissions grouped by partner. "Run Payout" button triggers the payout job (Stripe Connect or CSV export). Shows history of past payout batches. Class: `App\Filament\Partners\Pages\PartnerPayoutPage`.
- **Widgets:** `CommissionsPendingApprovalWidget` (count of commissions awaiting review), `TotalCommissionsPayableWidget` (sum of approved unpaid commissions), `CommissionsThisQuarterWidget` — on the Partners panel dashboard
- **Nav group:** Finance (partners panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| PartnerStack | Commission management and payouts |
| Tipalti | Partner/affiliate payouts |
| Impact.com | Commission tracking |
| Kiflo | Commission calculations and statements |
| Stripe Connect (direct) | Commission payout infrastructure |

## Related

- [[deal-registration]]
- [[partner-portal]]
- [[affiliate-management]]
- [[domains/finance/INDEX]]
- [[domains/subscription-billing/INDEX]]

## Implementation Notes

- **Stripe Connect platform setup:** The company must configure a Stripe Connect platform account (separate from their standard Stripe account). `STRIPE_CONNECT_CLIENT_ID` stored in `config/services.php`. Partners complete Express account onboarding via a Stripe-hosted OAuth flow initiated from the portal ("Connect your bank account for payouts"). The `stripe_account_id` is stored on the `partners` record.
- **Commission calculation:** `CommissionCalculator` service loads all active rules for the company, filters by `applicable_types` and `applicable_tiers` matching the partner, and filters by `min_deal_value` threshold. If multiple rules match, all are applied (creates multiple commission records — one per matching rule). Rules do not stack by default; if configured for single-rule mode, the first matching rule wins.
- **Payout batch processing:** `ProcessPayoutBatch` queued job iterates over all `partner_commissions` where `payout_batch_id = batch_id`. For Stripe Connect: calls `\Stripe\Transfer::create(['amount' => ...in cents..., 'currency' => ..., 'destination' => partner.stripe_account_id])`. Records `payout_reference = stripe_transfer_id`. For CSV: writes each row to a temporary file, stores via storage driver, generates a signed download URL.
- **Recurring commissions:** `SubscriptionRenewalCommissionJob` queued job subscribed to the `SubscriptionRenewed` event from the Subscription Billing domain. Finds any active `partner_commission_rules` with `type = recurring_percentage` for the partner associated with the original deal. Creates a commission record for `deal_value * rate / 100` with `status = approved` (if `auto_approve_recurring = true`).
- **Statement PDF:** `PartnerCommissionStatementPdf` class uses `barryvdh/laravel-dompdf`. Renders a Blade view with partner details, payout batch reference, itemised commission list, total, and company letterhead. Stored temporarily in `storage/app/partner-statements/{batch_id}/{partner_id}.pdf` and served via signed URL from the portal.
