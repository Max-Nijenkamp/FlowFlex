---
type: module
domain: Business Travel
panel: travel
module-key: travel.profiles
status: planned
color: "#4ADE80"
---

# Traveller Profiles

> Employee traveller profiles — passport and visa information, seat and meal preferences, and loyalty programme numbers.

**Panel:** `travel`
**Module key:** `travel.profiles`

---

## What It Does

Traveller Profiles stores the personal travel information for each employee so it does not have to be re-entered on every trip. Passport details, expiry dates, visa holdings, preferred seat positions, meal preferences, and frequent flyer and hotel loyalty numbers are all maintained here. When a travel coordinator makes a booking, they pull this information from the profile. Passport expiry alerts ensure employees are warned well in advance of trips to renewing documentation before it expires.

---

## Features

### Core
- Passport details: document number, nationality, expiry date (encrypted at rest)
- Visa records: visa type, destination country, expiry date, multiple-entry flag
- Seat preferences: aisle, window, or no preference; bulkhead/exit row preferences
- Meal preferences: dietary requirements and in-flight meal selection preference
- Loyalty numbers: airline frequent flyer numbers and hotel loyalty programme membership
- Passport expiry alerts: automated notifications 180/90/30 days before passport expiry

### Advanced
- Trusted traveller numbers: TSA PreCheck, Global Entry, NEXUS for expedited security
- Emergency contact: next-of-kin name, relationship, and phone number for duty-of-care compliance
- Travel history: summary of all trips taken linked to the traveller's profile
- Profile completeness indicator: encourage employees to fill in all relevant fields
- Admin-managed fields: certain fields (passport details) restricted to HR admin to maintain data accuracy

### AI-Powered
- Visa requirement check: given a destination and passport nationality, flag whether a visa is required
- Document expiry risk: identify employees with trips planned who have passports expiring within 6 months
- Preference conflict detection: flag when a traveller's meal preference conflicts with in-flight options on a specific airline

---

## Data Model

```erDiagram
    traveller_profiles {
        ulid id PK
        ulid company_id FK
        ulid employee_id FK
        string passport_number_encrypted
        string passport_nationality
        date passport_expires_at
        string seat_preference
        string meal_preference
        json loyalty_numbers
        json emergency_contact
        json visa_records
        timestamps created_at_updated_at
    }

    traveller_profiles }o--|| employees : "linked to"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `traveller_profiles` | Travel personal data | `id`, `company_id`, `employee_id`, `passport_nationality`, `passport_expires_at`, `seat_preference`, `loyalty_numbers` |

---

## Permissions

```
travel.profiles.view-own
travel.profiles.edit-own
travel.profiles.view-all
travel.profiles.edit-sensitive
travel.profiles.export
```

---

## Filament

- **Resource:** `App\Filament\Travel\Resources\TravellerProfileResource`
- **Pages:** `ListTravellerProfiles`, `ViewTravellerProfile`, `EditTravellerProfile`
- **Custom pages:** `PassportExpiryAlertPage`
- **Widgets:** `ExpiringPassportsWidget`, `ProfileCompletenessWidget`
- **Nav group:** Requests

---

## Displaces

| Feature | FlowFlex | TravelPerk | Concur | Egencia |
|---|---|---|---|---|
| Passport and visa storage | Yes | Yes | Yes | Yes |
| Loyalty number management | Yes | Yes | Yes | Yes |
| Expiry alerts | Yes | Yes | Yes | Partial |
| Visa requirement check | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[travel-requests]] — preferences used when submitting requests
- [[bookings]] — loyalty numbers applied during booking creation
- [[hr/INDEX]] — traveller profile linked to employee record
