---
domain: it
type: opportunities
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# IT & Security — Opportunity Radar

Web-researched (2024–2026) tooling gaps and repeatedly-requested capabilities that the incumbents —
**Jira Service Management, Freshservice, ServiceNow** (ITSM), **Snipe-IT** (asset), **BetterCloud / Zluri**
(SaaS licence), **Jamf / Intune / Kandji** (MDM) — either lack, gate behind expensive tiers, or split
across 3–4 disconnected tools. Each is a candidate differentiator for FlowFlex IT. Sourced + dated;
speculative sizing is marked `UNVERIFIED`. Constitution: [[../../decisions/decision-2026-06-20-full-mapping-conventions]].

> [!note] How to read this
> "Gap" = what's missing/painful in the market. "FlowFlex angle" = how our bounded, all-in-one,
> event-driven architecture (HR + IT + Finance in one DB) could exploit it. Angles are design bets, not
> commitments — treat every angle as `UNVERIFIED`.

---

## Candidates

### 1. HR-triggered zero-touch provisioning & deprovisioning
- **Gap**: the HR↔IT coordination gap is one of the most under-addressed problems in the modern workplace.
  41.6% of HR leaders estimate inconsistent offboarding costs up to **$500k/year**, and a 2026 security-ops
  study found **35% of security incidents** traced to improperly offboarded employees, with lack of
  automated deprovisioning as a core contributor. Standalone ITSM/asset tools don't own the HR trigger.
- **FlowFlex angle**: because HR lives in the same system, `EmployeeHired` / `EmployeeOffboarded` events
  fan out natively — [[access-provisioning/_module|access]] auto-creates/flags grants, [[asset-inventory/_module|assets]]
  flag returns, [[software-licences/_module|licences]] reclaim seats — with **zero integration middleware**.
  This is the domain's structural edge no single-purpose competitor has.
- Sources: stitchflow.com best-employee-offboarding-software (2026), cloudnuro.ai onboarding-offboarding-it-guide (2026), moveworks.com hr-offboarding-automation (2025). `UNVERIFIED` on cost-saving magnitude for SMEs.

### 2. Unified asset + ticket (one record, one pane) — no CMDB stitching
- **Gap**: Snipe-IT does **not integrate with helpdesk systems out of the box** (long-standing open request,
  GitHub #14145); teams bolt asset tracking onto a separate ticketing tool. Meanwhile CMDBs are "frequently
  poorly maintained" on stale data, and only **43% of orgs fully understand their IT assets** (Flexera ITAM
  2025, *down* from 47%). "Single pane of glass" is hard precisely because it usually spans vendors.
- **FlowFlex angle**: a helpdesk ticket ("my laptop won't boot") links directly to the `it_assets` row in the
  same DB — technicians get warranty, assignee, MDM compliance, and licence context inline, no sync job.
  See [[helpdesk/_module|helpdesk]] ↔ [[asset-inventory/_module|assets]].
- Sources: github.com/snipe/snipe-it issue #14145 (open), stitchflow.com single-pane-of-glass-for-it (2025), ivanti.com single-pane-of-glass (2025).

### 3. No network scanning / no lifecycle automation in cheap asset tools
- **Gap**: Snipe-IT has **no built-in network discovery** (manual/CSV entry only) and **no workflow
  automation** — e.g. no "laptop reaches 3 years → flag for replacement review" logic. It depends on manual
  data entry, raising error rates, and reporting is basic with no customizable dashboards.
- **FlowFlex angle**: even without network scanning at v1, state-machine + scheduled-command automation
  (warranty alerts, refresh-flag on age) is native, and [[it-reporting/_module|IT Reporting]] gives the
  dashboards Snipe-IT lacks. Later: MDM sync ([[mdm-integration/_module|mdm]]) becomes the discovery source.
- Sources: virima.com snipe-asset-management (2025), goworkwize.com snipe-it-review (2026), tech.co snipe-it-review (2025). `UNVERIFIED` on when network-scan lands.

### 4. Deploy in days, not a 6–12-month ServiceNow project
- **Gap**: ServiceNow's most-cited complaint is **implementation complexity** — 8–12 weeks for basic modules,
  6–12 months for complex; first-year TCO commonly **3–5× the licence fee**, plus a dedicated admin. It's
  built for enterprise, not the 50–500-employee SME.
- **FlowFlex angle**: IT ships as an activatable module on an already-running platform — the ITSM value
  (tickets, assets, access, reporting) with **no separate deployment project** and no ServiceNow admin.
- Sources: kanini.com itsm-comparison-2025 (2025), technologymatch.com itsm-tools-comparison (2025), comparethecloud.net servicenow-vs-freshservice-vs-jsm (2025).

### 5. Purpose-built ITSM UX for IT generalists (not developers)
- **Gap**: Jira Service Management "feels like a service-desk layer on Jira"; IT generalists who aren't
  development-adjacent find the queue/sprint/code-review mental model **harder to adopt than expected**. And
  since **Oct 2024 Atlassian moved change + problem management out of Standard** into Premium/Enterprise only.
- **FlowFlex angle**: [[helpdesk/_module|helpdesk]] is a plain internal support desk — employee self-service
  create-own, IT staff [[helpdesk/features/staff-queue|priority queue]], no Jira concepts. Filament UI aimed
  at IT generalists, not engineers.
- Sources: kanini.com itsm-comparison-2025 (2025), atlassian.com jsm-vs-freshservice (2025), corptec.com.au jsm-vs-servicenow (2025).

