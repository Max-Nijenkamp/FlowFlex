---
type: module
domain: Operations
panel: operations
module-key: operations.logistics
status: planned
color: "#4ADE80"
---

# Logistics

> Track outbound and inbound shipments, manage carrier contracts, schedule deliveries, and give customers real-time tracking visibility.

**Panel:** `operations`
**Module key:** `operations.logistics`

## What It Does

Logistics covers the movement of goods from the warehouse to the customer (outbound) and from suppliers to the warehouse (inbound). Each shipment is linked to the originating order or purchase order, assigned a carrier, and tracked from label creation through to delivery confirmation. Carrier rate comparison lets the despatch team choose the most cost-effective option per shipment. Customers receive automated tracking updates without a support call.

## Features

### Core
- Shipment creation: create from a fulfilled order or manually; assign carrier, service level, and tracking number
- Carrier management: configure carrier accounts (DHL, UPS, FedEx, PostNL, DPD, and others via EasyPost/Sendcloud integration)
- Shipping label print: generate and print carrier label from within FlowFlex; supports ZPL (thermal) and PDF
- Tracking: polling carrier APIs for status updates; statuses: label created → picked up → in transit → out for delivery → delivered → exception
- Delivery confirmation: automatic status update to linked order on delivery; notify customer
- Inbound shipment tracking: track supplier deliveries (ASN from supplier) against open purchase orders

### Advanced
- Multi-parcel shipments: a single order shipped in multiple boxes; track each parcel with its own tracking number
- Rate shopping: compare live rates from connected carriers for a given weight and destination before label creation
- Delivery scheduling: book a time-window delivery slot for large items; calendar view of scheduled deliveries
- Exception management: carrier exceptions (failed delivery, address not found) surfaced with suggested action
- Return label generation: create pre-paid return label for a specific order; link to returns workflow
- Customs documentation: commercial invoice and CN22/CN23 generation for international shipments

### AI-Powered
- Carrier recommendation: suggest optimal carrier per shipment based on cost, speed, historical reliability to that postcode
- Delivery time prediction: estimate actual delivery date based on carrier performance data

## Data Model

```erDiagram
    ops_shipments {
        ulid id PK
        ulid company_id FK
        ulid order_id FK
        string direction
        string carrier_code
        string service_level
        string tracking_number
        string status
        decimal weight_kg
        json dimensions_cm
        decimal freight_cost
        timestamp label_created_at
        timestamp delivered_at
        timestamps timestamps
    }

    ops_shipment_parcels {
        ulid id PK
        ulid shipment_id FK
        string tracking_number
        decimal weight_kg
        json dimensions_cm
        json items
    }

    ops_tracking_events {
        ulid id PK
        ulid shipment_id FK
        string status
        string location
        string description
        timestamp event_at
    }

    ops_carriers {
        ulid id PK
        ulid company_id FK
        string name
        string carrier_code
        json api_credentials
        boolean is_active
        timestamps timestamps
    }

    ops_shipments ||--o{ ops_shipment_parcels : "contains"
    ops_shipments ||--o{ ops_tracking_events : "has"
    ops_carriers }o--o{ ops_shipments : "handles"
```

| Table | Purpose |
|---|---|
| `ops_shipments` | Shipment header with carrier, tracking, and cost |
| `ops_shipment_parcels` | Individual parcel tracking for multi-box shipments |
| `ops_tracking_events` | Carrier status events timeline |
| `ops_carriers` | Carrier account configuration |

## Permissions

```
operations.logistics.view-any
operations.logistics.create-shipments
operations.logistics.manage-carriers
operations.logistics.print-labels
operations.logistics.manage-returns
```

## Filament

**Resource class:** `ShipmentResource`, `CarrierResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `DeliverySchedulePage` (calendar of booked delivery windows)
**Widgets:** `ShipmentStatusWidget` (exceptions and in-transit count), `DeliveryPerformanceWidget`
**Nav group:** Logistics

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Sendcloud | Multi-carrier label printing and tracking |
| EasyPost | Carrier API integration and rate shopping |
| ShipStation | Order fulfilment and shipping management |
| Shippo | Label generation and tracking |

## Related

- [[warehousing]] — pick-pack completion triggers shipment creation
- [[inventory]] — stock levels updated on confirmed despatch
- [[purchase-orders]] — inbound shipments tracked against POs
- [[../ecommerce/orders]] — ecommerce order tracking updated from shipment status
