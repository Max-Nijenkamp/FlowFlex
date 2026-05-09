---
type: module
domain: IT & Security
panel: it
phase: 3
status: planned
cssclasses: domain-it
migration_range: 603500–603999
last_updated: 2026-05-09
---

# IT Service Catalogue

Structured menu of IT services that employees can request. Standardises requests, sets expectations, and triggers the right workflow automatically.

---

## What's in the Catalogue

Each service is a requestable item with defined SLA and approval flow:

| Category | Service Examples |
|---|---|
| Hardware | New laptop, monitor, keyboard, docking station |
| Software | New licence (standard apps), custom software request |
| Access | VPN access, system access, shared folder, admin rights |
| Communication | New phone number, email alias, distribution list |
| Onboarding | New starter kit (laptop + access + apps bundle) |
| Offboarding | Leaver kit (revoke all access, retrieve hardware) |
| Network | New WiFi access point, VLAN configuration |

---

## Service Item Configuration

Each catalogue item:
- Title, description, category
- Request form (custom fields: which system? what access level?)
- Approval chain (auto-approve / manager / IT manager / security team)
- Fulfillment SLA (e.g., 2 business days)
- Cost (if chargeable to department)
- Assigned team/queue on submission

---

## Onboarding / Offboarding Bundles

"New starter" bundle:
- Triggers multiple service items in one request
- Hardware → order + provision
- Software licences → auto-provision where API available
- System access → access request per system
- All tracked in single parent ticket with sub-tasks

Offboarding: manager submits leaver form → system auto-creates access revocation tickets across all systems.

---

## Approval Workflow

Per service item:
1. Auto-approve (low-risk standard items)
2. Manager approval (most access requests)
3. IT security review (admin access, VPN, privileged accounts)
4. CFO approval (if cost above threshold)

---

## Data Model

### `it_catalogue_items`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(300) | |
| category | varchar(100) | |
| description | text | |
| request_form | json | custom field definitions |
| approval_flow | json | |
| fulfillment_days | int | |
| is_active | boolean | |

### `it_service_requests`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| catalogue_item_id | ulid | FK |
| ticket_id | ulid | FK → it_tickets |
| form_data | json | |
| status | enum | pending/approved/in_progress/fulfilled |

---

## Migration

```
603500_create_it_catalogue_items_table
603501_create_it_service_requests_table
```

---

## Related

- [[MOC_IT]]
- [[itsm-helpdesk]]
- [[change-management-itil]]
- [[it-procurement-requests]]
