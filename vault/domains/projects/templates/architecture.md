---
domain: projects
module: templates
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Templates — Architecture

## Services & Actions

- `TemplateService::instantiate(CreateFromTemplateData): ProjectData` — single transaction: create project + sections + tasks (due = start + `day_offset`) + milestones; **delegates creation to the owning modules' actions** (never a direct cross-table write).
- `TemplateService::fromProject(SaveAsTemplateData): TemplateData` — reverse: computes offsets from the project start.

## System templates

Seeded rows with `company_id` null + `is_system = true`; readable by all companies via a **read-only global-scope exception**; never editable cross-tenant (edit → copy into the company). See [[../../../architecture/multi-tenancy]].

## Events

None cross-domain. Instantiation is synchronous via owning-module actions.

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `ProjectTemplateResource` | Settings | #1 CRUD | sections/tasks repeaters; system templates read-only |
| `CreateProjectFromTemplatePage` | Settings | #7 wizard custom page | template → name/start date → confirm |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('projects.templates.view-any')
        && BillingService::hasModule('projects.templates');
}
```

## Jobs & Scheduling

None.

## Search & Realtime

None.
