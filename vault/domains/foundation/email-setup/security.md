---
domain: foundation
module: email-setup
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Email Setup — Security

## Permissions

None — no user-facing panel actions. The one write path (bounce webhook → `users.email_deliverable`) is authenticated by **webhook signature** (`VerifyResendSignature`), not a permission; the route is stateless (no session/CSRF) and rate-limited (`throttle:60,1`). See the Controls table below.

## Controls

| Control | Implementation | Status |
|---|---|---|
| Webhook signature verification | `VerifyResendSignature` middleware on `POST /resend/webhook` — rejects unsigned/invalid before the controller | verified present |
| Rate limiting | `throttle:60,1` on the webhook route | verified |
| No CSRF/session | stateless API route — signature is the auth | verified |
| Suppression | hard bounce sets `email_deliverable = false`; sends to those addresses are skipped | verified (test) |

> The 2026-06-11 security audit flagged signature verification (HIGH) and a webhook throttle (medium) as gaps. Both are now implemented in `routes/api.php`.

> [!warning] UNVERIFIED — needs confirmation: secret env var + header
> The concrete header name and secret env var live inside `VerifyResendSignature`; not read here.

## Related

- [[_module|Email Setup]] · [[api|API]]
- [[../../../security/webhooks-signing]] · [[../../../security/threat-model]]
