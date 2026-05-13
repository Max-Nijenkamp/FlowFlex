---
type: module
domain: HR & People
panel: hr
module-key: hr.org
status: planned
color: "#4ADE80"
---

# Org Chart

> Visual org chart with manager reporting lines, department groupings, and headcount — a read-only custom Filament page drawn from the employee hierarchy in Employee Profiles.

**Panel:** `hr`
**Module key:** `hr.org`

## What It Does

The Org Chart module renders the company's reporting hierarchy as an interactive tree diagram. It reads the `manager_id` self-referential relationship from the Employee Profiles module — no separate data model is needed. The chart is presented as a zoomable, pannable tree where each node is an employee card showing name, job title, and profile photo. Clicking a node opens the employee's profile. The chart can be filtered by department to focus on one part of the organisation. It is read-only — changes to the hierarchy are made in Employee Profiles.

## Features

### Core
- Interactive tree diagram: zoomable and pannable — rendered using a JavaScript tree library (D3.js or similar)
- Employee node card: profile photo thumbnail, name, job title, direct report count
- Click to open: clicking a node navigates to the employee's Filament profile view
- Department filter: show only employees within a selected department and their manager chain
- Headcount overlay: node badge showing how many direct reports each manager has

### Advanced
- Compact mode: collapse subtrees for managers with >10 direct reports — expand on click
- Vacancy nodes: if an employee record is marked as a vacancy (unfilled role), it appears as a placeholder node so the intended structure is visible
- Print/export: export the current view as a PNG or PDF — used for board presentations
- Guest share link: generate a time-limited read-only URL so external parties (board members, new hires) can view the org chart without a FlowFlex account
- Historical view: toggle to see the org chart as it was on a past date — reconstructed from `employee_compensation` history and department change log

### AI-Powered
- Span of control analysis: AI flags managers with fewer than 2 or more than 12 direct reports — surfaced as an organisational health insight
- Restructure preview: drag-and-drop prototype mode shows what the org chart would look like after a proposed restructure (does not save changes — planning only)

## Data Model

```erDiagram
    employees {
        ulid id PK
        ulid company_id FK
        ulid manager_id FK
        string first_name
        string last_name
        string job_title
        ulid department_id FK
        string status
        timestamps created_at/updated_at
    }
```

| Source | Notes |
|---|---|
| `employees.manager_id` | Self-referential — drives the hierarchy tree |
| `employees.department_id` | Used for department filter |
| No separate `org_chart` table | All data read from Employee Profiles |

## Permissions

- `hr.org.view`
- `hr.org.view-salaries`
- `hr.org.export`
- `hr.org.share-link`
- `hr.org.view-historical`

## Filament

- **Resource:** None
- **Pages:** None
- **Custom pages:** `OrgChartPage` — full-page interactive tree with filter and export controls
- **Widgets:** None
- **Nav group:** Employees (hr panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| ChartHop | Org chart and people analytics |
| Lucidchart | Manual org chart drawing |
| OrgWeaver | Org chart software |
| Pingboard | Org chart and company directory |

## Implementation Notes

**Filament:** `OrgChartPage` is a custom `Page` class. The tree diagram is rendered by a JavaScript library inside a `<div id="org-chart-container">` in the Blade view — it cannot be built with Filament table or form components. Recommended library: **OrgChart.js** (MIT) or **D3.js** (MIT) with a tree layout. The employee hierarchy data is loaded via a Livewire `getChartData()` method returning a JSON tree (recursive structure from `employees.manager_id` self-join), which is passed to the JavaScript library via `@js($this->chartData)` in the Blade template.

**Data loading:** The recursive employee hierarchy must be fetched efficiently. Use a PostgreSQL recursive CTE (Common Table Expression) — this is a PostgreSQL-specific feature, acceptable per the tech stack constraints. The recursive query starts from all employees with `manager_id IS NULL` (the root nodes) and expands downward. For large orgs (1,000+ employees), the CTE approach is far more efficient than N+1 Eloquent traversal.

**Historical view:** The "as at past date" historical org chart reconstructs the hierarchy from audit log events or from a separate `employee_history` table. If `spatie/laravel-activitylog` is logging employee profile changes, the `manager_id` and `department_id` values at any past date can be reconstructed from the activity log. However, reconstructing a full tree from activity logs at arbitrary past dates is complex — consider adding a `employee_snapshots` table (daily or weekly snapshots of manager_id per employee) as a simpler alternative for the historical view feature.

**Export (PNG/PDF):** Use `spatie/browsershot` (Puppeteer) to capture the rendered chart page as a PNG. This requires Node.js + Puppeteer in the Docker image. The export button triggers a Livewire action that dispatches `ExportOrgChartJob` — the job renders the page in headless Chrome and uploads the image to S3.

**Guest share link:** Generate a signed URL (`URL::temporarySignedRoute('org-chart.guest', $expiry, ['token' => $token])`) where `$token` is stored in a `org_chart_share_links {ulid id, ulid company_id, string token, timestamp expires_at, boolean is_active}` table. The guest route renders the org chart page without authentication middleware, using the `company_id` from the share link record.

**AI features:** Span of control analysis is a SQL aggregate (count employees grouped by `manager_id`) — no LLM needed. Restructure preview drag-and-drop is a client-side-only mode where the JavaScript tree library enables drag-to-reparent nodes — changes are stored in Alpine.js state only and discarded on page leave.

## Related

- [[employee-profiles]]
- [[workforce-planning]]
- [[succession-planning]]
- [[hr-analytics]]
