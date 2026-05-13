---
type: module
domain: Business Travel
panel: travel
cssclasses: domain-travel
phase: 7
status: complete
migration_range: 910000–913999
last_updated: 2026-05-12
---

# Travel Booking Portal

Self-serve flight, hotel, car, and rail booking within company travel policy. Employees book travel directly without a travel agent — policy is enforced at search time, not after.

---

## Booking Modes

### Flights
- Search: origin/destination airports, dates, cabin class, number of passengers
- Results: real fare inventory via GDS (Amadeus or Sabre) or aggregator API (Duffel, Kiwi.com)
- Policy enforcement at result level:
  - Out-of-policy options greyed out with tooltip ("Economy required for routes < 3h")
  - Out-of-policy options can still be selected with business justification
- Seat selection (if GDS supports it)
- Add baggage
- Loyalty program number stored on employee profile, auto-applied

### Hotels
- Search: city/address, check-in/check-out, room type
- Preferred supplier rates shown first (negotiated corporate rates from [[preferred-supplier-management]])
- Max nightly rate per city enforced (from [[travel-policy-engine]])
- Rate includes breakfast flag
- Green flag: hotel with verified sustainability rating (LEED/EcoLabel)

### Car Rental
- Search: pick-up location, dates, car category
- Corporate rates via preferred supplier
- Policy: economy/compact by default; large car requires justification

### Rail
- UK: National Rail (ATOC) or Trainline API
- EU: Deutsche Bahn / SNCF / NS (Netherlands) API integrations
- Eurostar, Thalys for cross-border

---

## Booking Workflow

```
Search → Results → Select → Add to trip → Submit for approval (if required)
                                         → Auto-approve (if within policy) → Confirm booking
```

On confirmation:
- GDS booking confirmed → PNR/booking reference stored
- Email confirmation to traveller
- Calendar invite created (from/to, dates, hotel address)
- Pre-trip risk check (destination risk level from duty of care module)

---

## Trip Management
- "My Trips" dashboard: upcoming, past, cancelled trips
- Each trip shows: flight details, hotel confirmation, car booking, total cost
- Change/cancel: initiate via portal → triggers approval if policy requires
- Documents: e-ticket PDFs, hotel vouchers stored on trip record

---

## Data Model

### `travel_trips`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| traveller_id | ulid | FK `employees` |
| destination_city | varchar | |
| destination_country | char(2) | ISO |
| departure_date | date | |
| return_date | date | nullable (one-way) |
| purpose | varchar(300) | business justification |
| status | enum | draft/pending_approval/approved/booked/cancelled |
| total_cost | decimal(12,2) | |
| currency | char(3) | |
| carbon_kg | decimal(8,2) | computed from segments |

### `travel_bookings`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| trip_id | ulid | FK |
| type | enum | flight/hotel/car/rail |
| supplier | varchar(100) | airline/hotel name |
| confirmation_ref | varchar(50) | PNR or booking ref |
| starts_at | datetime | |
| ends_at | datetime | |
| cost | decimal(12,2) | |
| currency | char(3) | |
| carbon_kg | decimal(8,2) | |

---

## Migration

```
910000_create_travel_trips_table
910001_create_travel_bookings_table
910002_create_travel_booking_documents_table
910003_create_travel_loyalty_profiles_table
```

---

## Related

- [[MOC_Travel]]
- [[travel-policy-engine]]
- [[trip-approvals-workflow]]
- [[duty-of-care-traveller-safety]]
- [[corporate-carbon-tracking]]
- [[MOC_Finance]] — trip costs → travel expense management
