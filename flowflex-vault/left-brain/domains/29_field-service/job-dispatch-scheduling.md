---
type: module
domain: Field Service Management
panel: fsm
module: Job Dispatch & Scheduling
phase: 5
status: complete
cssclasses: domain-fsm
migration_range: 1050000–1050499
last_updated: 2026-05-12
---

# Job Dispatch & Scheduling

Drag-and-drop dispatch board with geographic clustering, skill-based technician matching, and travel time estimation. The central operations view for dispatchers.

---

## Key Tables

```sql
CREATE TABLE fsm_jobs (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    job_number      VARCHAR(20) UNIQUE,  -- e.g. FSM-2026-00142
    customer_id     ULID NOT NULL REFERENCES contacts(id),
    asset_id        ULID NULL,           -- customer asset being serviced
    type            ENUM('installation','maintenance','repair','inspection','emergency'),
    priority        ENUM('low','normal','high','emergency') DEFAULT 'normal',
    status          ENUM('unscheduled','scheduled','en_route','in_progress','completed','cancelled','no_access'),
    description     TEXT,
    scheduled_start TIMESTAMP NULL,
    scheduled_end   TIMESTAMP NULL,
    actual_start    TIMESTAMP NULL,
    actual_end      TIMESTAMP NULL,
    location_address TEXT,
    location_lat    DECIMAL(10,7) NULL,
    location_lng    DECIMAL(10,7) NULL,
    source          ENUM('manual','crm_request','maintenance_contract','emergency_call'),
    source_id       ULID NULL,
    created_at      TIMESTAMP DEFAULT NOW(),
    updated_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE fsm_job_technicians (
    id              ULID PRIMARY KEY,
    job_id          ULID NOT NULL REFERENCES fsm_jobs(id),
    technician_id   ULID NOT NULL REFERENCES fsm_technicians(id),
    role            ENUM('lead','assistant'),
    assigned_at     TIMESTAMP DEFAULT NOW()
);

CREATE TABLE fsm_job_skills_required (
    id              ULID PRIMARY KEY,
    job_id          ULID NOT NULL REFERENCES fsm_jobs(id),
    skill_id        ULID NOT NULL REFERENCES fsm_skills(id),
    required_level  TINYINT DEFAULT 1
);
```

---

## Dispatch Board

Real-time board showing all technicians as columns, time slots as rows (30-min intervals).  
Jobs drag from "Unscheduled" pool into technician's time slot.  
Board data driven by `fsm_jobs` joined with technician availability.

```
Unscheduled          | Jan (Utrecht)     | Pieter (Amsterdam) | ...
─────────────────────|───────────────────|────────────────────|
[HVAC Service #1042] | 08:00 ████ #1038  | 08:00 ████ #1055   |
[Boiler Repair #1047]| 10:00             | 10:00 ████ #1048   |
[Emergency #1055]    | ...               | ...                |
```

Travel time between jobs calculated via HERE Maps / Google Maps API.  
Overlap warning fires when job scheduled before previous completes + travel time.

---

## Skill Matching

When creating a job, dispatcher can set required skills. Board highlights technicians who:
- Have all required skills (green border)
- Have some required skills (amber border)
- Miss required skills (red border)

---

## Emergency Job Flow

Priority `emergency` → plays alert sound for all dispatchers, floats to top of unscheduled queue.  
Auto-suggest nearest available technician with required skills.

---

## Service

```php
interface JobDispatchInterface
{
    public function createJob(CreateJobDTO $dto): JobDTO;
    public function assignTechnician(ULID $jobId, ULID $technicianId): void;
    public function reschedule(ULID $jobId, Carbon $newStart, Carbon $newEnd): void;
    public function suggestTechnician(ULID $jobId): Collection; // ranked list
    public function getDispatchBoard(Carbon $date, array $filters): DispatchBoardDTO;
}
```

---

## Related

- [[MOC_FieldService]]
- [[technician-management]]
- [[mobile-field-app]]
- [[MOC_CRM]]
