---
domain: foundation
module: email-setup
feature: branded-mailable
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Branded, Queued Mailable (`FlowFlexMailable`)

Every outbound mail is company-branded and sent off the request via the queue — the base class all domain mailables extend.

## Behaviour

- `FlowFlexMailable` injects the company name, logo, and primary colour, resolving branding from `CompanyContext` at render time.
- All mailables `ShouldQueue` → the `notifications` queue; queued mails carry `company_id` so `WithCompanyContext` restores the right tenant before render.
- Local: Mailpit captures everything (internal `:8025`); production: Resend SMTP.
- Suppression: an address with `email_deliverable = false` (hard-bounced) is skipped ([[bounce-webhook]]).

## UI

- **Kind**: background (mail delivery — no in-app screen). The rendered email itself is the "UI"; branding is
  the visible surface, previewable in Mailpit locally.

## Data

- Owns: no tables. Reads: `companies` branding fields (own-company, via context) + `users.email_deliverable`.
- Cross-domain writes: none.

## Relations

- Consumes: any domain that sends mail extends this class. Feeds: nothing (terminal side-effect).
- Shared entity: company branding (name/logo/colour), owned by [[../../../../domains/core/company-settings/_module|company-settings]].

## Test Checklist

### Unit
- [ ] `FlowFlexMailable` resolves name / logo / primary colour from the current `CompanyContext`

### Feature (Pest)
- [ ] Rendered mail contains the sending company's branding (`MailBrandingTest`)
- [ ] Mailable is queued on `notifications`, never sent synchronously
- [ ] A queued mail restores the correct tenant via `WithCompanyContext` — no cross-company branding
- [ ] An address with `email_deliverable = false` is skipped

## Unknowns

> [!warning] UNVERIFIED — whether branding is cached per company or resolved every render; production sender
> domain / DKIM setup. See [[../unknowns]].

## Related

- [[../_module|Email Setup]] · [[bounce-webhook]] · [[../../queue-workers/_module|Queue Workers]] · [[../../../../architecture/email]]