### 6. Kill SaaS licence waste (30–50% of seats idle)
- **Gap**: **30–50% of SaaS licences are under-/un-used** each month; analysts estimate **~25% of every SaaS
  dollar is wasted**, and orgs without centralized visibility overspend **≥25% through 2027**. For a 200-seat
  company with 40% turnover, manual provisioning leaves **15–25% of licences** unassigned/underutilized.
- **FlowFlex angle**: [[software-licences/_module|licences]] tracks seats-per-employee with a utilisation +
  waste computation (`brick/money`), and offboarding auto-flags seats for reclaim — closing the #1 waste
  driver (poor offboarding) automatically because HR fires the event.
- Sources: cafetosoftware.com why-30-of-saas-licenses-go-unused (2025), block64.com saas-license-waste (2025), ramp.com unused-software-subscriptions (2025). `UNVERIFIED` on reclaim % for our seat model.

### 7. Renewal-surprise prevention (spreadsheet-killer)
- **Gap**: most IT teams still track warranties + licence renewals in **spreadsheets that go stale** —
  "someone has to enter the date, remember to check it, and act before it expires"; at scale that process
  breaks and dates get missed. Dedicated tools now treat automated 30-day expiry alerts as table-stakes.
- **FlowFlex angle**: both [[asset-inventory/features/warranty-alerts|warranty alerts]] and
  [[software-licences/features/renewal-alerts|licence renewal alerts]] run as scheduled commands with
  once-per-cycle idempotency guards — no spreadsheet, no missed renewal, plus a renewals-next-60-days widget.
- Sources: blog.invgate.com best-hardware-asset-management-warranty-tracking (2025), cloudaware.com it-hardware-asset-management (2025), docs.syncromsp.com asset-warranty-tracking (2025).

### 8. Data-driven hardware refresh (signals, not blanket 4-year rule)
- **Gap**: guidance is shifting from "replace everything every 4 years" to **data-driven refresh** —
  warranty EOL, ticket-volume-per-device, and compliance are more reliable signals; but the required
  structured lifecycle data rarely exists in cheap tools, so refresh stays guesswork.
- **FlowFlex angle**: FlowFlex already holds the signals in one place — asset age/warranty ([[asset-inventory/_module|assets]]),
  tickets-per-device ([[helpdesk/_module|helpdesk]]), and MDM compliance ([[mdm-integration/_module|mdm]]) —
  so a refresh-priority view is a join, not a data-collection project. `UNVERIFIED` (post-v1 report).
- Sources: blog.invgate.com hardware-refresh (2025), techservicetoday.com technology-lifecycle-management (2026), growrk.com it-asset-lifecycle-management (2026).

### 9. Audit-ready access reviews (SOC 2 / ISO 27001 evidence)
- **Gap**: for SOC 2 / ISO 27001, **auditors care that access reviews actually happen and are recorded**,
  not that the tool is fancy — yet SMEs do "who-has-access-to-what" reviews manually and struggle to produce
  repeatable, tracked evidence. Access-review tooling is usually a separate governance product.
- **FlowFlex angle**: [[access-provisioning/features/access-review-matrix|Access Review]] is a native
  employees×systems matrix with export; because grants are event-sourced from HR lifecycle, the review is
  always current and the trail (grant/revoke, who/when) is built-in audit evidence.
- Sources: zluri.com iso-27001-user-access-review (2025), securends.com iso-27001-user-access-review-guide (2025), adeliarisk.com soc-2-compliance-checklist (2025). `UNVERIFIED` on auditor acceptance without a dedicated GRC tool.

### 10. Affordable, provider-agnostic MDM visibility for mixed/small fleets
- **Gap**: Jamf carries a **per-device minimum** (~$250/mo for 25 Macs) and Kandji/Iru reportedly wants a
  **100-device minimum** — both punish small fleets; Intune only pays off if you're already deep in Microsoft
  365. Small mixed fleets end up over-buying or under-managing.
- **FlowFlex angle**: [[mdm-integration/_module|MDM Integration]] is a **read-and-act layer over whichever
  provider you already run** (driver abstraction: Jamf/Intune/Kandji) — pull compliance + device state into
  the same asset/employee record, no second full MDM subscription for visibility. `UNVERIFIED` (v1 = one
  provider first, per module ADR).
- Sources: goworkwize.com jamf-vs-intune (2025), stabilise.io jamf-vs-intune-vs-iru (2026), technologymatch.com intune-vs-jamf-pro-vs-kandji (2026).

### 11. Employee self-service that feels consumer-grade
- **Gap**: modern service desks now expect an **app-store-style self-service portal** (drag-and-drop, request
  catalogue); many SME setups still route IT requests through email or a clunky form, and the automation gap
  (20–40% of apps lack SCIM/API, gated behind a "SCIM tax") blocks true self-service fulfilment.
- **FlowFlex angle**: [[helpdesk/features/self-service-requests|self-service requests]] lets any employee
  raise incidents/service-requests scoped to their own assets, and access-request → single-approval flows
  through [[access-provisioning/_module|access]] — all inside the workspace users already log into daily.
- Sources: solarwinds.com servicenow-alternatives (2026), stitchflow.com single-pane-of-glass-for-it (2025). `UNVERIFIED` on catalogue depth at v1.

---

## Related

- [[_index|IT & Security MOC]] · [[../../decisions/decision-2026-06-20-full-mapping-conventions]]
- Modules: [[asset-inventory/_module]] · [[helpdesk/_module]] · [[access-provisioning/_module]] · [[software-licences/_module]] · [[mdm-integration/_module]] · [[it-reporting/_module]]
