---
tags: [brain, domain, projects]
last_updated: 2026-05-07
---

# Domain — Projects

**Spec:** `03 - Projects & Work/` — Task Management, Time Tracking, Document Management  
**All models:** `app/Models/Projects/`  
**All resources:** `app/Filament/Projects/Resources/`  
**Panel:** `/projects` — guard `tenant`, color slate  
**Module key:** `projects`

All models carry: `BelongsToCompany`, `HasUlids`, `SoftDeletes`, `LogsActivity`.

---

## Models

### Task
**Spec:** `03 - Projects & Work/Task Management.md`  
**Table:** `tasks`  
**Purpose:** The core unit of work. Self-referential for unlimited sub-task nesting. Supports labels (many-to-many), dependencies (blocking/blocked-by), time tracking, and automation triggers.

**Fillable fields:**
- `company_id`
- `parent_id` → self (sub-task FK — the column added in gap-fill for `parent()`/`children()` relations)
- `parent_task_id` → self (original spec FK — also in fillable; `parentTask()`/`subtasks()` relations use this)
- `title`, `description` (text, nullable)
- `priority` → `TaskPriority` enum
- `status` → `TaskStatus` enum
- `assignee_tenant_id` (nullable → Tenant)
- `due_date` (date, nullable), `start_date` (date, nullable)
- `estimated_hours` (decimal, nullable)
- `is_recurring` (bool, default false), `recurrence_rule` (string, nullable — iCal RRULE format)

**Casts:** `priority` → `TaskPriority`, `status` → `TaskStatus`, `due_date`/`start_date` → date, `is_recurring` → boolean

**Relations:**
- `assignee()` → BelongsTo `Tenant` (via `assignee_tenant_id` — scope dropdown to `company_id`)
- `parent()` → BelongsTo `Task` (self, via `parent_id`)
- `children()` → HasMany `Task` (self, via `parent_id`)
- `parentTask()` → BelongsTo `Task` (self, via `parent_task_id`)
- `subtasks()` → HasMany `Task` (self, via `parent_task_id`)
- `labels()` → BelongsToMany `TaskLabel` via `task_label_assignments` (FK: `task_id`, `label_id`)
- `dependencies()` → HasMany `TaskDependency`
- `timeEntries()` → HasMany `TimeEntry`
- `automationLogs()` → HasMany `TaskAutomationLog`

**Spec vs impl note:** The spec (`03 - Projects & Work/Task Management.md`) lists a `task_subtasks` join table. Implementation uses a `parent_id` column directly on `tasks` — same result, different approach. Both `parent_id` and `parent_task_id` exist in `$fillable` (historical — `parent_task_id` was original; `parent_id` added in gap-fill for `parent()`/`children()`).

**Events fired:** `TaskCreated`, `TaskStatusChanged`, `TaskCompleted`

---

### TaskLabel
**Table:** `task_labels`  
**Purpose:** Colour-coded tags that can be attached to multiple tasks. Each label is company-scoped.

**Fillable fields:**
- `company_id`, `name`, `color` (string — hex color, e.g. `#FF5733`)

**Pivot table:** `task_label_assignments` — columns: `task_id`, `label_id`

**Permission trap:** Permission string is `projects.task-labels.*` — NOT `projects.tasks.*`. Different resource, different permission prefix.

---

### TaskDependency
**Table:** `task_dependencies`  
**Purpose:** Links two tasks with a dependency type. Enables Gantt-style blocking relationships.

**Fillable fields:**
- `task_id`, `depends_on_task_id`
- `type` (string: finish_to_start/start_to_start/finish_to_finish/start_to_finish)

**Relations:**
- `task()` → BelongsTo `Task`
- `dependsOn()` → BelongsTo `Task` (via `depends_on_task_id`)

---

### TaskAutomation
**Table:** `task_automations`  
**Purpose:** Rule-based automation that fires when a trigger condition is met. E.g., "When status changes to Done, assign to QA team member".

