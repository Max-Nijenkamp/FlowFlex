---
type: moc
domain: Field Service Management
panel: fsm
cssclasses: domain-fsm
phase: 5
color: "#EA580C"
last_updated: 2026-05-09
---

# Field Service Management — Map of Content

End-to-end field service operations: job dispatch, mobile technician app, GPS tracking, parts management, customer sign-off, and field invoicing. For companies with field teams visiting customer sites.

**Panel:** `fsm`  
**Phase:** 5  
**Migration Range:** `1050000–1099999`  
**Colour:** Orange-600 `#EA580C` / Light: `#FFF7ED`  
**Icon:** `heroicon-o-wrench-screwdriver`

---

## Why This Domain Exists

Operations domain handles internal equipment maintenance (CMMS). Field Service is **customer-facing**: a technician dispatched to a customer location. Different use case, different data model, different UX.

Current market costs:
- ServiceMax: €80–120/user/month
- Salesforce Field Service: €60/user/month
- Jobber: €47/user/month
- Commusoft: €45/user/month
- FieldPulse: €30/user/month

---

## Target Industries

HVAC · Plumbing · Electrical · Telecoms (broadband install) · Medical equipment service · Lift/elevator maintenance · Security systems · Cleaning services · IT on-site support

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| [[job-dispatch-scheduling\|Job Dispatch & Scheduling]] | 5 | planned | Drag-drop dispatch board, geographic clustering, skill matching |
| [[mobile-field-app\|Mobile Field App]] | 5 | planned | iOS/Android PWA: job list, navigation, forms, photo capture, offline |
| [[technician-management\|Technician & Team Management]] | 5 | planned | Skills, certifications, availability, territory, performance |
| [[customer-sign-off\|Customer Sign-Off & POD]] | 5 | planned | Digital signature capture on device, photo proof-of-delivery |
| [[field-invoicing\|Field Invoicing & Payment]] | 5 | planned | On-site invoice generation, card/BACS payment, Stripe integration |
| [[parts-inventory-fsm\|Parts & Inventory (Field)]] | 5 | planned | Technician stock, van inventory, parts ordering from warehouse |
| SLA & Contract Tracking | 6 | planned | Maintenance contracts, SLA timers, breach alerting, renewal |
| Customer Asset Register | 6 | planned | Equipment installed at customer sites, service history |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `JobCreated` | Dispatch | Notifications (assigned technician), Calendar |
| `JobStarted` | Mobile App | CRM (job in progress), GPS tracking |
| `JobCompleted` | Mobile App | Finance (create invoice), Inventory (deduct parts), CRM (log activity) |
| `CustomerSignatureReceived` | Sign-Off | DMS (store POD document), Finance (trigger invoice) |
| `PartOutOfStock` | Parts Inventory | Operations (raise PO), Notifications (technician) |
| `SLABreached` | SLA Tracking | Notifications (manager), CRM (log) |

---

## Dispatch Board Concept

```
[Unscheduled Jobs] ──drag──► [Jan van Dam | Today]
                              08:00 ██████ Job #1042 (HVAC Service)
                              10:30 ████   Job #1038 (Boiler Install)
                              14:00 ████████ Job #1055 (Emergency Call)

[Pieter Bakker | Today]
                              09:00 ██████ Job #1039
                              12:00 ██     Job #1047
```

---

## Permissions Prefix

`fsm.jobs.*` · `fsm.technicians.*` · `fsm.dispatch.*`  
`fsm.parts.*` · `fsm.contracts.*` · `fsm.assets.*`

---

## Competitors Displaced

ServiceMax · Salesforce Field Service · Jobber · Commusoft · FieldPulse · ServiceTitan · Totalmobile · BigChange

---

## Related

- [[MOC_Domains]]
- [[MOC_Operations]] — internal maintenance (CMMS) vs customer-facing FSM
- [[MOC_CRM]] — customers, contacts, assets
- [[MOC_Finance]] — field invoices → revenue
- [[MOC_Ecommerce]] — spare parts sold to customers
