---
type: builder-log
module: projects-phase2
domain: Projects & Work
panel: projects
started: 2026-05-10
status: in-progress
color: "#F97316"
left_brain_source: "[[MOC_Projects]]"
last_updated: 2026-05-11
---

# Builder Log: Projects & Work — Phase 2

Left Brain source: [[MOC_Projects]]

---

## Sessions

### Session 2026-05-10

**Goal:** Scaffold the Projects Filament panel and build the 6 core modules: Task Management, Kanban Boards, Gantt Timeline (milestones), Sprint & Agile, Project Templates (is_template flag), Time Tracking.

**Built:**

Migrations:
- `200001_create_projects_table.php` — projects with status/priority enums, owner FK, template self-FK (two-step pattern)
- `200002_create_project_members_table.php` — project_members pivot with role enum
- `200003_create_tasks_table.php` — tasks with self-referential parent_id (two-step pattern), all fields
- `200004_create_task_dependencies_table.php` — dependency types enum
- `200005_create_task_comments_table.php`
- `200006_create_kanban_boards_table.php`
- `200007_create_kanban_columns_table.php` — WIP limits, maps_to_status
- `200008_create_sprints_table.php`
- `200009_create_sprint_tasks_table.php` — composite PK pivot (no ulid id — Eloquent BelongsToMany incompatible)
- `200010_create_time_entries_table.php` — billable, approved_by
- `200011_create_project_milestones_table.php`

Models (`app/Models/Projects/`):
- `Project.php` — HasUlid, BelongsToCompany, SoftDeletes; all relations
- `ProjectMember.php`
- `Task.php` — subtasks(), sprint() BelongsToMany
- `TaskDependency.php`
- `TaskComment.php`
- `KanbanBoard.php`
- `KanbanColumn.php`
- `Sprint.php`
- `TimeEntry.php`
- `ProjectMilestone.php`

Contracts (`app/Contracts/Projects/`):
- `ProjectServiceInterface.php` — create, update, archive, addMember
- `TaskServiceInterface.php` — create, update, complete, reorder
- `SprintServiceInterface.php` — createSprint, startSprint, completeSprint, addTask
- `TimeEntryServiceInterface.php` — log, approve, calculateHours

Services (`app/Services/Projects/`):
- `ProjectService.php` — fires ProjectCreated event, auto-adds owner as member
- `TaskService.php` — fires TaskCreated/TaskCompleted events
- `SprintService.php` — velocity calculation on completeSprint
- `TimeEntryService.php` — fires TimeEntryApproved event

DTOs (`app/Data/Projects/`):
- `CreateProjectData.php`
- `CreateTaskData.php`
- `LogTimeData.php`

Events (`app/Events/Projects/`):
- `ProjectCreated.php`
- `TaskCreated.php`
- `TaskCompleted.php`
- `ProjectMilestoneReached.php`
- `TimeEntryApproved.php`

Filament Panel:
- `ProjectsPanelProvider.php` — id: projects, path: /projects, Indigo theme
- `resources/css/filament/projects/theme.css` — Vite-built
- `app/Filament/Projects/Pages/Dashboard.php`
- `vite.config.js` updated with projects theme entry

Filament Resources (`app/Filament/Projects/Resources/`):
- `ProjectResource.php` — nav group: Projects
- `TaskResource.php` — nav group: My Work; TagsInput for labels
- `KanbanBoardResource.php` — nav group: Planning
- `SprintResource.php` — nav group: Planning; Start Sprint / Complete Sprint table actions
- `TimeEntryResource.php` — nav group: Time; Approve action
- `ProjectMilestoneResource.php` — nav group: Planning

All resources have List/Create/Edit page classes.

ServiceProvider: `app/Providers/Projects/ProjectsServiceProvider.php`
Registered in `bootstrap/providers.php`

Permissions: 22 new permissions added to `PermissionSeeder.php` (total: 47 → 69)

Factories (`database/factories/Projects/`):
- `ProjectFactory.php`, `ProjectMemberFactory.php`, `TaskFactory.php`, `SprintFactory.php`, `TimeEntryFactory.php`, `KanbanBoardFactory.php`, `ProjectMilestoneFactory.php`

Tests:
- `tests/Feature/Projects/ProjectServiceTest.php` — 5 tests
- `tests/Feature/Projects/TaskServiceTest.php` — 5 tests
- `tests/Feature/Projects/SprintServiceTest.php` — 5 tests
- `tests/Feature/Projects/TimeEntryServiceTest.php` — 4 tests
- `tests/Feature/Filament/ProjectsPanelTest.php` — 4 tests

**Final test count:** 234 passed, 0 failed (up from 210)

---

**Decisions made:**
- `sprint_tasks` pivot table uses composite PK `['sprint_id', 'task_id']` instead of a ULID `id` — Eloquent `BelongsToMany` inserts only FK columns; a NOT NULL `id` column breaks inserts. Pivot tables don't need their own PK.
- Self-referential FKs (`projects.template_id` and `tasks.parent_id`) moved to separate `Schema::table` block after `Schema::create` — same pattern as GAP-017 / ADR for HR domain.
- Permission count updated from 47 → 69 in all test assertions (`PermissionSeederTest`, `LocalSeederTest`).
- Vite build required after adding new panel theme CSS entry — same pattern as ADR `decision-2026-05-10-vite-rebuild-required`.