**Fillable fields:**
- `company_id`, `name`
- `trigger_type` (string: status_changed/due_date_passed/label_added/assignee_changed)
- `trigger_config` (JSON — e.g., `{"from": "todo", "to": "in_progress"}`)
- `action_type` (string: assign_to/add_label/send_notification/change_status)
- `action_config` (JSON — e.g., `{"tenant_id": "01..."}`)
- `is_enabled` (bool)

**Casts:** `trigger_config`/`action_config` → array, `is_enabled` → boolean

---

### TaskAutomationLog
**Table:** `task_automation_logs`  
**Purpose:** Audit log of every automation execution. Includes success/failure result for debugging.

**Fillable fields:**
- `task_automation_id`, `task_id`
- `triggered_at` (datetime)
- `result` (string: success/failed/skipped)
- `error_message` (text, nullable)

**Relations:**
- `automation()` → BelongsTo `TaskAutomation`
- `task()` → BelongsTo `Task`

---

### TimeEntry
**Spec:** `03 - Projects & Work/Time Tracking.md`  
**Table:** `time_entries`  
**Purpose:** A logged block of time by a team member on a specific task. Can be part of a timesheet. Billable flag enables client billing reports.

**Fillable fields:**
- `company_id`, `tenant_id` (who logged the time), `task_id` (nullable)
- `timesheet_id` (nullable — if grouped into a timesheet)
- `description` (string, nullable)
- `started_at` (datetime), `ended_at` (datetime, nullable)
- `duration_minutes` (int, nullable — can be set directly or computed from start/end)
- `is_billable` (bool, default false), `is_approved` (bool, default false)

**Guard:** Use `auth('tenant')->id()` — NOT `auth()->id()` — inside tenant panel context.

**Relations:**
- `task()` → BelongsTo `Task`
- `tenant()` → BelongsTo `Tenant`
- `timesheet()` → BelongsTo `Timesheet`

---

### Timesheet
**Table:** `timesheets`  
**Purpose:** Weekly grouping of time entries for a single team member. Submit-and-approve workflow.

**Fillable fields:**
- `company_id`, `tenant_id`
- `week_start` (date), `week_end` (date)
- `status` (string: draft/submitted/approved/rejected)
- `total_hours` (decimal), `submitted_at` (datetime, nullable)

**Guard:** Use `auth('tenant')->id()` throughout.

**Relations:**
- `tenant()` → BelongsTo `Tenant`
- `timeEntries()` → HasMany `TimeEntry`
- `approvals()` → HasMany `TimesheetApproval`

---

### TimesheetApproval
**Table:** `timesheet_approvals`  
**Purpose:** Records an approval or rejection decision on a timesheet. One record per decision.

**Fillable fields:**
- `timesheet_id`, `approver_tenant_id`
- `status` (string: approved/rejected)
- `notes` (text, nullable)
- `approved_at` (datetime)

**Relations:**
- `timesheet()` → BelongsTo `Timesheet`
- `approver()` → BelongsTo `Tenant` (via `approver_tenant_id`)

---

### DocumentFolder
**Spec:** `03 - Projects & Work/Document Management.md`  
**Table:** `document_folders`  
**Purpose:** Container for documents. Self-referential for unlimited folder nesting (like a filesystem tree).

**Fillable fields:**
- `company_id`, `parent_folder_id` (nullable → self, enables nesting)
- `name`, `description` (nullable)
- `created_by_tenant_id`

**Relations:**
- `parent()` → BelongsTo `DocumentFolder` (self, via `parent_folder_id`)
- `children()` → HasMany `DocumentFolder` (self, via `parent_folder_id`)
- `documents()` → HasMany `Document`
- `createdBy()` → BelongsTo `Tenant`

---

### Document
**Table:** `documents`  
**Purpose:** A versioned file in the document management system. `current_file_id` points to the active version; old versions kept in `DocumentVersion`. Shareable via time-limited signed links.

**Fillable fields:**
- `company_id`, `folder_id` (nullable → DocumentFolder)
- `current_file_id` → `File` (the active file version)
- `title`, `original_filename`, `mime_type`
- `file_size_bytes` (int)
- `version_number` (int, default 1)
- `uploaded_by_tenant_id`
- `is_starred` (bool, default false)
- `tags` (JSON array — free text tags)

