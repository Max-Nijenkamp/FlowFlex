---
domain: communications
module: email-channel
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Email Channel — Unknowns

## Assumed Items

- v1 inbound address format `{token}@inbound.flowflex.io` *(assumed)*.
- Outbound provider = Resend with custom from + reply-to *(assumed)*.
- Spam filter = provider spam-score header threshold *(assumed)*.

## Open Questions

> [!warning] UNVERIFIED
> Inbound-relay provider (Resend vs. Mailgun vs. Postmark) and DMARC/SPF/DKIM setup for sending *as* the customer's address are undocumented — deliverability + spoofing implications.

- OAuth (Gmail/Outlook) connection UX and scopes for v1.x.
- How is the forwarding-address setup verified (test email round-trip)?
- Subject-fallback threading collision risk (two unrelated threads, same subject) — acceptable?
- Attachment size caps + inline-image handling.

## Related

- [[_module]] · [[decisions]] · [[../../../architecture/email]]
