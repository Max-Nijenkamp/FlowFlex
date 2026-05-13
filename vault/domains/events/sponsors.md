---
type: module
domain: Events Management
panel: events
module-key: events.sponsors
status: planned
color: "#4ADE80"
---

# Sponsors

> Sponsor management — tiers, benefits, logo, primary contact, and sponsorship payment tracking.

**Panel:** `events`
**Module key:** `events.sponsors`

---

## What It Does

Sponsors manages the commercial relationships with event sponsors. Events teams define sponsorship tiers (e.g. Platinum, Gold, Silver) with the associated benefits (logo placement, booth space, speaking slot, email mentions, attendee list access). Individual sponsor records link to a CRM company and contact, track the sponsorship fee, and record whether the fee has been paid. All logos and assets provided by sponsors are stored against their record for use in event materials.

---

## Features

### Core
- Sponsorship tier definitions: tier name, price, and benefits list per event
- Sponsor record: company name, CRM link, primary contact, assigned tier, fee agreed, payment status
- Logo and asset storage: upload sponsor logo (PNG/SVG) and any other brand assets
- Payment tracking: invoice issued, payment received, and balance outstanding
- Benefits fulfilment: checklist of agreed benefits with fulfilment status

### Advanced
- Multi-event sponsors: one sponsor record linked to sponsorship of multiple events
- Sponsor portal: send a sponsor a link to submit their logo, bio, and contact details
- Contra deals: record non-cash sponsorship (e.g. AV equipment in exchange for branding)
- Booth assignment: assign physical booth locations to sponsors for in-person events
- Sponsorship contract status: track whether the sponsorship agreement has been countersigned

### AI-Powered
- Sponsor prospecting: AI suggests companies from the CRM that would be a good fit for sponsorship based on industry and past event data
- Benefits fulfilment reminder: automatically remind the events team of unfulfilled benefits as the event approaches
- ROI summary: draft a post-event sponsor ROI summary from attendance and engagement data

---

## Data Model

```erDiagram
    sponsorship_tiers {
        ulid id PK
        ulid event_id FK
        string name
        decimal price
        json benefits
        timestamps created_at_updated_at
    }

    event_sponsors {
        ulid id PK
        ulid event_id FK
        ulid tier_id FK
        ulid company_id FK
        ulid crm_account_id FK
        string company_name
        string primary_contact_email
        decimal fee_agreed
        string payment_status
        string logo_url
        boolean contract_signed
        timestamps created_at_updated_at
    }

    sponsorship_tiers ||--o{ event_sponsors : "assigned to"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `sponsorship_tiers` | Tier definitions | `id`, `event_id`, `name`, `price`, `benefits` |
| `event_sponsors` | Sponsor records | `id`, `event_id`, `tier_id`, `company_name`, `fee_agreed`, `payment_status`, `contract_signed` |

---

## Permissions

```
events.sponsors.view
events.sponsors.create
events.sponsors.update
events.sponsors.delete
events.sponsors.manage-tiers
```

---

## Filament

- **Resource:** `App\Filament\Events\Resources\EventSponsorResource`
- **Pages:** `ListEventSponsors`, `CreateEventSponsor`, `EditEventSponsor`, `ViewEventSponsor`
- **Custom pages:** `SponsorPortalPage`, `SponsorshipRevenueReport`
- **Widgets:** `TotalSponsorshipRevenueWidget`, `OutstandingPaymentsWidget`
- **Nav group:** Content

---

## Displaces

| Feature | FlowFlex | Cvent | Eventbrite | Spreadsheet |
|---|---|---|---|---|
| Tier management | Yes | Yes | No | Manual |
| Payment tracking | Yes | Yes | No | Manual |
| Sponsor portal | Yes | Yes | No | No |
| AI sponsor prospecting | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[events]] — sponsors linked to specific events
- [[post-event-analytics]] — sponsorship revenue included in analytics
- [[crm/INDEX]] — sponsor companies linked from CRM accounts
