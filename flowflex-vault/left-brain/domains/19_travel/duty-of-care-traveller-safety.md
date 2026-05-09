---
type: module
domain: Business Travel
panel: travel
cssclasses: domain-travel
phase: 7
status: planned
migration_range: 918000–920999
last_updated: 2026-05-09
---

# Duty of Care & Traveller Safety

Know where every travelling employee is at all times. Emergency mass communication, destination risk alerts, check-in protocols, and evacuation coordination.

---

## Legal Context

Employers have a legal duty of care for employees travelling for work. UK: Corporate Manslaughter Act 2007. EU: employer liability under national labour law. Practically: if an employee is in a high-risk zone and the company didn't have a system to track and alert them, the company may be liable.

---

## Core Functionality

### Live Traveller Map
- Dashboard showing all employees currently on trips (status: booked + dates active)
- Map view: pin per traveller at their current destination
- Travel timeline: departure, layovers, hotel location, return
- Filter by department, country, risk level

### Destination Risk Intelligence
Risk data sourced from third-party providers (e.g., ISOS, Control Risks, Healix) via API:
- Country risk level: Extreme / High / Medium / Low / Minimal
- Current alerts: political unrest, natural disasters, health advisories, crime incidents
- Travel advisories from UK FCDO, US State Department, NL MFA

At booking time: traveller warned if destination is Medium risk or above.
At booking of High/Extreme risk: automatic HR + Security team notification + mandatory safety briefing acknowledgement.

### Safety Check-Ins
For High/Extreme risk destinations:
- Scheduled check-in prompts sent to traveller: "Are you safe? Reply OK or call +XX XXXX"
- Check-in methods: in-app tap, SMS, email reply
- Missed check-in → alert escalation to HR + emergency contact
- Frequency: daily for Extreme risk, on departure/arrival for High risk

### Emergency Mass Alert
When a critical incident occurs (e.g., earthquake, terror attack, civil unrest):
1. Admin identifies affected area (draw polygon on map or select country/city)
2. All travellers in affected area shown with contact details
3. Mass alert sent: push + SMS + email: "URGENT: Please confirm your safety"
4. Response tracker: confirmed safe / unconfirmed (colour-coded)
5. Automated escalation for non-responders

### Emergency Contact Registry
- Each employee registers emergency contact (name, phone, relationship)
- Visible to HR and security team in emergency situations
- GDPR: stored with explicit consent, used only in genuine emergencies

---

## Data Model

### `travel_safety_checkins`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| trip_id | ulid | FK |
| scheduled_at | datetime | |
| responded_at | datetime | nullable |
| response_method | enum | app/sms/email/phone |
| status | enum | pending/safe/missed |

### `travel_risk_alerts`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| country_code | char(2) | |
| city | varchar | nullable |
| risk_level | enum | minimal/low/medium/high/extreme |
| alert_title | varchar(300) | |
| alert_body | text | |
| source | varchar(100) | "FCDO" / "ISOS" / manual |
| issued_at | datetime | |
| expires_at | datetime | nullable |

---

## Integrations

- **HR** — employee emergency contacts, manager notification tree
- **IT** — employee phone numbers for SMS alerts
- **External risk API** — ISOS, Control Risks, or similar (configurable provider)
- **Finance** — emergency repatriation cost tracking

---

## Migration

```
918000_create_travel_safety_checkins_table
918001_create_travel_risk_alerts_table
918002_create_travel_emergency_contacts_table
918003_create_travel_incident_responses_table
```

---

## Related

- [[MOC_Travel]]
- [[travel-booking-portal]]
- [[trip-approvals-workflow]]
- [[MOC_HR]] — employee records, emergency contacts