**Problems hit:**
1. `projects.template_id` FK failed with "no unique constraint" on PostgreSQL during `Schema::create` — fixed with two-step Schema::create + Schema::table pattern (GAP-017 pattern, already documented).
2. `tasks.parent_id` same issue — same fix applied.
3. `sprint_tasks` NOT NULL violation on `id` column when using `BelongsToMany::syncWithoutDetaching()` — Eloquent never sets the `id` for pivot inserts. Fixed by dropping the `id` column and using composite PK.
4. `ProjectsPanelTest` returned 500 for login page until Vite theme was built.

---

## Gaps Discovered

- [ ] [[gap_projects-pivot-ulid-incompatible]] — sprint_tasks pivot table with ULID id column is incompatible with Eloquent BelongsToMany — documented as architectural decision

---

## Lessons

- Pivot tables for `belongsToMany` relations should never have a ULID `id` primary key when using standard Eloquent pivot operations. Use composite PK.
- Always build Vite theme immediately after creating a new panel to avoid 500 errors in panel tests.
- Left-brain migration ranges are advisory only; actual migration numbers used were 200001–200011 (simpler sequential numbering within the range).

---

## Post-Build Checklist

- [x] All migrations run cleanly (`php artisan migrate`)
- [x] All tests pass (234 passed, 0 failed)
- [x] Filament panel registers at /projects
- [x] Login page loads (200 OK)
- [x] Dashboard loads for authenticated user (200 OK)
- [x] Permissions registered (69 total, up from 47)
- [x] Left Brain specs updated (6 modules → status: in-progress, right_brain_log linked)
- [x] STATUS_Dashboard updated
- [ ] Filament resources render in browser (needs manual QA)
- [ ] Events fire correctly (needs manual QA or integration test)

---

---

### Session 2026-05-11 — Phase 0–2 production-viability audit + dashboard + sprint kanban

**Goal:** Full audit of Phase 0–2 for production viability. Fix module key mismatches, build dashboards for all panels, add sprint kanban view.

**Fixed — module key mismatches (3 resources were invisible):**
- `ProjectResource::canAccess()`: `'projects.projects'` → `'projects.tasks'`
- `KanbanBoardResource::canAccess()`: `'projects.boards'` → `'projects.kanban'`
- `ProjectMilestoneResource::canAccess()`: `'projects.projects'` → `'projects.milestones'`

**Fixed — missing catalog entries:**
- `ModuleCatalogSeeder`: added `projects.time` (Time Tracking) and `hr.analytics` (HR Analytics)
- `LocalCompanySeeder`: added `projects.milestones` and `hr.analytics` to demo company subscriptions

**Fixed — ProjectsResourcesTest stale module keys:**
- Split `projects.projects` test into separate `projects.tasks` (ProjectResource) and `projects.milestones` (ProjectMilestoneResource) tests
- Fixed `projects.boards` → `projects.kanban` for KanbanBoardResource test

**Built — Dashboard widgets:**
- `app/Filament/Projects/Widgets/ActiveSprintsWidget.php` — active sprint count, in-progress tasks, done tasks
- `app/Filament/Projects/Widgets/MyTasksWidget.php` — my open tasks, due today, overdue (per auth user)
- `app/Filament/Projects/Widgets/ProjectsOverviewWidget.php` — active projects, total tasks, completed tasks
- `app/Filament/App/Widgets/CompanyOverviewWidget.php` — team members, active modules, company name
- Registered all widgets in `Projects/Pages/Dashboard.php`, `Hr/Pages/Dashboard.php` (existing HeadcountWidget, DepartmentBreakdownWidget, LeaveStatsWidget), `App/Pages/Dashboard.php`
- Fixed `getColumns(): array|int` signature on all 3 dashboard pages (Filament 5 parent uses `array|int` not `int|string|array`)

**Built — Sprint Kanban view:**
- `app/Filament/Projects/Resources/SprintResource/Pages/ViewSprint.php` — Filament resource Page, mounts sprint with eager-loaded tasks + assignees; `getTasksByStatus()` buckets tasks into 4 kanban columns; `getSprintProgress()` calculates % done; header actions: Start Sprint, Complete Sprint, Edit
- `resources/views/filament/projects/pages/view-sprint.blade.php` — sprint goal info box, progress bar, date range, 4-column kanban grid (1 col mobile, 2 md, 4 xl), task cards with priority badge + story points + assignee avatar initials, empty-state per column
- `SprintResource.php`: added `ViewSprint` import, `'view' => ViewSprint::route('/{record}')` in `getPages()`, `\Filament\Actions\ViewAction::make()` "View Board" action in table

**All PHP files pass `php -l` syntax checks.**

---

## Related

- [[ACTIVATION_GUIDE]]
- [[STATUS_Dashboard]]
- [[MOC_Projects]]
