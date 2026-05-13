---
type: module
domain: Workplace & Facility
panel: workplace
module-key: workplace.visitors
status: planned
color: "#4ADE80"
---

# Visitor Management

> Visitor pre-registration, arrival check-in, host notification, and visitor badge printing.

**Panel:** `workplace`
**Module key:** `workplace.visitors`

---

## What It Does

Visitor Management digitises the front-desk experience. Employees pre-register expected visitors — providing their name, company, purpose, and arrival date — and the visitor receives a pre-arrival email with directions and any required pre-visit document acknowledgment (e.g. NDA, health and safety notice). On arrival, visitors check in at a self-service kiosk or via a reception tablet, and their host is immediately notified by push notification or Slack. Visitor logs are maintained for compliance and building security purposes.

---

## Features

### Core
- Visitor pre-registration: host provides visitor name, company, email, and expected arrival date/time
- Pre-arrival email: visitor receives a confirmation email with directions and arrival instructions
- Self-service check-in: visitor enters name or scans a QR code from their pre-arrival email
- Host notification: instant notification to host on visitor arrival (in-app, email, or Slack)
- Visitor log: timestamped record of all visitors, check-in, and check-out times
- Document acknowledgment: optional pre-visit or on-arrival document sign-off (NDA, visitor policy)

### Advanced
- Visitor badge printing: generate and print a visitor badge with name, host, visit date, and QR code
- Multi-site support: manage visitor logs separately per office building
- Watch list check: optional integration to cross-reference visitor against a denied-visitor list
- Emergency evacuation list: real-time list of all currently checked-in visitors per building
- Recurring visitor: save a visitor profile for frequent visitors to speed up future registrations

### AI-Powered
- Expected visitor prediction: flag when a registered visitor has not checked in by their expected arrival time
- Visitor pattern analysis: identify peak visitor hours for reception staffing optimisation
- Anomaly detection: alert when visitor volume is unusually high or low

---

## Data Model

```erDiagram
    visitor_registrations {
        ulid id PK
        ulid company_id FK
        ulid host_id FK
        string visitor_name
        string visitor_company
        string visitor_email
        string purpose
        string building_id
        datetime expected_at
        datetime checked_in_at
        datetime checked_out_at
        string status
        string badge_url
        timestamps created_at_updated_at
    }

    visitor_document_acknowledgments {
        ulid id PK
        ulid registration_id FK
        string document_name
        string document_url
        timestamp acknowledged_at
        string signature_data
    }

    visitor_registrations ||--o{ visitor_document_acknowledgments : "requires"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `visitor_registrations` | Visit records | `id`, `company_id`, `host_id`, `visitor_name`, `expected_at`, `checked_in_at`, `status` |
| `visitor_document_acknowledgments` | Document sign-offs | `id`, `registration_id`, `document_name`, `acknowledged_at` |

---

## Permissions

```
workplace.visitors.pre-register
workplace.visitors.check-in
workplace.visitors.view-log
workplace.visitors.manage-documents
workplace.visitors.export-evacuation-list
```

---

## Filament

- **Resource:** `App\Filament\Workplace\Resources\VisitorRegistrationResource`
- **Pages:** `ListVisitorRegistrations`, `CreateVisitorRegistration`, `ViewVisitorRegistration`
- **Custom pages:** `VisitorCheckInKioskPage`, `EvacuationListPage`, `VisitorLogPage`
- **Widgets:** `TodayVisitorsWidget`, `CurrentlyOnSiteWidget`
- **Nav group:** Visitors

---

## Displaces

| Feature | FlowFlex | Envoy | Proxyclick | SwipedOn |
|---|---|---|---|---|
| Pre-registration | Yes | Yes | Yes | Yes |
| Host notification | Yes | Yes | Yes | Yes |
| Badge printing | Yes | Yes | Yes | Yes |
| Evacuation list | Yes | Yes | Yes | Yes |
| Native HR host lookup | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[office-spaces]] — visitor check-in scoped to a building
- [[desk-booking]] — visitor arrival can prompt host to book a space
- [[hr/INDEX]] — host employee records used for lookup and notification
