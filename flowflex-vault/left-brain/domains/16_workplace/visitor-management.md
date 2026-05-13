---
type: module
domain: Workplace & Facility Management
panel: workplace
cssclasses: domain-workplace
phase: 6
status: complete
migration_range: 860000–862999
last_updated: 2026-05-12
---

# Visitor Management

Pre-register visitors, manage sign-in/sign-out at kiosk, print badges, notify hosts, and maintain a visitor log for security compliance.

---

## Core Functionality

### Pre-Registration
- Host creates visitor invite from Filament or via email link
- Invite sends visitor a confirmation with: address, parking, arrival instructions, QR code
- Visitor can complete pre-registration form before arrival: company, NDA acceptance (if required), photo upload

### Kiosk Sign-In
- Tablet kiosk app (iPad/Android) at reception
- Sign-in flow: scan QR code OR enter name → verify identity → photo capture → print badge → notify host
- Walk-in flow (no pre-registration): enter name + company + host lookup → photo → badge
- NDA signature capture on kiosk (touch-sign)
- GDPR notice displayed on kiosk — consent to photo/data stored

### Badge Printing
- Zebra/Dymo label printer integration via WebUSB or local print agent
- Badge template: visitor name, company, host name, date, visitor type label, expiry time
- QR code on badge for gate/turnstile scan
- Visitor type stickers (colours): Guest, Contractor, Interview Candidate, Delivery

### Host Notifications
| Trigger | Channel |
|---|---|
| Visitor arrives at reception | Push notification + SMS (if configured) |
| Visitor waiting > 5 min | Escalation push to backup contact |
| Visitor signs out | Email summary to host |

### Sign-Out
- Kiosk sign-out or reception staff manual sign-out
- Badge invalidated (QR deactivated)
- Auto sign-out at end of day (23:59) for anyone not signed out

---

## Compliance & Security

- Visitor log: immutable record of all arrivals/departures (cannot be deleted, only exported)
- NDA storage: signed NDA PDF stored in tenant document storage, linked to visitor record
- GDPR: visitor photo and personal data deleted after configurable retention period (default 90 days)
- Emergency muster: export current in-building visitor list for evacuation roll call
- Blocked visitor list: flag individuals who should not be admitted → alert reception on sign-in attempt

---

## Data Model

### `workplace_visitors`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| full_name | varchar(200) | |
| company | varchar(200) | nullable |
| email | varchar | nullable |
| photo_path | varchar | nullable, GDPR-purged after retention |
| nda_signed_at | timestamp | nullable |
| blocked | bool | default false |

### `workplace_visitor_visits`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| visitor_id | ulid | FK |
| host_employee_id | ulid | FK |
| building_id | ulid | FK |
| expected_at | datetime | nullable |
| signed_in_at | datetime | |
| signed_out_at | datetime | nullable |
| badge_printed | bool | |
| badge_number | varchar(20) | |
| visit_type | enum | guest/contractor/candidate/delivery |
| notes | text | nullable |

---

## Integrations

- **HR** — employee directory for host lookup
- **IT** — auto-generate guest WiFi code on arrival, sent to visitor SMS/email
- **Legal** — NDA documents stored in legal document vault

---

## Migration

```
860000_create_workplace_visitors_table
860001_create_workplace_visitor_visits_table
860002_create_workplace_visitor_blocked_list_table
```

---

## Related

- [[MOC_Workplace]]
- [[hot-desk-space-booking]]
- [[MOC_IT]] — guest WiFi provisioning
- [[MOC_Legal]] — NDA document storage
