---
type: module
domain: E-commerce
panel: ecommerce
module-key: ecommerce.returns
status: planned
color: "#4ADE80"
---

# Returns

> Customer return requests, RMA number generation, refund processing, and restocking workflow from a single returns management interface.

**Panel:** `ecommerce`
**Module key:** `ecommerce.returns`

## What It Does

Returns manages the complete reverse logistics process. A customer initiates a return from the self-service portal or the merchant initiates it from the order record. The system generates an RMA (Return Merchandise Authorisation) number, sends the customer a pre-paid return label if configured, and tracks the return through receipt, inspection, and resolution. Resolution options are refund to original payment method, exchange for another item, or store credit. Restocked items are returned to operations inventory.

## Features

### Core
- Return request: customer initiates from self-service portal or merchant creates from order detail; records items, quantities, and return reason
- RMA number: auto-generated unique number for tracking the return shipment
- Return reasons: configurable list (changed mind, faulty, wrong item received, not as described, size issue)
- Pre-paid return label: generate and email a prepaid return shipping label to the customer
- Return receipt: warehouse records the return received against the RMA; condition assessed (sellable, damaged, destroyed)
- Resolution workflow: refund (full or partial), exchange (replacement dispatched), or store credit issued

### Advanced
- Resolution SLA: configurable target time from return receipt to resolution; alert when SLA approaching
- Restocking: sellable returned items pushed back into inventory in [[../operations/inventory]] with a restocking movement record
- Partial returns: return some line items from an order; order status reflects partial return
- Photo evidence: customer can upload photos during return request; merchant reviews before approving
- Return analytics: return rate per product, return reason breakdown, resolution mix — surface candidates for product improvement
- Automated approval rules: auto-approve returns under a configurable value threshold without manual review

### AI-Powered
- Return reason analysis: identify products with unusually high return rates or specific reasons clustered to a batch or supplier
- Resolution recommendation: suggest the most cost-effective resolution based on product cost, return reason, and item condition

## Data Model

```erDiagram
    ec_return_requests {
        ulid id PK
        ulid company_id FK
        ulid order_id FK
        ulid customer_id FK
        string rma_number
        string status
        string reason
        json items
        string resolution_type
        decimal refund_amount
        string return_label_url
        timestamp received_at
        timestamp resolved_at
        timestamps timestamps
    }

    ec_return_items {
        ulid id PK
        ulid return_id FK
        ulid order_line_id FK
        integer quantity
        string condition
        boolean restocked
        timestamps timestamps
    }

    ec_return_requests ||--o{ ec_return_items : "includes"
```

| Table | Purpose |
|---|---|
| `ec_return_requests` | Return request header with RMA and resolution |
| `ec_return_items` | Line items included in the return |

## Permissions

```
ecommerce.returns.view-any
ecommerce.returns.create
ecommerce.returns.approve
ecommerce.returns.resolve
ecommerce.returns.delete
```

## Filament

**Resource class:** `ReturnRequestResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `ReturnResolutionPage` (inspect received return and select resolution)
**Widgets:** `PendingReturnsWidget`, `ReturnRateWidget`
**Nav group:** Orders

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Loop Returns | Self-service returns and exchange portal |
| Happy Returns | Return label generation and processing |
| Returnly | Returns and instant exchange |
| AfterShip Returns | Return tracking and management |

## Related

- [[orders]] — return requests initiated from order records
- [[payments]] — refunds processed against the original payment
- [[inventory-sync]] — restocked items returned to operations inventory
- [[../operations/warehousing]] — physical returns received in the warehouse
