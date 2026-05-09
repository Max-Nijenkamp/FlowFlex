---
type: module
domain: Operations & Field Service
panel: operations
cssclasses: domain-operations
phase: 5
status: planned
migration_range: 300000–399999
last_updated: 2026-05-09
---

# Fleet Management

Vehicle register, real-time GPS tracking, maintenance scheduling, driver compliance, fuel management, and fleet cost reporting. Replaces Webfleet and Fleetio.

---

## Features

### Vehicle Register
- Vehicle profiles: make, model, year, VIN, licence plate, fuel type
- Document storage: registration, insurance, MOT/APK, lease agreement
- Renewal alerts (insurance, MOT, road tax)
- Vehicle assignment to driver/department

### GPS Tracking
- Real-time vehicle location (via TomTom/Webfleet/Geotab integration)
- Trip recording (start/end, distance, duration, route)
- Geofencing alerts (vehicle leaves/enters zone)
- Live map dashboard
- Historical trip playback

### Driver Management
- Driver profiles linked to Employee records
- Licence type, expiry, penalty points tracking
- Licence renewal reminders
- Driving behaviour score (harsh braking, speeding — from telematics)
- Driver briefing acknowledgements

### Maintenance
- Scheduled maintenance by mileage and/or date
- Service history log
- Defect reporting (driver submits defect check via mobile)
- Breakdown job creation → links to Field Service
- Parts usage tracking

### Fuel & Cost
- Fuel card integration (or manual entry)
- Fuel cost per vehicle / per km
- Fleet total cost of ownership report
- CO₂ emissions reporting per vehicle and fleet-wide
- EV fleet readiness analysis

---

## Data Model

```erDiagram
    fleet_vehicles {
        ulid id PK
        ulid company_id FK
        string registration_plate
        string make
        string model
        integer year
        string vin
        string fuel_type
        string status
        ulid assigned_driver_id FK
    }

    fleet_trips {
        ulid id PK
        ulid vehicle_id FK
        ulid driver_id FK
        decimal distance_km
        timestamp started_at
        timestamp ended_at
        json route_polyline
    }

    fleet_maintenance_records {
        ulid id PK
        ulid vehicle_id FK
        string type
        date service_date
        integer odometer_km
        decimal cost
        string notes
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `VehicleDocumentExpiring` | 30 days before expiry | Notifications (fleet manager) |
| `DefectReported` | Driver submits defect | Notifications (fleet manager), Operations (maintenance work order) |
| `GeofenceViolation` | Vehicle exits zone | Notifications (manager) |

---

## Permissions

```
operations.fleet.view-any
operations.fleet.manage-vehicles
operations.fleet.view-tracking
operations.fleet.manage-drivers
```

---

## Competitors Displaced

Webfleet (TomTom) · Fleetio · Samsara · Fleet Complete · Masternaut

---

## Related

- [[MOC_Operations]]
- [[entity-employee]]
- [[field-service-management]]
