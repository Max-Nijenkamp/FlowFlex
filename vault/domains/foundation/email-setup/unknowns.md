---
domain: foundation
module: email-setup
type: unknowns
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Email Setup — Unknowns

Parent: [[_module]].

| # | Item | State |
|---|---|---|
| 1 | Resend signature scheme — header name, secret env var (e.g. Svix `RESEND_WEBHOOK_SECRET`), reject status code | UNVERIFIED — middleware body not read ([[api]], [[security]]) |
| 2 | Soft-bounce / complaint handling (vs. hard-bounce → `email_deliverable=false`) | *(assumed)* — only hard-bounce confirmed |
| 3 | Whether branding (logo/colour) is cached per company or resolved every render | *(assumed)* render-time from `CompanyContext` |
| 4 | Production sender domain / SPF-DKIM-DMARC setup | UNVERIFIED — deployment concern ([[../../../infrastructure/mail]]) |
| 5 | Retry/backoff policy for the `notifications` queue on transient send failure | *(assumed)* Laravel defaults |

## Related

- [[_module]] · [[api]] · [[security]] · [[../../../infrastructure/mail]] · [[../../../security/webhooks-signing]]
