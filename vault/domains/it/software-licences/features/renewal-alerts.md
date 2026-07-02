---
domain: it
module: software-licences
feature: renewal-alerts
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Renewal Alerts

Surface upcoming licence renewals and flagged seats on a dashboard widget, and send a renewal notification once per cycle via a daily command.

## Behaviour

- `LicenceRenewalWidget` lists renewals in the next 60 days plus seats currently flagged for reclaim.
- `LicenceRenewalAlertCommand` runs daily, notifies 30 days before `renewal_date`.
- **Once per cycle**: `renewal_alerted_at` is the once-guard — set when the alert fires; a licence is alerted at most once per renewal cycle. Cleared when `renewal_date` changes so the next cycle alerts again (see [[../decisions|software-licences.decisions]]).

## UI

- **Kind**: widget + background — dashboard widget plus a scheduled console command ([[../../../../architecture/ui-strategy]]).
- **Page**: `LicenceRenewalWidget` on the IT/Licences dashboard (nav group Licences). Command `LicenceRenewalAlertCommand` has no UI.
- **Layout**: widget — two lists, "Renewals next 60 days" (software, vendor, renewal date, days out) and "Flagged seats" (employee, licence, flagged date).
- **Key interactions**: click a renewal row → open its `LicenceResource` record; command runs on schedule (notifications queue) and dispatches notifications.
- **States**: empty (no upcoming renewals / no flagged seats → "Nothing due" message) · loading (skeleton list) · error (widget shows last-known + retry) · selected (row → licence record).
- **Gating**: widget visible with `it.licences.view-any` + `BillingService::hasModule('it.licences')`; command runs system-side under company context.

## Data

- Owns / writes: `it_licences` (`renewal_alerted_at` once-guard) and reads `it_licence_assignments` (flagged seats) — own tables only.
- Reads: own module tables; notification dispatch via core.notifications.
- Cross-domain writes: none — writes only its own module tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: notifications via core.notifications (renewal-due alert).
- Shared entity: none.

## Unknowns

- `*(assumed)*` 30-day alert lead time and 60-day widget horizon — see [[../unknowns|software-licences.unknowns]].

## Related

- [[../_module|Software Licences]] · [[licence-record]] · [[offboarding-seat-reclaim]] · [[../architecture|architecture]]
