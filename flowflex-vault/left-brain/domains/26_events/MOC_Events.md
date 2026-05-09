---
type: moc
domain: Events Management
panel: events
phase: 5
color: "#EC4899"
cssclasses: domain-events
last_updated: 2026-05-09
---

# Events Management — Map of Content

End-to-end event management: creation, registration, ticketing, attendee management, session scheduling, check-in, and post-event analytics. Covers in-person, virtual, and hybrid. Replaces Eventbrite, Cvent, Splash, Bevy, and Bizzabo.

**Panel:** `events`  
**Phase:** 5  
**Migration Range:** `990000–994999`  
**Colour:** Pink `#EC4899` / Light: `#FDF2F8`  
**Icon:** `heroicon-o-calendar-days`

---

## Why This Domain Exists

B2B companies run events constantly: webinars, user conferences, customer roundtables, training workshops, product launches, partner events. Current tools:
- Cvent: enterprise only (€30k+/yr)
- Eventbrite: takes 2–3.5% of ticket revenue + fees
- Hopin: shut down (product sold to RingCentral)
- Splash/Bizzabo: €10k–30k/yr

FlowFlex Events integrates with CRM (auto-log attendees as contacts), Marketing (event campaigns), and LMS (events as training sessions).

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| Event Creation & Branding | 5 | planned | Event setup, branding, agenda, venue/virtual, capacity |
| Registration & Ticketing | 5 | planned | Registration forms, ticket types, promo codes, waitlists |
| Attendee Management | 5 | planned | Registrant list, check-in, dietary/accessibility requirements |
| Session & Speaker Management | 5 | planned | Multi-track agenda, speaker profiles, session recordings |
| Event Check-In App | 5 | planned | QR code scan, walk-in registration, badge printing |
| Post-Event Analytics | 5 | planned | Attendance rates, session ratings, NPS, lead generation |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `EventRegistered` | Registration | CRM (create/update contact), Marketing (add to campaign), Notifications |
| `EventAttended` | Check-In | CRM (update contact activity), CS (customer engagement) |
| `EventCancelled` | Event Management | Notifications (all registrants), Finance (refund trigger) |
| `SessionRated` | Post-Event | Analytics (speaker/session scores), LMS (if training event) |

---

## Filament Panel Structure

**Navigation Groups:**
- `Events` — All Events, Calendar View, Past Events
- `Registration` — Registrant Lists, Ticket Types, Promo Codes
- `Agenda` — Sessions, Speakers, Tracks
- `On-Site` — Check-In Dashboard, Walk-In, Badge Print
- `Analytics` — Attendance Reports, Engagement, Lead Report

---

## Permissions Prefix

`events.management.*` · `events.registration.*` · `events.sessions.*`  
`events.checkin.*` · `events.analytics.*`

---

## Competitors Displaced

Eventbrite · Cvent · Bizzabo · Splash · Bevy · Whova · Hopin (defunct) · Zoom Events

---

## Related

- [[MOC_Domains]]
- [[MOC_Marketing]] — event promotion campaigns
- [[MOC_CRM]] — attendees → CRM contacts
- [[MOC_LMS]] — training events → course completions
- [[MOC_Communications]] — event communications
