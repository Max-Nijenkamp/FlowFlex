---
type: module
domain: Events Management
panel: events
module-key: events.events
status: planned
color: "#4ADE80"
---

# Events

> Event creation — name, date, venue or virtual link, capacity, ticket types, and event lifecycle status management.

**Panel:** `events`
**Module key:** `events.events`

---

## What It Does

Events is the master record for every corporate event managed in FlowFlex. Event managers create events with all the essential details — name, description, date and time, location (physical or virtual), maximum capacity, and ticket types (free, paid, invitation-only). Events progress through a status lifecycle from draft through published, live, and completed. Published events generate a registration page that can be embedded on the company website or shared as a direct link.

---

## Features

### Core
- Event creation: name, description, cover image, start and end datetime, timezone
- Event type: in-person, virtual, or hybrid
- Venue details: physical address with map embed, or virtual link (Zoom, Teams, Webex)
- Capacity management: maximum attendee count with waitlist activation when full
- Ticket types: free, paid, VIP, invitation-only — multiple types per event
- Event status: draft, published, cancelled, completed
- Registration page: auto-generated public registration page for published events

### Advanced
- Multi-day events: configure a programme across multiple days with session slots
- Early-bird pricing: time-limited discounted ticket price
- Group registration: one registrant can register multiple attendees simultaneously
- Private events: password-protected registration requiring an access code
- Custom registration questions: collect additional attendee information beyond name and email

### AI-Powered
- Event description generator: AI drafts the event description from a brief input
- Optimal date suggestion: recommend event dates based on calendar conflicts and historical attendance patterns
- Capacity recommendation: suggest capacity limits based on venue constraints and typical registration-to-attendance ratios

---

## Data Model

```erDiagram
    events {
        ulid id PK
        ulid company_id FK
        string name
        text description
        string cover_image_url
        string event_type
        datetime starts_at
        datetime ends_at
        string timezone
        string venue_address
        string virtual_link
        integer capacity
        boolean waitlist_enabled
        string status
        boolean is_private
        string access_code
        timestamps created_at_updated_at
    }

    event_ticket_types {
        ulid id PK
        ulid event_id FK
        string name
        string access_level
        decimal price
        string currency
        integer quantity_available
        date sale_end_date
    }

    events ||--o{ event_ticket_types : "has"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `events` | Event records | `id`, `company_id`, `name`, `starts_at`, `capacity`, `event_type`, `status`, `is_private` |
| `event_ticket_types` | Ticket configurations | `id`, `event_id`, `name`, `price`, `quantity_available`, `sale_end_date` |

---

## Permissions

```
events.events.view
events.events.create
events.events.update
events.events.delete
events.events.publish
```

---

## Filament

- **Resource:** `App\Filament\Events\Resources\EventResource`
- **Pages:** `ListEvents`, `CreateEvent`, `EditEvent`, `ViewEvent`
- **Custom pages:** `EventCalendarPage`, `RegistrationPagePreview`
- **Widgets:** `UpcomingEventsWidget`, `CapacityWidget`
- **Nav group:** Events

---

## Displaces

| Feature | FlowFlex | Eventbrite | Cvent | Hopin |
|---|---|---|---|---|
| Event creation and management | Yes | Yes | Yes | Yes |
| Multi-ticket types | Yes | Yes | Yes | Yes |
| Private/access-code events | Yes | Yes | Yes | Yes |
| AI description generation | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Implementation Notes

**Registration page (public-facing):** The auto-generated registration page for published events is NOT a Filament page — it is a Vue 3 + Inertia public page served from `GET /events/{event-slug}/register`. This page renders the event details, ticket type selection, attendee form fields, and payment (if paid tickets exist). The page is public (no auth required for free events) or requires an access code for private events.

**Paid ticket payments:** The spec mentions "paid" ticket types but the payment mechanism is not specified. Integrate with Stripe Checkout or Stripe Elements (already in the stack via `stripe/stripe-php`): (1) Create a Stripe Checkout Session for the selected ticket type + quantity, (2) redirect to Stripe Checkout, (3) on success, handle `checkout.session.completed` webhook to create the `registrations` records. Add `event_ticket_types.stripe_price_id` to store the Stripe Price object ID — not currently in the data model.

**`EventCalendarPage`:** A custom Filament `Page` showing all events on a FullCalendar.js monthly/weekly calendar. Events are colour-coded by status (draft = grey, published = blue, live = green, cancelled = red). Clicking an event opens the `ViewEvent` page.

**`RegistrationPagePreview`:** A custom Filament `Page` that renders the public registration page inside an `<iframe>` for preview before publishing — similar to `StorefrontPreviewPage` in the ecommerce domain.

**Virtual link integration:** `events.virtual_link` stores the URL for virtual events. This can be a Zoom meeting URL, Teams link, or a Daily.co room URL. For integration with the `comms.video` module: if a comms meeting was created for this event, store the meeting ID in `events` and derive the virtual link from it. Add `ulid comms_meeting_id FK nullable` to the `events` table.

**AI features:** Event description generator and optimal date suggestion call `app/Services/AI/EventPlanningService.php` wrapping OpenAI GPT-4o. Capacity recommendation is a PHP calculation based on historical `registrations/actual-attendees` ratio per event type — no LLM needed.

**Missing from data model:** `event_ticket_types` is missing `ulid company_id FK` for `BelongsToCompany`. Also needs `string stripe_price_id nullable` for paid ticket Stripe integration. `events.timezone` should be a validated IANA timezone string (e.g. `Europe/Amsterdam`) — store as string, validate against PHP's `DateTimeZone` supported timezones list.

## Related

- [[registrations]] — attendee sign-ups against event ticket types
- [[speakers]] — speaker assignments to events
- [[sponsors]] — sponsor records linked to events
- [[check-in]] — on-the-day check-in for registered attendees
- [[post-event-analytics]] — analytics generated after event completion
