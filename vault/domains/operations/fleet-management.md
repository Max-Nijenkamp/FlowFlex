---
type: module
domain: Operations
panel: operations
module-key: operations.fleet
status: planned
color: "#4ADE80"
---

# Fleet Management

> Maintain vehicle records, schedule maintenance, track fuel costs, manage driver assignments, and report on fleet total cost of ownership.

**Panel:** `operations`
**Module key:** `operations.fleet`

## What It Does

Fleet Management is the vehicle lifecycle system for companies that operate their own transport. Each vehicle has a profile with make, model, registration, insurance, and MOT details. The module monitors service intervals (by mileage or date) and raises maintenance tasks before deadlines are missed. Drivers are linked to vehicles with licence validity tracked. Fuel entries and all costs roll up into a per-vehicle and fleet-wide total cost of ownership report.

## Features

### Core
- Vehicle register: make, model, year, VIN, registration plate, fuel type, assigned driver, department
- Document storage: registration certificate, insurance certificate, MOT/APK, lease agreement — with expiry dates
- Expiry alerts: configurable notification schedule before insurance, MOT, and road tax renewal deadlines
- Maintenance scheduling: service intervals by mileage threshold or calendar date; auto-create maintenance task when due
- Service history log: record each service with date, odometer, work performed, cost, and workshop
- Defect reporting: driver submits a defect check from mobile; defects raise a maintenance task for the fleet manager

### Advanced
- Driver management: driver profiles linked to HR employee records; licence class, expiry, and penalty points tracked
- Licence renewal reminders: alert driver and fleet manager before licence expiry
- Fuel log: manual entry or fuel card CSV import; fuel cost per vehicle, per kilometre, and fleet total
- Cost tracking: all costs (fuel, maintenance, insurance, lease, fines) recorded per vehicle for TCO reporting
- GPS integration: connect to TomTom Webfleet, Samsara, or Geotab for real-time location and trip recording
- CO₂ emissions reporting: per-vehicle and fleet-wide emissions from fuel consumption data

### AI-Powered
- Predictive maintenance: flag vehicles likely to need unscheduled maintenance based on mileage trajectory and service history patterns
- EV fleet readiness: analyse route data and fuel costs to estimate savings from switching specific vehicles to electric

## Data Model

```erDiagram
    ops_fleet_vehicles {
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
        integer current_odometer_km
        timestamps timestamps
        softDeletes deleted_at
    }

    ops_fleet_maintenance {
        ulid id PK
        ulid vehicle_id FK
        string maintenance_type
        string trigger_type
        integer due_odometer_km
        date due_date
        string status
        date completed_on
        decimal cost
        text notes
        timestamps timestamps
    }

    ops_fleet_fuel_logs {
        ulid id PK
        ulid vehicle_id FK
        ulid driver_id FK
        date filled_on
        decimal litres
        decimal cost_per_litre
        decimal total_cost
        integer odometer_km
        timestamps timestamps
    }

    ops_fleet_trips {
        ulid id PK
        ulid vehicle_id FK
        ulid driver_id FK
        decimal distance_km
        timestamp started_at
        timestamp ended_at
        string trip_purpose
        json route_polyline
    }

    ops_fleet_vehicles ||--o{ ops_fleet_maintenance : "has"
    ops_fleet_vehicles ||--o{ ops_fleet_fuel_logs : "logs"
    ops_fleet_vehicles ||--o{ ops_fleet_trips : "travels"
```

| Table | Purpose |
|---|---|
| `ops_fleet_vehicles` | Vehicle master records |
| `ops_fleet_maintenance` | Scheduled and completed maintenance records |
| `ops_fleet_fuel_logs` | Fuel entries for cost and consumption tracking |
| `ops_fleet_trips` | Trip records (manual or GPS-sourced) |

## Permissions

```
operations.fleet.view-any
operations.fleet.manage-vehicles
operations.fleet.manage-maintenance
operations.fleet.view-tracking
operations.fleet.manage-drivers
```

## Filament

**Resource class:** `FleetVehicleResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `FleetMapPage` (live GPS positions), `FleetCostReportPage` (TCO per vehicle)
**Widgets:** `VehicleAlertsWidget` (expiring documents and overdue maintenance), `FleetCostSummaryWidget`
**Nav group:** Logistics

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Webfleet (TomTom) | GPS tracking and fleet management |
| Fleetio | Vehicle records, maintenance, and fuel logging |
| Samsara Fleet | Real-time tracking and driver safety |
| Fleet Complete | Fleet maintenance and compliance |

## Related

- [[logistics]] — fleet vehicles used for own-account deliveries
- [[../hr/INDEX]] — driver linked to HR employee record
- [[supplier-management]] — maintenance workshops as suppliers
