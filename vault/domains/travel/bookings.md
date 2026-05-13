---
type: module
domain: Business Travel
panel: travel
module-key: travel.bookings
status: planned
color: "#4ADE80"
---

# Bookings

> Travel booking records — flights, hotels, car hire, itinerary storage, and receipt upload.

**Panel:** `travel`
**Module key:** `travel.bookings`

---

## What It Does

Bookings is the record-of-travel module. Once a travel request is approved, the employee or travel coordinator creates booking records for each leg of the trip — flights, hotel nights, car hire — uploading confirmation documents and recording the actual cost. The module assembles these into a complete itinerary view for the traveller. Actual costs feed into expense reports for reimbursement, and the booking data provides real spend visibility against approved request estimates.

---

## Features

### Core
- Booking record creation: type (flight, hotel, rail, car hire), provider, confirmation number, dates, actual cost
- Document upload: attach booking confirmation PDFs and e-tickets
- Itinerary view: consolidated day-by-day travel itinerary assembled from all bookings on a request
- Link to travel request: every booking is associated with an approved travel request
- Currency support: record costs in the booking currency with automatic conversion to company base currency

### Advanced
- Booking modification tracking: record changes to bookings (date changes, cancellations) with cost impact
- Supplier records: maintain a list of preferred hotels, airlines, and car hire suppliers
- Hotel booking notes: record room type, check-in time, and loyalty programme number used
- Cancellation and refund tracking: log cancelled bookings and expected refund amount
- Group travel: one travel request can have multiple travellers, each with their own booking records

### AI-Powered
- Cost variance alert: flag when actual booking cost significantly exceeds the estimated cost on the request
- Supplier recommendation: suggest preferred suppliers based on destination, past bookings, and policy compliance
- Itinerary completeness check: flag when a multi-leg trip has gaps (e.g. hotel not booked for overnight stops)

---

## Data Model

```erDiagram
    travel_bookings {
        ulid id PK
        ulid request_id FK
        ulid company_id FK
        ulid employee_id FK
        string booking_type
        string provider
        string confirmation_number
        date start_date
        date end_date
        decimal actual_cost
        string currency
        string document_url
        string status
        timestamps created_at_updated_at
    }

    travel_bookings }o--|| travel_requests : "belongs to"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `travel_bookings` | Booking records | `id`, `request_id`, `company_id`, `employee_id`, `booking_type`, `provider`, `actual_cost`, `status` |

---

## Permissions

```
travel.bookings.create
travel.bookings.view-own
travel.bookings.view-all
travel.bookings.update
travel.bookings.delete
```

---

## Filament

- **Resource:** `App\Filament\Travel\Resources\TravelBookingResource`
- **Pages:** `ListTravelBookings`, `CreateTravelBooking`, `EditTravelBooking`, `ViewTravelBooking`
- **Custom pages:** `ItineraryViewPage`, `BookingCalendarPage`
- **Widgets:** `UpcomingTripsWidget`, `TravelSpendSummaryWidget`
- **Nav group:** Bookings

---

## Displaces

| Feature | FlowFlex | TravelPerk | Concur | Egencia |
|---|---|---|---|---|
| Booking record management | Yes | Yes | Yes | Yes |
| Itinerary assembly | Yes | Yes | Yes | Yes |
| Document upload | Yes | Yes | Yes | Yes |
| Supplier preference records | Yes | Yes | Yes | Yes |
| Included in platform | Yes | No | No | No |

---

## Related

- [[travel-requests]] — bookings reference approved requests
- [[traveller-profiles]] — traveller preferences and loyalty numbers used in booking
- [[expense-reports]] — actual booking costs become expense line items
- [[travel-policies]] — booking costs checked against policy limits
