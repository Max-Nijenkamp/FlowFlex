---
domain: core
module: billing-engine
feature: dunning
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Dunning

Parent: [[../_module]] · See [[../architecture]]

Payment-failure retry + suspension flow.

- Failed payment moves invoice `open → past_due` and starts the dunning schedule (3 attempts over 14 days *(assumed)*).
- `ProcessDunningCommand` — finance queue, daily 06:00. WHERE-guards on retry schedule timestamps for idempotency.
- Retry success → `past_due → paid`, dunning cancelled.
- Dunning exhausted → `past_due → uncollectible`, fires `CompanySubscriptionSuspended`, company `subscription_status → suspended`.
- Suspended company is blocked from panels by middleware.

> [!warning] UNVERIFIED
> `EnsureSubscriptionActive` middleware (which would block suspended companies) was not found in `app/`. See [[../unknowns]].

## UI

- **Kind**: background
- **Page**: background (no page) — `ProcessDunningCommand`, finance queue, daily 06:00. Past-due invoices surface on the same `BillingResource` list as a `past_due` status badge; there is no dedicated dunning screen.
- **Layout**: none of its own. Effect visible as an invoice status badge (`past_due` / `uncollectible`) and, once suspended, a company-level suspension banner rendered by the panel-access middleware.
- **Key interactions**: unattended. An admin may watch outcomes on `/admin` metrics and the billing list; the payer resolves it by paying via Stripe (out of band).
- **States**: empty = no past-due invoices · loading = n/a (background) · error = retry attempt failure re-queued on the finance queue · selected = a past-due invoice row on the billing list.
- **Gating**: no user-facing permission on the job itself; the resulting invoice rows honour `core.billing.view`.

## Data

- Owns / writes: `billing_invoices` (status transitions `open → past_due → paid|uncollectible`, retry-schedule timestamps). Updates `companies.subscription_status` only via its own `suspend()` path.
- Reads: Stripe payment-failure signals arrive through `handleStripeWebhook` (see [[stripe-integration]]); no other domain's tables read.
- Cross-domain writes: none directly — suspension effects reach other domains only by firing `CompanySubscriptionSuspended` (their own listeners react). See [[../../../../security/data-ownership]].

## Relations

- Consumes: Stripe `invoice.payment_failed` (via [[stripe-integration]]) → moves invoice `open → past_due`, starts dunning.
- Feeds: `CompanySubscriptionSuspended` on dunning exhaustion → consumed by [[../../notifications/_module]] (`NotifySubscriptionSuspendedListener`, must deliver by mail even when the company is suspended).
- Shared entity: `companies.subscription_status` enum — written only by this module's service.

> [!warning] UNVERIFIED
> Dunning schedule "3 attempts over 14 days" and the `subscription_status` enum being a simple column (not a spatie state machine) are `*(assumed)*` in the source notes. See [[../unknowns]].

## Test Checklist

### Unit
- [ ] Dunning schedule computes 3 retry windows over 14 days *(assumed)* from the past-due timestamp

### Feature (Pest)
- [ ] Payment failure moves invoice `open → past_due` and starts the dunning schedule
- [ ] Retry success moves `past_due → paid` and cancels dunning
- [ ] Dunning exhaustion moves `past_due → uncollectible`, fires `CompanySubscriptionSuspended`, and sets company `subscription_status → suspended` (pessimistic row lock — concurrent/duplicate webhook not double-processed)
