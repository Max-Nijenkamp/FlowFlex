---
domain: workplace
type: opportunities
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Workplace & Facility - Opportunity Radar

Web-researched (2024-2026) tooling gaps and repeatedly-requested capabilities that the incumbents
(Envoy, Robin, OfficeSpace, Skedda, YAROOMS, SwipedOn / Sign In App) either lack, gate behind
expensive tiers, or split across point tools. Each is a candidate differentiator for FlowFlex Workplace.
Sourced + dated; speculative sizing is marked `UNVERIFIED`. Constitution:
[[../../decisions/decision-2026-06-20-full-mapping-conventions]].

> [!note] How to read this
> "Gap" = what is missing / painful in the market. "FlowFlex angle" = how our bounded, all-in-one,
> event-driven architecture (workplace + HR + finance in one tenant) could exploit it. Angles are design
> bets, not commitments - `UNVERIFIED`.

---

## Candidates

### 1. One bundle, not three point tools (consolidation)
- **Gap**: platform consolidation is the dominant 2025-2026 buyer priority (Verdantix, Gartner); stitching
  desk + room + visitor + analytics point tools creates integration headaches. Only YAROOMS/Maptician
  bundle the full set; most SMBs run 3-4 tools.
- **FlowFlex angle**: Workplace ships rooms + desks + visitors + maintenance + analytics in ONE panel,
  already inside the same tenant as HR + Finance - zero integration project. Ties [[room-booking/_module|rooms]]
  ... [[workplace-analytics/_module|analytics]].
- Sources: yarooms.com/reports/best-hybrid-workplace-software (2026), maptician.com guide (2026). `UNVERIFIED` on win-rate lift.

### 2. Ghost-booking / no-show recovery built in (not an add-on)
- **Gap**: 30-40% of room capacity is lost to ghost bookings (recurring meetings never cancelled,
  placeholders, no-shows). Auto-release + check-in enforcement recover most of it, but many tools treat it
  as a premium feature.
- **FlowFlex angle**: no-show auto-release is core in both [[room-booking/_module|rooms]] (start+15m) and
  [[desk-booking/_module|desks]] (11:00 cutoff), surfaced in [[workplace-analytics/_module|analytics]]
  no-show rate - not upsold.
- Sources: skedda.com room-booking-software-hybrid-meetings (2025), archieapp.co best-booking-systems (2026). `UNVERIFIED` on % recovered per tenant.

### 3. Utilisation truth without a sensor contract
- **Gap**: badge/booking data overstates usage; accurate occupancy needs PIR/thermal/mmWave sensors, an
  extra hardware + platform spend. 66% of orgs run <60% utilisation and cannot trust their own numbers.
- **FlowFlex angle**: [[workplace-analytics/_module|analytics]] pairs booking data with check-in stamps
  (booked-vs-actually-checked-in) to approximate real occupancy with zero hardware; a sensor webhook is a
  clean later add. `UNVERIFIED` - check-in is a proxy, not a sensor.
- Sources: butlr.com occupancy-sensor-solutions-2025, basking.io top-10-occupancy-analytics-2025, skedda.com space-utilization-vs-occupancy (2025).

### 4. Visitor + HR host directory in one system
- **Gap**: visitor tools bolt onto an external HR/directory sync; host lookup + emergency roster drift out
  of date. SMB VMS (SwipedOn) keeps host lists manually.
- **FlowFlex angle**: [[visitor-management/_module|visitors]] reads the live `hr.profiles` employee
  directory read-only - hosts are always current, no sync job. Emergency roster = today's visitors +
  checked-in employees in one query.
- Sources: gable.to best-visitor-management-software (2026), cm-alliance.com best-vms (2025). `UNVERIFIED` on roster-accuracy claim.

### 5. Watchlist / block-list screening for SMBs
- **Gap**: watchlist + ID + access-control screening is real (Envoy: SOC2/GDPR/SB553/PCI), but positioned
  as enterprise; SMB tools "don't need watchlist screening" per market guidance - so SMBs get nothing.
- **FlowFlex angle**: a lightweight per-company block-list check on [[visitor-management/_module|visitor]]
  check-in (deny + alert host/security) is cheap given we already own the visitor record. `UNVERIFIED` -
  scoped in visitor `unknowns`, not yet designed.
- Sources: envoy.com solutions/security-compliance (2025), gable.to best-visitor-management (2026), eportid.com visitor-management-access-control (2025).

### 6. Access-control credential lifecycle tied to check-in
- **Gap**: a common audit gap is temporary badge/credentials issued at check-in but never revoked at
  check-out; best tools integrate access control to auto-revoke.
- **FlowFlex angle**: [[visitor-management/_module|visitor]] check-in/out already stamps precise
  timestamps; a `VisitorArrived` / `VisitorLeft` event (currently undecided) could drive
  issue/revoke on an access-control webhook. `UNVERIFIED` - depends on the undecided event.
