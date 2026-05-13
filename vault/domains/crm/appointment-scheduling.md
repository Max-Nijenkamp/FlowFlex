---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.scheduling
status: planned
color: "#4ADE80"
---

# Appointment Scheduling

> Public booking pages, round-robin team scheduling, calendar sync, and paid bookings — Calendly-style functionality built natively into the CRM so every booking is automatically linked to a contact and deal.

**Panel:** `/crm`
**Module key:** `crm.scheduling`

## What It Does

Appointment Scheduling gives every sales rep, team, and department a public booking page where prospects and customers can self-schedule meetings without email back-and-forth. The module syncs with Google Calendar and Microsoft Outlook to show real availability, applies configurable rules (buffer time, max meetings per day, minimum notice), and automatically creates or updates a CRM contact record for every person who books. Payment collection via Stripe is supported for paid consultations. Booking confirmation and reminder emails are sent automatically, and no-show follow-up sequences can be triggered.

## Features

### Core
- Booking page builder: create named booking pages with a URL slug (`/book/{slug}`), configurable meeting duration, description, location (video link or in-person address), and a custom intro message
- Calendar sync: connect Google Calendar (OAuth2) or Microsoft Outlook/Exchange (Microsoft Graph OAuth2) — the module reads free/busy data from the connected calendar to show real-time availability
- Buffer time: configurable buffer before and after meetings (e.g. 15 min before, 10 min after) to prevent back-to-back bookings
- Minimum notice period: minimum hours/days in advance a booking can be made (e.g. no same-day bookings)
- Maximum meetings per day: cap on how many bookings can be accepted on a single calendar day per booking page
- Booking confirmation: automated confirmation email to the booker immediately on booking with meeting details and a calendar invite (`.ics` attachment)
- CRM contact auto-creation: when a booking is made, the booker's name and email are matched against existing contacts in CRM; if no match, a new contact is created automatically and the booking is linked

### Advanced
- Round-robin team scheduling: create a team booking page where bookings are assigned to team members in rotation based on availability — ensures even distribution
- Custom booking questions: add a form to the pre-booking flow (e.g. "What's the main topic you'd like to discuss?", "Company name", "How many employees?") — answers are stored on the booking and optionally mapped to CRM contact fields
- Automated reminders: email reminders sent at 24 hours and 1 hour before the meeting — configurable on/off per booking page
- No-show follow-up: if the meeting status is marked as no-show, trigger a configurable follow-up email sequence (uses Sales Sequences module)
- Rescheduling and cancellation: every confirmation email includes a unique link for the booker to reschedule or cancel — changes update the calendar event automatically
- Payment on booking: connect a Stripe price ID to a booking page — bookers pay before the booking is confirmed; failed payment = no booking created
- Embed widget: generate a `<script>` snippet that embeds the booking calendar directly on any website page (uses postMessage for sizing)

### AI-Powered
- Meeting prep brief: 30 minutes before a scheduled meeting, the assigned rep receives an AI-generated brief pulling from CRM contact history, recent deal activity, and last email thread — surfaces talking points and open action items
- Booking intent classification: when custom questions are submitted, AI classifies the booking intent (e.g. "new business", "renewal", "support", "partnership") and tags the CRM contact accordingly
- Optimal time suggestion: for outbound booking requests sent by a rep, AI analyses the prospect's timezone and typical email response times to suggest the three most likely-to-be-accepted time slots

## Data Model

