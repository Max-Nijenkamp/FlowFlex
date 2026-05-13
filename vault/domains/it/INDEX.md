---
type: domain-index
domain: IT & Security
panel: it
panel-path: /it
panel-color: Cyan
color: "#4ADE80"
---

# IT & Security

One panel for IT asset management, service desk ticketing, incident and change management, access governance, vulnerability tracking, licence management, and IT compliance — replacing ServiceNow (SMB) or Freshservice.

**Panel:** `it` — `/it`
**Filament color:** Cyan

---

## Modules

| Module | Key | Description |
|---|---|---|
| [[asset-management]] | it.assets | Hardware/software asset inventory, assignment, lifecycle, and depreciation |
| [[incident-management]] | it.incidents | IT incident reports, severity classification, SLA tracking, and resolution notes |
| [[change-management]] | it.changes | Change requests, approval workflow, change calendar, and impact assessment |
| [[service-desk]] | it.service-desk | Internal IT helpdesk with ticket creation, SLA, priority, and resolution tracking |
| [[access-management]] | it.access | User access requests, provisioning approvals, access reviews, and deprovisioning |
| [[vulnerability-management]] | it.vulnerabilities | Vulnerability scan results, risk scoring, remediation tracking, and CVE tracking |
| [[software-licenses]] | it.licenses | Software licence inventory, seat counts, renewal dates, and cost tracking |
| [[audit-compliance]] | it.audit | IT compliance checklists, control evidence collection, and asset audit trail |
| [[capacity-planning]] | it.capacity | Infrastructure capacity metrics, growth projections, and upgrade planning |
| [[it-analytics]] | it.analytics | IT metrics dashboard: ticket volume, SLA performance, asset health (read-only) |

---

## Nav Groups

- **Assets** — asset-management, software-licenses, capacity-planning
- **Incidents** — service-desk, incident-management
- **Access** — access-management, change-management
- **Compliance** — audit-compliance, vulnerability-management
- **Settings** — SLA policies, categories, escalation rules

---

## Displaces

| Tool | Replaced By |
|---|---|
| ServiceNow (SMB tier) | service-desk, incident-management, change-management |
| Freshservice | service-desk, asset-management, change-management |
| Jira Service Management | service-desk, incident-management |
| Snipe-IT | asset-management, software-licenses |
| Qualys / Tenable (SMB) | vulnerability-management |

---

## Related

- [[../hr/INDEX]] — employee records linked to asset assignments and access requests
- [[../finance/INDEX]] — asset depreciation and licence costs in finance
- [[../analytics/INDEX]] — IT KPIs surface in BI dashboards