- Sources: eportid.com visitor-management-access-control-2025, envoy.com visitor-management-features (2025).

### 7. Facility maintenance + HR onboarding/offboarding join-up
- **Gap**: CMMS (Corrigo, Tractian) and HR onboarding (Sapling, Deel, Factorial) are separate; desk
  assignment + asset return + facility setup for a joiner/leaver span tools with no shared trigger.
- **FlowFlex angle**: because HR fires onboarding/offboarding events in the same tenant,
  [[maintenance/_module|maintenance]] and [[desk-booking/_module|desks]] could react (auto-create a
  "prep desk" request on hire; release desk + flag asset return on exit). `UNVERIFIED` - cross-domain
  events not yet wired.
- Sources: jll.com/products/corrigo (2025), lumos.com employee-onboarding-software (2025), tractian.com best-cmms (2026).

### 8. Preventive maintenance without a CMMS purchase
- **Gap**: recurring/preventive maintenance scheduling is a CMMS feature SMBs often skip because a full
  CMMS is heavy; reactive-only maintenance dominates.
- **FlowFlex angle**: [[maintenance/_module|maintenance]] ships `wp_maintenance_schedules` +
  auto-generated requests (weekly/monthly/quarterly) as a first-class, lightweight feature - preventive
  maintenance for companies that would never buy Corrigo. `UNVERIFIED` on adoption.
- Sources: carbonweb.co cmms-benefits (2025), tractian.com best-cmms-software (2026).

### 9. Predictable pricing (no per-headcount / per-room creep)
- **Gap**: Envoy modular pricing "stacks unpredictably" with renewal hikes; Robin per-headcount cost
  mismatches softening attendance; room tools charge $15-35/room/mo or $3-8/user/mo.
- **FlowFlex angle**: Workplace is one module in the FlowFlex bundle (per [[../../architecture/module-system]]),
  not priced per desk/room/head - cost does not scale with office size or attendance. `UNVERIFIED` -
  pricing model is a product decision.
- Sources: skedda.com best-envoy-alternatives (2026), skedda.com best-robin-alternatives (2026), archieapp.co meeting-room-booking-system-cost (2025).

### 10. Reporting that is not a spreadsheet project
- **Gap**: Envoy exports "arrive split across tabs or missing context", turning floor reshuffles into
  spreadsheet work; OfficeSpace modification options are "constrained".
- **FlowFlex angle**: [[workplace-analytics/_module|analytics]] is a native cached dashboard (apex charts)
  computed from the same tenant's data, with a throttled export - no cross-tab reconciliation. `UNVERIFIED`
  on depth vs incumbents.
- Sources: skedda.com best-envoy-alternatives (2026), selecthub.com officespace-vs-robin (2025).

### 11. Self-service floor-plan edits (no professional-services ticket)
- **Gap**: Robin routes floor-plan changes through its professional-services team; OfficeSpace mods are
  constrained - a change is a support ticket, not a self-serve edit.
- **FlowFlex angle**: [[desk-booking/_module|desks]] floor map is positioned markers over an uploaded floor
  image, editable in-app via the desk resource - admins reshuffle without a vendor ticket. `UNVERIFIED` -
  simpler than CAD/vector floor plans (a trade-off).
- Sources: skedda.com best-robin-alternatives (2026), selecthub.com officespace-vs-robin (2025).

### 12. Team-coordination ("where is my team sitting today")
- **Gap**: hot-desking without support "creates frustration instead of flexibility"; only 25% of firms
  keep assigned seating (down from 56% in 2023) - people cannot find teammates.
- **FlowFlex angle**: [[desk-booking/_module|desks]] team view shows same-day colleague desks on the floor
  map (HR directory read-only), turning hot-desking into deliberate co-location. `UNVERIFIED` - privacy
  opt-out is an open question in desk `unknowns`.
- Sources: skedda.com space-utilization-vs-occupancy (2025), elia.io desk-occupancy (2025), nimway.com 3-tech-trends-2025.

### 13. Integrated `.ics` invites + shared calendar (no Calendly tax)
- **Gap**: even $99/user/mo tools (Close) lack native scheduling; teams bolt on Calendly; room invites are
  a separate calendar step.
- **FlowFlex angle**: [[room-booking/_module|room]] bookings emit `.ics` invites via
  `spatie/icalendar-generator`, and CRM appointment-scheduling already lives in the same tenant - one
  scheduling fabric across meetings + external booking. `UNVERIFIED` - `.ics` flow is *(assumed)* in the room spec.
- Sources: archieapp.co best-booking-systems (2026), skedda.com room-booking-app-calendar-sync (2025).

---

## Related

- [[_index|Workplace MOC]] - [[../../security/data-ownership]] - [[../../architecture/module-system]]
- [[../../decisions/decision-2026-06-20-full-mapping-conventions]]
