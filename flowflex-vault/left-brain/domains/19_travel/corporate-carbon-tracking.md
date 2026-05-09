---
type: module
domain: Business Travel
panel: travel
cssclasses: domain-travel
phase: 7
status: planned
migration_range: 924000–929999
last_updated: 2026-05-09
---

# Corporate Carbon Tracking (Travel)

CO₂ per trip, per employee, per department. Fleet-wide travel emissions, carbon budget management, and voluntary offset purchasing. Feeds ESG domain's Scope 3 emissions.

---

## Emission Calculation

### Flights
IATA methodology + DEFRA 2024 emission factors:
- Haversine great-circle distance between airports
- Radiative forcing index (RFI) multiplier: 1.9× for high-altitude aviation impact
- Per cabin: economy / premium economy / business / first (different seat density factors)
- Output: kg CO₂e per passenger

Example:
```
Amsterdam (AMS) → New York (JFK)
Distance: 5,852 km
Economy seat emission factor: 0.116 kg CO₂e / passenger-km
RFI multiplier: 1.9×
Result: 5,852 × 0.116 × 1.9 = 1,290 kg CO₂e
```

### Hotels
DEFRA accommodation emission factors:
- Default: UK hotel average ≈ 6.5 kg CO₂e / room-night
- Green-certified hotels: use lower factor (hotels provide verified data or EcoLabel factor)

### Car Rental / Ground Transport
- Car category emission factors (economy car ≈ 0.17 kg CO₂e / km)
- Estimated km from trip distance if actual not reported
- Electric vehicle: near-zero (location-adjusted grid emission factor)

### Rail
- Eurostar / high-speed rail: ~0.011 kg CO₂e / km (vs aviation 10–20× higher)
- Displayed prominently as "alternative: take the train and save X kg CO₂"

---

## Carbon Dashboard

### Per Traveller
- Total travel emissions (kg CO₂e) this year
- Breakdown by transport mode
- Top 3 highest-emission trips
- Year vs target comparison (if personal travel carbon budget set)

### Per Department
- Department total emissions
- Per-capita travel emissions (department size normalised)
- Top contributing trips

### Company Overview
- Total travel emissions vs last year
- By mode: flight / hotel / ground
- By geography: domestic / EU / long-haul international
- Trend chart: monthly emissions

### Carbon Budget
- Set annual company travel carbon budget (kg CO₂e)
- Actual vs budget: red when > 90%, amber when > 75%
- At booking time: show employee their personal carbon "spend" this year

---

## Rail vs Flight Comparison

At search time, if rail is a viable alternative (e.g., AMS-PAR, LHR-EDI):
- Show side-by-side: duration, price, CO₂ emissions for both options
- "Book train instead: save 180 kg CO₂e (93% reduction)"
- Track modal shift rate over time (are employees choosing train more?)

---

## Carbon Offsets

- Integration with offset providers (e.g., Atmosfair, Gold Standard, South Pole)
- Employee-initiated: "offset this trip" at booking or post-trip
- Company-level batch offset purchase: offset all Q1 travel
- Offset tracking: vintage, project type (forestry/renewable/cookstoves), certificate number

Note: offsets are supplementary. Reduction > offsets in ESG reporting.

---

## ESG Integration

Travel carbon data feeds into [[carbon-footprint-tracking]]:
- Scope 3, Category 6: business travel
- Air travel (employee-owned or company-booked)
- Hotel nights
- Ground transport

Auto-sync: monthly batch push from travel_bookings with carbon_kg → ESG carbon module.

---

## Data Model

Carbon data stored on `travel_bookings.carbon_kg` (calculated at booking time).

### `travel_carbon_offsets`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| booking_id | ulid | nullable FK (per-trip offset) |
| provider | varchar(100) | |
| project_name | varchar(300) | |
| vintage_year | int | |
| carbon_kg | decimal(10,2) | amount offset |
| cost | decimal(10,2) | |
| currency | char(3) | |
| certificate_ref | varchar(200) | |

---

## Migration

```
924000_create_travel_carbon_budgets_table
924001_create_travel_carbon_offsets_table
```

---

## Related

- [[MOC_Travel]]
- [[travel-booking-portal]] — carbon shown per option at search
- [[carbon-footprint-tracking]] — receives Scope 3 travel data
- [[MOC_ESG]] — ESG sustainability reporting