```erDiagram
    crm_scheduling_pages {
        ulid id PK
        ulid company_id FK
        enum owner_type
        ulid owner_id
        string slug
        string name
        integer duration_minutes
        integer buffer_before_minutes
        integer buffer_after_minutes
        integer max_per_day
        integer min_notice_hours
        json booking_questions
        string stripe_price_id "nullable"
        boolean is_active
        string location
        text description
        timestamps created_at/updated_at
    }

    crm_bookings {
        ulid id PK
        ulid page_id FK
        ulid company_id FK
        ulid booker_contact_id FK "nullable"
        string booker_name
        string booker_email
        timestamp starts_at
        timestamp ends_at
        enum status
        string meeting_link "nullable"
        json custom_answers "nullable"
        text notes
        string stripe_payment_intent_id "nullable"
        enum intent_classification "nullable"
        timestamps created_at/updated_at
    }

    crm_calendar_connections {
        ulid id PK
        ulid user_id FK
        ulid company_id FK
        enum provider
        text access_token_encrypted
        text refresh_token_encrypted
        string calendar_id
        timestamp expires_at
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `crm_scheduling_pages.owner_type` | enum: `user` / `team` — for round-robin pages, `owner_id` references an HR team/department record |
| `crm_scheduling_pages.booking_questions` | JSON array: `[{id, label, type, required, crm_field_mapping}]` |
| `crm_bookings.status` | enum: `pending` / `confirmed` / `cancelled` / `rescheduled` / `no_show` / `completed` |
| `crm_calendar_connections.provider` | enum: `google` / `microsoft` |
| `crm_calendar_connections.access_token_encrypted` | AES-256 encrypted at rest using `ENCRYPT_KEY` env variable |
| `crm_bookings.booker_contact_id` | Populated after CRM contact match/create; can be null briefly at creation |

## Permissions

```
crm.scheduling.view-bookings
crm.scheduling.manage-pages
crm.scheduling.connect-calendar
crm.scheduling.view-all-bookings
crm.scheduling.manage-payments
```

## Filament

- **Resource:** `SchedulingPageResource` — manage booking pages (list, create, edit); includes a preview link button that opens the public booking page in a new tab
- **Custom page:** `BookingCalendarPage` — a custom Filament page showing all upcoming bookings across all pages in a calendar view (using a FullCalendar.js integration); click a booking to see details, mark as completed or no-show, add notes
- **Relation manager:** `BookingsRelationManager` on `ContactResource` — shows all bookings linked to a CRM contact with dates, status, and meeting notes
- **Widget:** `UpcomingBookingsWidget` on CRM dashboard — list of today's and tomorrow's meetings for the logged-in user
- **Nav group:** Activities (crm panel)
- **Public page:** The public booking interface (`/book/{slug}`) is a Vue 3 + Inertia page served by the public frontend — not a Filament page. It renders the availability calendar, custom question form, and payment step. The Filament backend serves as the API for availability slots and booking creation.

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Calendly | Public booking pages, round-robin scheduling, calendar sync |
| HubSpot Meetings | CRM-integrated meeting scheduling |
| YouCanBookMe | Team booking pages, custom questions |
| SavvyCal | Scheduling with calendar overlay |
| Acuity Scheduling | Paid appointment booking with forms |

## Related

- [[contacts]]
- [[deals]]
- [[activities]]
- [[sales-sequences]]
- [[email-integration]]
- [[../communications/video-conferencing]]

## Implementation Notes

### Calendar API Integration
Google Calendar requires OAuth2 with `https://www.googleapis.com/auth/calendar.readonly` scope for availability reading and `https://www.googleapis.com/auth/calendar.events` for event creation. Tokens must be refreshed using the refresh token before expiry — implement a `RefreshCalendarTokenJob` that runs every 50 minutes for active connections.

Microsoft Graph requires OAuth2 with `Calendars.Read` and `Calendars.ReadWrite` scopes. The Graph API availability check uses the `/me/calendarView` endpoint with `$top=100` and the booking page's working hours as the query window.

Cache availability slots in Redis with a 5-minute TTL per user per day to avoid hammering the calendar APIs on every page load.

### Meeting Link Generation
V1 decision needed before build: use Zoom API integration (requires Zoom app approval) or generate a Whereby/Google Meet link. Recommended V1 approach: generate a Jitsi Meet link using a company-specific room slug (`meet.flowflex.com/{company_slug}/{booking_ulid}`) if FlowFlex hosts a Jitsi instance, or use the Video Conferencing module's link generation. Document this decision in an ADR.

### Stripe Payment Flow
When `stripe_price_id` is set on a booking page, the booking form includes a Stripe Payment Element (v3 payment intents). Booking creation is held in `pending` status until the webhook `payment_intent.succeeded` confirms payment. Use idempotency keys on Stripe API calls keyed to the booking ULID.

### Embed Widget
The embed snippet is a `<script>` tag that renders an `<iframe>` pointing to `/book/{slug}?embed=1`. The iframe uses `postMessage` to communicate height changes to the parent page for seamless resizing. Content-Security-Policy headers must allow framing from any origin for embed pages (`X-Frame-Options: ALLOWALL` only on `/book/{slug}?embed=1` routes).
