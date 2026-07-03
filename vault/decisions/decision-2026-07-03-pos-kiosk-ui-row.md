---
type: adr
date: 2026-07-03
status: proposed
domain: All
color: "#F97316"
---

# Kiosk / scan-station — add a UI-strategy row for shared-terminal, full-screen, scan/touch-first surfaces

## Context

Wave 3a feature-gap research (2026-07) surfaced the same UI shape in three separate domains that the
[[architecture/ui-strategy]] decision table (rows 1–19) has **no kind for**:

- **Operations** — a **warehouse scanning station**: a shared, wall-/counter-mounted terminal where any
  worker scans to receive / count / move stock. Fixed-mount and self-service scanning stations are a
  recurring SME warehouse request. ([[../domains/operations/_opportunities]] 2026-07 refresh; the barcode-label
  gap [[../build/gaps/gap-feature-inventory-barcode-labels]] is the print half of the same loop.)
- **Workplace / Events** — a **visitor / attendee check-in kiosk**: QR pre-registration → self check-in at a
  front-desk tablet. Already logged as a spec hole ([[../build/gaps/gap-feature-visitor-qr-checkin]]); events
  registration also issues check-in QR (`simplesoftwareio/simple-qrcode`).
- **E-commerce** — a **retail / counter POS** surface (out of v1 scope, but the same shape).

All three share properties none of the existing rows capture: **shared device (not a per-user session),
full-screen with panel chrome hidden, large touch targets, scanner-as-keyboard input, and often an
unauthenticated or device-scoped guard.** Rows #8 (inbox/chat) and #19 (floor map) are the closest custom-page
kinds but assume a normal authenticated back-office operator inside the panel shell.

This is the same class of miss the 2026-06-11 ui-strategy ADR ([[decisions/decision-2026-06-11-ui-strategy-new-rows]])
and the open [[../build/gaps/gap-two-panel-matcher-ui-row-missing]] gap address: a real screen kind with no row,
so builders cite a row that doesn't fit.

## Options Considered

1. **Reuse existing rows** — build each kiosk as a plain Custom Filament Page (#8-style) or a Vue+Inertia
   public page (#16). *Rejected as the silent default:* neither row documents the kiosk constraints
   (chrome-off, shared session, scanner input, device guard), so each domain would re-invent them
   inconsistently — exactly the divergence the frozen decision table exists to prevent.
2. **Add one new row #20 "Kiosk / scan-station"** with an explicit tech recommendation and constraint note.
   *Preferred.*
3. **Two rows** (authenticated in-panel scan-station vs fully-public kiosk). *Deferred* — likely overkill;
   one row with a "guard" column note can cover both until a second consumer proves the split.

## Decision (proposed)

Add **row #20 — Kiosk / scan-station (shared-terminal, full-screen, touch/scan-first)** to
[[architecture/ui-strategy]]:

| # | Screen kind | Tech | Package(s) | Realtime | Example |
|---|---|---|---|---|---|
| 20 | Kiosk / scan-station (shared terminal, chrome hidden, touch/scan-first) | **Custom Filament Page** in kiosk mode (render hooks strip sidebar/topbar; device-scoped guard) — or **Vue 3 + Inertia** when fully public/unauthenticated | Page + `simplesoftwareio/simple-qrcode`; Filament panel render hooks ([[architecture/patterns/filament-panel-chrome]]) | Polling / none | `operations.inventory` scan-station, `workplace.visitors` check-in, `events.registrations` check-in |

Open sub-question for the build-time owner: **Filament-page-in-kiosk-mode vs Vue+Inertia** per surface —
authenticated shared-staff terminals (warehouse) fit a chrome-stripped Filament page; unauthenticated public
kiosks (visitor lobby) fit Vue+Inertia under a scoped guard. The row lists both; the picker is per-consumer.
No new package required — `simplesoftwareio/simple-qrcode`, Filament, and Vue+Inertia are all already chosen.

Status is **proposed**: raising for the architecture owner to ratify (or fold into a broader ui-strategy
refresh) rather than freezing unilaterally, since [[_meta/spec-template]] and the ui-strategy table are
change-controlled.

## Consequences

- If ratified: the three kiosk/scan gaps get a row to cite; `operations`, `workplace`, `events` (and later
  `ecommerce`) stop improvising kiosk chrome. Row #20 joins the frozen table via the same amendment path as
  rows 17–19.
- If rejected: kiosk surfaces stay as ad-hoc #8/#16 builds — acceptable only if v1 truly ships none (visitor
  check-in and warehouse scan-station are both Phase 2/3, so there is time to decide before first build).
- Security: shared-device surfaces need an explicit guard decision (device token / scoped guest guard) and
  rate limiting — must be pinned in [[architecture/security]] before the first kiosk build, regardless of row.

## Related

- [[architecture/ui-strategy]] · [[decisions/decision-2026-06-11-ui-strategy-new-rows]] · [[architecture/patterns/filament-panel-chrome]]
- Gaps: [[../build/gaps/gap-feature-visitor-qr-checkin]] · [[../build/gaps/gap-feature-inventory-barcode-labels]] · [[../build/gaps/gap-feature-it-asset-qr-labels]]
- [[../domains/operations/_opportunities]] · [[../domains/it/_opportunities]] (2026-07 refresh)
