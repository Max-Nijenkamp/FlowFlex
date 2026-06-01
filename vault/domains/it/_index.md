---
type: domain-index
domain: IT & Security
panel: it
color: "#4ADE80"
---

# IT & Security

Asset inventory, IT helpdesk, access provisioning, software licences, MDM integration, and reporting. **Panel:** `/it` (Cyan) — Phase 3.

---

## Navigation Groups

- **Assets** — Asset Inventory, Assignments
- **Helpdesk** — IT Tickets, Queue
- **Access** — Systems, Access Grants, Access Review
- **Licences** — Software Licences
- **Devices** — MDM Devices
- **Reporting** — IT Dashboard

---

## Modules

| Module | Key | Status | Priority |
|---|---|---|---|
| [[domains/it/asset-inventory\|Asset Inventory]] | `it.assets` | planned | **P3 core** |
| [[domains/it/helpdesk\|IT Helpdesk]] | `it.helpdesk` | planned | **P3 core** |
| [[domains/it/access-provisioning\|Access Provisioning]] | `it.access` | planned | P3 |
| [[domains/it/software-licences\|Software Licences]] | `it.licences` | planned | P3 |
| [[domains/it/mdm-integration\|MDM Integration]] | `it.mdm` | planned | P3 |
| [[domains/it/it-reporting\|IT Reporting]] | `it.reporting` | planned | P3 |

---

## Key Patterns

- `spatie/laravel-model-states` — asset status, ticket status
- Encrypted MDM/provider API credentials (see [[architecture/patterns/encryption]])
- Cross-domain: consumes `EmployeeHired` (provisioning) + `EmployeeOffboarded` (de-provision, asset return, seat reclaim)
- Integrates with [[domains/hr/onboarding]] and [[domains/finance/fixed-assets]]
