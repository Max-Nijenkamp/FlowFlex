---
domain: core
module: billing-engine
type: decision
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Billing Engine — Decisions

Parent: [[_module]]

## Stripe: raw SDK, not Cashier

Billing uses the raw `stripe/stripe-php` SDK (customer creation, subscription items per module, invoice generation, webhook handling) rather than Laravel Cashier. FlowFlex computes invoices and creates Stripe invoices directly.

→ [[../../../decisions/decision-2026-06-01-stripe-cashier-vs-sdk]]

## Open question

Stripe subscription-items vs invoice-per-period: spec assumes FlowFlex computes invoices and creates Stripe invoices directly (not Stripe-managed proration) *(assumed)* — revisit with an ADR if proration complexity demands Stripe subscriptions.
