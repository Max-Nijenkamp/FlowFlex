---
domain: core
module: webhooks
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Webhooks — Security

Parent: [[_module]]

## Permissions

`core.webhooks.view-any` · `core.webhooks.create` · `core.webhooks.update` · `core.webhooks.delete` · `core.webhooks.test`

## Authorization

`WebhookEndpointResource` gates on:
`canAccess() = Auth::user()->can('core.webhooks.view-any') && BillingService::hasModule('core.webhooks')`
per [[../../../architecture/filament-patterns]] #1. See [[../../../security/authn-authz]].

## HMAC signing

Every delivery carries `X-FlowFlex-Signature` = HMAC-SHA256 of the payload keyed by the endpoint secret; receivers verify with `hash_equals` (constant-time). See [[../../../security/webhooks-signing]].

## Encrypted secret

`webhook_endpoints.secret` is a `text` column with the `encrypted` cast — shown once at creation, never displayed again. `RotateWebhookSecretAction` returns a new plaintext secret once and re-encrypts at rest. See [[../../../architecture/patterns/encryption]] and [[../../../security/encryption]].

## HTTPS-only

`CreateWebhookEndpointData` requires `url` to start with `https://` *(assumed)* — non-HTTPS URLs are rejected: "Webhook URLs must use HTTPS."

## Test rate limiter

`SendTestWebhookAction` is throttled (a few test sends per endpoint per minute) — from `build/security-audit-2026-06-11` (medium). Prevents using the test button as an SSRF/spam amplifier.

## Tenancy

`webhook_endpoints` and `webhook_deliveries` are company-scoped via `CompanyScope`; the dispatcher only ever matches endpoints for the event's own `company_id`, so company A's events are never delivered to company B's endpoints. See [[../../../security/tenancy-isolation]].
