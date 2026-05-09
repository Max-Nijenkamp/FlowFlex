---
type: module
domain: Operations & Supply Chain
panel: operations
phase: 3
status: planned
cssclasses: domain-operations
migration_range: 457500–457999
last_updated: 2026-05-09
---

# Returns Management (RMA)

Manage customer returns end-to-end. Return Merchandise Authorisation, inspection, restocking, and refund/replacement. Reduce fraud, streamline warehouse operations.

---

## RMA Initiation

Customer requests return:
1. Customer submits return request: order number, item(s), reason code
2. System checks eligibility: within return window, item returnable
3. RMA number generated (unique reference)
4. Return shipping label issued (pre-paid or at-cost, configurable per reason)
5. Customer drops off or schedules collection

---

## Reason Codes

Configurable return reasons:
- Defective / damaged in transit
- Wrong item sent
- Item not as described
- Changed mind (no-fault return)
- Arrived too late
- Duplicate order

Reason drives: refund eligibility, restocking approach, supplier chargeback (if defective).

---

## Receiving & Inspection

On item received at warehouse:
- RMA number scanned → return record opened
- Condition assessment: resaleable / refurbish / scrap / return to supplier
- Photo documented
- Disposition decided per item

---

## Dispositions

| Condition | Action |
|---|---|
| New/resaleable | Return to stock |
| Minor damage | Refurbish → sell as "Grade B" |
| Defective (supplier fault) | Return to supplier for credit |
| Beyond repair | Scrap / donate / recycle |

---

## Refund & Replacement

After inspection approved:
- Full refund, partial refund, or replacement shipment
- Refund triggered in payment system
- Replacement: new outbound order created
- Customer notified at each step

---

## Analytics

- Return rate by product, SKU, category
- Return reason breakdown
- Defective rate by supplier (triggers supplier quality review)
- Refund value trend
- Time to process (RMA created → refund issued)

---

## Data Model

### `ops_rmas`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| order_id | ulid | FK |
| customer_id | ulid | FK |
| rma_number | varchar(50) | unique |
| status | enum | requested/approved/received/inspected/resolved |
| reason_code | varchar(100) | |
| resolution | enum | refund/replacement/credit/rejected |

### `ops_rma_items`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| rma_id | ulid | FK |
| product_id | ulid | FK |
| quantity | int | |
| condition | enum | resaleable/refurbish/defective/scrap |
| disposition | enum | restock/return_supplier/scrap/refurbish |

---

## Migration

```
457500_create_ops_rmas_table
457501_create_ops_rma_items_table
```

---

## Related

- [[MOC_Operations]]
- [[warehouse-management]]
- [[lot-batch-serial-tracking]]
- [[MOC_Ecommerce]]