**Casts:** `is_starred` → boolean, `tags` → array

**Relations:**
- `folder()` → BelongsTo `DocumentFolder`
- `currentFile()` → BelongsTo `File` (via `current_file_id`)
- `versions()` → HasMany `DocumentVersion`
- `shares()` → HasMany `DocumentShare`
- `uploadedBy()` → BelongsTo `Tenant`

**File access:** Always use `$document->currentFile->url()` or `FileStorageService::temporaryUrl()`. Never expose raw `path`.  
**Events fired:** `DocumentUploaded`, `DocumentShared`

---

### DocumentVersion
**Table:** `document_versions`  
**Purpose:** Historical record of every file version for a document. When a file is updated, a new DocumentVersion row is created, and `Document.current_file_id` is updated.

**Fillable fields:**
- `document_id`, `file_id` (→ File — the actual file for this version)
- `version_number` (int)
- `uploaded_by_tenant_id`
- `change_summary` (string, nullable)

**Relations:**
- `document()` → BelongsTo `Document`
- `file()` → BelongsTo `File`
- `uploadedBy()` → BelongsTo `Tenant`

---

### DocumentShare
**Table:** `document_shares`  
**Purpose:** A shareable link for a document. Can have an optional password and expiry. The `share_token` is the public URL segment.

**Fillable fields:**
- `document_id`, `share_token` (string — random, URL-safe)
- `password_hash` → `hashed` cast (optional password protection)
- `expires_at` (datetime, nullable)
- `is_active` (bool)
- `created_by_tenant_id`

**Casts:** `password_hash` → hashed, `expires_at` → datetime, `is_active` → boolean

**Security:** `password_hash` excluded from `logOnly()`. Never log password fields.

**Relations:**
- `document()` → BelongsTo `Document`
- `createdBy()` → BelongsTo `Tenant`

---

## Resources (Projects Panel)

| Resource | Model | Nav Group | Permissions | Key Features |
|---|---|---|---|---|
| `TaskResource` | `Task` | Tasks | `projects.tasks.*` | Full CRUD, labels via select |
| `TaskLabelResource` | `TaskLabel` | Tasks | `projects.task-labels.*` | CRUD — permissions are task-labels, NOT tasks |
| `TimeEntryResource` | `TimeEntry` | Time Tracking | `projects.time-entries.*` | CRUD, uses `auth('tenant')` guard |
| `TimesheetResource` | `Timesheet` | Time Tracking | `projects.timesheets.*` | CRUD, submit/approve actions |
| `DocumentFolderResource` | `DocumentFolder` | Documents | `projects.document-folders.*` | CRUD, nested folders |
| `DocumentResource` | `Document` | Documents | `projects.documents.*` | CRUD, versioning, share generation |

---

## Enums

### TaskStatus
`App\Enums\Projects\TaskStatus`  
`Backlog`, `Todo`, `InProgress`, `InReview`, `Done`

### TaskPriority
`App\Enums\Projects\TaskPriority`  
Backing values: `p1_critical`, `p2_high`, `p3_medium`, `p4_low`  
**Test pattern:** Use backing values in factory/test data: `'priority' => 'p3_medium'` — NOT `'medium'`.

---

## Events (Phase 2)

All wired in `EventServiceProvider`. All listeners implement `ShouldQueue`.

| Event | Listener | Type | What it does |
|---|---|---|---|
| `TaskCreated` | `NotifyAssignee` | real | Sends TaskAssignedNotification |
| `TaskStatusChanged` | stub | stub | — |
| `TaskCompleted` | stub | stub | — |
| `TimeEntryApproved` | stub | stub | — |
| `TimesheetSubmitted` | stub | stub | — |
| `TimesheetApproved` | stub | stub | — |
| `DocumentUploaded` | stub | stub | — |
| `DocumentShared` | stub | stub | — |
| `DocumentVersionCreated` | stub | stub | — |
| `FolderCreated` | stub | stub | — |
