---
type: gap
severity: low
category: feature
status: open
domain: workplace
color: "#F97316"
discovered: 2026-07-03
discovered-in: workplace.visitors
---

# Visitor QR pre-registration → fast kiosk check-in missing

## Context
`workplace.visitors` specs pre-registration, a kiosk check-in page, badge PDF (`GenerateVisitorBadgeJob`),
and an optional NDA gate — but **no QR-code path**. The kiosk currently identifies a pre-registered guest
by decrypting today's expected visitors in memory (`data-model.md` / `unknowns.md` flag this as a design
cost, since `name`/`email` are encrypted and not plaintext-searchable).

## Problem
QR check-in is table stakes for the VMS incumbents FlowFlex Workplace displaces (Envoy, SwipedOn): a
pre-registered visitor receives a QR token in their confirmation email and scans it at the kiosk for a
one-tap arrival. Beyond parity, a QR token would let the kiosk resolve the exact visitor record directly
instead of decrypting the whole expected set — turning the noted lookup pain into a non-issue.

## Impact
- Slower, less polished reception experience than the tools being replaced.
- The encrypted-visitor in-memory lookup (an open `unknowns` item) stays unresolved.

## Proposed Solution
Issue a signed QR token on pre-registration and accept it at the kiosk, using the **already-chosen**
`simplesoftwareio/simple-qrcode` (in the stack for "event tickets, check-in QR") — no new packages. The
token maps to the `wp_visitors` row id (rate-limited, kiosk-role only per the existing kiosk ADR), so
check-in needs no plaintext search. Spec the token contents + expiry in the check-in feature note.

## Related
- [[../../domains/workplace/visitor-management/_module]] · [[../../domains/workplace/visitor-management/unknowns]]
- [[../../domains/workplace/_opportunities]] · [[../../security/encryption]]
