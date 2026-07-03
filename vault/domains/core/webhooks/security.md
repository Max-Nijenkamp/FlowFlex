---
domain: core
module: webhooks
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Webhooks — Security

Parent: [[_module]]

## Permissions

`core.webhooks.view-any` · `core.webhooks.create` · `core.webhooks.update` · `core.webhooks.delete` · `core.webhooks.test` · `core.webhooks.rotate`

One permission per command: `test` (`SendTestWebhookAction`) and `rotate` (`RotateWebhookSecretAction`, mints a new secret) each have their own verb on top of the CRUD set. Seeded in `PermissionSeeder`.

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

## Rate limiting (test + rotate)

`SendTestWebhookAction` names the `panel-action` limiter (a few test sends per endpoint per minute) — from `_archive/build-history/security-audit-2026-06-11` (medium). It calls an external URL, so throttling prevents using the test button as an SSRF/spam amplifier. `RotateWebhookSecretAction` also names the `panel-action` limiter since it mints a credential (per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]]). Both return 429 + `Retry-After` on exhaustion.

## Tenancy

`webhook_endpoints` and `webhook_deliveries` are company-scoped via `CompanyScope`; the dispatcher only ever matches endpoints for the event's own `company_id`, so company A's events are never delivered to company B's endpoints. See [[../../../security/tenancy-isolation]].
