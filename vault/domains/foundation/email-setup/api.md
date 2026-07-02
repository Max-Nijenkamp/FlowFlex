---
domain: foundation
module: email-setup
type: api
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Email Setup — API

One inbound endpoint, verified in `routes/api.php`.

## `POST /api/resend/webhook`

| Aspect | Value |
|---|---|
| Controller | `ResendWebhookController` (invokable) |
| Middleware | `VerifyResendSignature`, `throttle:60,1` |
| Session/CSRF | none (stateless API route) |
| Name | `webhooks.resend` |
| Purpose | bounce/complaint events → flag `users.email_deliverable` via `HandleEmailBounceAction` |

```mermaid
sequenceDiagram
    Resend->>+API: POST /api/resend/webhook (signed)
    API->>API: VerifyResendSignature
    alt invalid
        API-->>Resend: reject (no state change)
    else valid
        API->>HandleEmailBounceAction: run(email, type)
        HandleEmailBounceAction->>DB: email_deliverable = false (hard bounce)
        API-->>-Resend: 200
    end
```

> [!warning] UNVERIFIED — needs confirmation: exact signature scheme
> Verified that `VerifyResendSignature` middleware exists and gates the route. The exact header name / secret env var (e.g. Svix `RESEND_WEBHOOK_SECRET`) and the rejection status code were not read from the middleware body here.

## Related

- [[_module|Email Setup]] · [[security|Security]]
- [[../../../security/webhooks-signing]]
