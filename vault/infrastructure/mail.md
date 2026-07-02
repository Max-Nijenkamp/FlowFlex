---
domain: infrastructure
type: infrastructure
build-status: planned
status: unverified
color: "#F97316"
updated: 2026-06-20
---

# Mail

**Dev:** SMTP → **Mailpit** container (`MAIL_MAILER=smtp`, `MAIL_HOST=mailpit`, `MAIL_PORT=1025`).
Mailpit is internal-only (`expose: 1025/8025`) since the host already runs one — inspect via
`docker compose exec mailpit …` or temporarily publish `:8025`.

**Prod:** Resend / Postmark transport.
> [!warning] UNVERIFIED — needs confirmation: production mail provider
> No production mail config exists in the repo. Resend is referenced (there is a real
> `ResendWebhookController` + `VerifyResendSignature` for bounce handling), but the prod transport
> is not provisioned. Treat prod mail as planned.

## Platform mail today

- `FlowFlexMailable` base (always `ShouldQueue`), Switchboard+ markdown theme.
- Inbound: Resend bounce/complaint webhook → `HandleEmailBounceAction` (signature-verified, throttled).
- Verification emails mandatory across panels; email change resets verification.

Conventions: [[../architecture/email]]. Spec: [[../domains/foundation/email-setup/_module]]. Webhook signing:
[[../security/webhooks-signing]].

## Related

- [[docker-stack]] · [[_moc|Infrastructure MOC]]
