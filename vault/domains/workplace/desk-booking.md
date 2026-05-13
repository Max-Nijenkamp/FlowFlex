---
type: module
domain: Workplace & Facility
panel: workplace
module-key: workplace.bookings
status: planned
color: "#4ADE80"
---

# Desk Booking

> Desk and meeting room booking with real-time availability, a calendar view, and cancellation management.

**Panel:** `workplace`
**Module key:** `workplace.bookings`

---

## What It Does

Desk Booking enables employees to find and reserve desks and meeting rooms through a simple calendar or floor plan interface. Employees select a date, see available spaces in real time, and book with a single action. Meeting room bookings include an attendee list and optional agenda. The system enforces booking rules configured in Office Spaces (advance notice windows, maximum duration) and sends confirmation and reminder notifications. Cancellations automatically release the space back to availability.

---

## Features

### Core
- Date-based availability view: see all available desks and rooms for a selected date
- Floor plan booking: click a desk on the interactive floor plan to book it
- Calendar list view: browse upcoming bookings in a personal calendar
- Meeting room booking: include attendees, agenda, and resource requirements (screen, video conferencing)
- Confirmation notification: booking confirmation with calendar file attachment (ICS)
- Cancellation: self-service cancellation with configurable advance-notice requirement
- Personal booking history: view and manage all upcoming and past bookings

### Advanced
- Recurring bookings: book the same desk or room on a recurring schedule (daily, weekly)
- Booking on behalf of: managers or admins book desks for team members
- Team booking: book a cluster of desks for a whole team on the same day
- Check-in confirmation: employees check in digitally on arrival; no-shows auto-release the desk
- Favourite spaces: bookmark preferred desks for quick re-booking

### AI-Powered
- Smart desk suggestion: recommend the best available desk based on the employee's past preferences and team proximity
- Meeting room optimisation: suggest the smallest available room that fits the attendee count
- Predictive availability: show expected busy days based on historical booking patterns

---

## Data Model

```erDiagram
    space_bookings {
        ulid id PK
        ulid space_id FK
        ulid company_id FK
        ulid booked_by FK
        ulid booked_for FK
        date booking_date
        time start_time
        time end_time
        string status
        string cancellation_reason
        boolean checked_in
        timestamp checked_in_at
        json attendees
        text agenda
        timestamps created_at_updated_at
    }

    space_bookings }o--|| office_spaces : "reserves"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `space_bookings` | Booking records | `id`, `space_id`, `booked_by`, `booked_for`, `booking_date`, `start_time`, `end_time`, `status`, `checked_in` |

---

## Permissions

```
workplace.bookings.book-own
workplace.bookings.book-for-others
workplace.bookings.view-all
workplace.bookings.cancel-any
workplace.bookings.manage-rules
```

---

## Filament

- **Resource:** `App\Filament\Workplace\Resources\SpaceBookingResource`
- **Pages:** `ListSpaceBookings`, `CreateSpaceBooking`
- **Custom pages:** `BookingCalendarPage`, `FloorPlanBookingPage`, `MyBookingsPage`
- **Widgets:** `TodayBookingsWidget`, `AvailabilityHeatmapWidget`
- **Nav group:** Bookings

---

## Displaces

| Feature | FlowFlex | Robin | Condeco | Envoy Desks |
|---|---|---|---|---|
| Floor plan booking | Yes | Yes | Yes | Yes |
| Team booking | Yes | Yes | No | No |
| Check-in auto-release | Yes | Yes | No | Yes |
| AI desk suggestion | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Implementation Notes

**Filament:** `FloorPlanBookingPage` is a custom `Page` — the interactive floor plan cannot be built with standard Filament form or table components. The floor plan is a scalable SVG image (uploaded by the admin in the `office-spaces` module) overlaid with clickable desk hotspots. Each hotspot's position (x%, y% of the SVG viewport) is stored in the desk record. When a user clicks a hotspot, a Livewire side panel opens to complete the booking. Colour-coding: green = available, red = booked, amber = partially booked (for multi-slot desks).

**`BookingCalendarPage`:** A personal calendar view of the user's upcoming bookings. Uses **FullCalendar.js** (MIT) rendered in a Blade partial with Livewire data hydration. Shows desk bookings and meeting room bookings as calendar events in different colours.

**Real-time availability:** When one user books a desk, the availability view of other users on the same floor plan page must update. Broadcast `DeskBooked` event on `workplace.availability.{company_id}.{date}` public channel (or private for company isolation). The `FloorPlanBookingPage` Livewire component listens and updates the hotspot colour in real time. Without Reverb, double-booking is possible in the brief window between availability check and booking save — use a database-level unique constraint on `(space_id, booking_date, start_time)` to prevent it regardless.

**ICS file attachment:** Booking confirmation emails include a `.ics` calendar attachment generated from a simple PHP iCalendar string builder (no package needed for basic VEVENT format). The `BookingConfirmedNotification` Mailable attaches the generated `.ics` content.

**Check-in auto-release:** A scheduled job `AutoReleaseDeskBookingsJob` runs every 30 minutes. It queries `space_bookings` where `booking_date = today`, `start_time` has passed by more than 30 minutes (configurable grace period), and `checked_in = false`. For each, it sets `status = no_show` and makes the space available again (no delete — the record is kept for occupancy analytics).

**AI features:** Smart desk suggestion is a PHP aggregate — query the employee's last 10 bookings, find the most frequently booked desk (or desk cluster), and suggest it if available. Meeting room optimisation is a SQL query: `WHERE capacity >= attendee_count AND is_available ORDER BY capacity ASC LIMIT 1`. No LLM needed for either.

## Related

- [[office-spaces]] — space registry that bookings query
- [[visitor-management]] — visitor arrival can trigger desk booking for host
- [[occupancy-analytics]] — booking data feeds occupancy reports
