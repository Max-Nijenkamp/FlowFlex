---
type: module
domain: Business Travel
panel: travel
cssclasses: domain-travel
phase: 7
status: complete
migration_range: 914000–915999
last_updated: 2026-05-12
---

# Travel Policy Engine

Configure company travel rules. Policies enforce class restrictions, advance booking windows, per-diem rates, and city-specific hotel rate caps. Applied at search time in the booking portal.

---

## Policy Dimensions

### Flight Class Rules
| Route type | Allowed class | Example |
|---|---|---|
| Domestic / < 3h | Economy only | LHR–AMS |
| International 3–6h | Economy or Premium Economy | LHR–DXB |
| Long haul > 6h | Economy or Premium Economy; Business with manager approval | LHR–JFK |
| Long haul, Director+ | Business class allowed | LHR–SIN |

Configurable threshold hours. Class rules can also be role-based (Senior Director and above = business automatically allowed).

### Advance Booking Window
- Minimum days before departure: e.g., 14 days for international flights
- Emergency exception: booking inside window requires justification + manager approval
- Logic: earlier booking = cheaper fares, enforcing reduces cost

### Hotel Rate Caps (per city)
Defined per city/region:
```
Amsterdam: max €180/night
London: max €220/night
New York: max $280/night
Tier 2 cities: max €120/night
```

Rate caps auto-update annually (admin-editable). Policy shows both cap and suggested booking window.

### Per Diem Rates
Daily meal/incidental allowance by country:
```
Netherlands: €75/day
UK: £85/day
USA: $85/day (or use IRS standard rates per city)
```

Integrated with expense module: per-diem days auto-calculated from trip dates; actual receipts vs per-diem comparison.

### Approval Thresholds
- Trips under €X: auto-approved
- Trips over €X: manager approval required
- Specific destinations (high-risk countries): always require approval + security briefing
- International trips: HR notification (for duty of care)

---

## Policy Tiers

Companies can define multiple policies for different employee groups:
- **Standard** — all employees
- **Senior** — senior staff (relaxed class rules, higher hotel cap)
- **Executive** — C-suite (no class restrictions, highest caps)
- **Contractor** — most restrictive

Each employee assigned to a policy tier (from HR profile). Policy tier is checked at booking.

---

## Out-of-Policy Booking

When employee selects out-of-policy option:
1. Booking flagged with warning indicator
2. Employee must enter business justification (free text, min 50 chars)
3. Manager receives approval request
4. If approved: booking proceeds; exception logged
5. Monthly exception report: all out-of-policy bookings, by category, by department

---

## Data Model

### `travel_policies`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(200) | "Standard Employee Policy" |
| flight_rules | json | array of class rules |
| advance_booking_days | int | |
| hotel_rate_caps | json | {city: max_rate} |
| per_diem_rates | json | {country: daily_rate} |
| auto_approve_threshold | decimal(12,2) | |
| requires_receipts_above | decimal(8,2) | |

### `travel_policy_assignments`
| Column | Type | Notes |
|---|---|---|
| employee_id | ulid | FK |
| policy_id | ulid | FK |
| effective_from | date | |

---

## Migration

```
914000_create_travel_policies_table
914001_create_travel_policy_assignments_table
914002_create_travel_policy_exceptions_table
```

---

## Related

- [[MOC_Travel]]
- [[travel-booking-portal]]
- [[trip-approvals-workflow]]
- [[MOC_Finance]] — per diem rates sync with expense module
