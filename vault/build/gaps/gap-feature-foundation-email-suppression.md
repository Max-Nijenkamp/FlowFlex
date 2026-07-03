---
type: gap
severity: medium
category: feature
status: accepted
domain: foundation
color: "#F97316"
discovered: 2026-07-03
discovered-in: foundation.email
---

# Gap: Email suppression handles hard bounces only — no complaint / soft-bounce list

## Context

[[../../domains/foundation/email-setup/features/bounce-webhook|bounce-webhook]] receives Resend events and,
on a **hard bounce**, sets `users.email_deliverable = false`. Complaint (spam-report) events and repeated
**soft** bounces are explicitly out of scope (see the feature's own UNVERIFIED note), and suppression is a
single boolean on `users` — there is no suppression list, no transactional-vs-marketing isolation.

## Problem

In 2025 Gmail enforces a max spam-complaint rate of 0.3% and Microsoft began (May 2025) rejecting
non-compliant bulk mail to Outlook/Hotmail/Live. Continuing to mail an address that filed a complaint, or
one that soft-bounces repeatedly, degrades sender reputation for **every** tenant on the shared sending
domain. A hard-bounce-only flag misses the two failure modes (complaints, persistent soft bounces) that do
the most reputational damage.

## Impact

Deliverability risk is platform-wide (one bad tenant's complaints hurt all), and it undercuts the "reliable
transactional email" substrate every domain depends on ([[../../domains/foundation/email-setup/_module|foundation.email]]).
Package-fit — handled with the existing mailer + webhook, plus a suppression table.

## Proposed Solution

Handle Resend `complaint` and `bounce` (soft, thresholded — e.g. ≥5 consecutive) events in
`HandleEmailBounceAction`; record them in a `email_suppressions` table (address, reason, first/last seen)
rather than only a boolean, and check it in [[../../domains/foundation/email-setup/features/branded-mailable|branded-mailable]]
before send. Keep transactional suppression isolated from any future marketing suppression so a newsletter
bounce never blocks an account/security notice.

## Sources

- [Suppression must cover complaints + soft-bounce thresholds; isolate transactional from marketing (MailerSend)](https://www.mailersend.com/features/suppression-list-management) (accessed 2026-07-03)
- [Gmail 0.3% complaint cap; Microsoft May-2025 bulk-mail rejection; hard/soft/complaint suppression (MessageGears)](https://support.messagegears.com/hc/en-us/articles/37304213923213-Email-Deliverability-Best-Practices-Suppression-Lists-and-Blocklists) (accessed 2026-07-03)
