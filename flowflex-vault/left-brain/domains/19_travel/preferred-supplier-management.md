---
type: module
domain: Business Travel
panel: travel
cssclasses: domain-travel
phase: 7
status: complete
migration_range: 921000–923999
last_updated: 2026-05-12
---

# Preferred Supplier Management

Manage corporate rate agreements with airlines, hotel chains, and car rental companies. Preferred suppliers surface first in booking portal with negotiated rates and contract terms.

---

## Supplier Types

### Airlines
- Corporate fare agreements (negotiated discount off published fares)
- Stored: airline IATA code, fare basis code, discount %, booking class eligibility, validity dates
- Loyalty programme corporate account number (pooled company miles/points)
- Blackout dates (dates excluded from corporate fare)

### Hotels
- Negotiated rate per property or per hotel chain (all properties)
- Stored: hotel name, city, chain, negotiated rate (per room type), rate validity, breakfast included flag
- Rate loading method: direct load via GDS chain codes, or manual rate table
- Preferred hotel badge in booking portal results

### Car Rental
- Corporate discount code per vendor (Hertz, Avis, Enterprise)
- Stored: vendor, discount code, eligible vehicle categories, included insurances, additional driver policy

### Other Suppliers
- Airport transfer companies (pre-negotiated flat fares for common routes)
- Rail season ticket discounts
- Airport lounge access (if company provides)

---

## Contract Management

Each supplier agreement has:
- Contract documents (stored in document vault)
- Start/end dates
- Volume commitments (e.g., min 50 nights/year)
- SLA terms (service level if issues arise)
- Review schedule (annual renegotiation reminder)
- Actual vs committed volume tracking (for renegotiation leverage)

---

## Savings Tracking

For each booking at a preferred supplier rate vs best public rate available:
- Estimated saving = public rate − negotiated rate
- Cumulative savings report: by supplier, by month, by department
- Used at renegotiation: "We spent €180k at your properties last year and saved €42k — we want better rates."

---

## Supplier Portal (Optional)

Give preferred suppliers a read-only portal view showing:
- Volume they've received from the company (nights/bookings)
- Upcoming bookings (helps hotel chains pre-block inventory)
- Current contract terms and renewal date

---

## Data Model

### `travel_preferred_suppliers`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| type | enum | airline/hotel/car/transfer/other |
| name | varchar(200) | |
| supplier_code | varchar(50) | IATA code, chain code, or vendor ID |
| negotiated_terms | json | type-specific rate/discount data |
| loyalty_account | varchar(100) | nullable |
| contract_start | date | |
| contract_end | date | |
| volume_commitment | json | nullable |
| active | bool | |

### `travel_preferred_supplier_savings`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| booking_id | ulid | FK `travel_bookings` |
| supplier_id | ulid | FK |
| public_rate | decimal(10,2) | best available public rate at time of booking |
| negotiated_rate | decimal(10,2) | |
| saving | decimal(10,2) | computed |
| currency | char(3) | |

---

## Migration

```
921000_create_travel_preferred_suppliers_table
921001_create_travel_preferred_supplier_savings_table
921002_create_travel_supplier_contracts_table
```

---

## Related

- [[MOC_Travel]]
- [[travel-booking-portal]] — preferred rates shown first in search
- [[travel-policy-engine]] — policy references preferred suppliers
- [[MOC_Finance]] — supplier invoice reconciliation
