---
type: module
domain: Field Service Management
panel: fsm
module: Technician & Team Management
phase: 5
status: complete
cssclasses: domain-fsm
migration_range: 1051000–1051499
last_updated: 2026-05-12
---

# Technician & Team Management

Skills registry, certifications, territory assignment, availability management, and performance tracking for field service technicians.

---

## Key Tables

```sql
CREATE TABLE fsm_technicians (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    user_id         ULID NOT NULL REFERENCES users(id),   -- links to HR employee
    employee_id     ULID NULL REFERENCES hr_employees(id),
    technician_number VARCHAR(20) UNIQUE,
    status          ENUM('active','inactive','on_leave','suspended') DEFAULT 'active',
    vehicle_registration VARCHAR(20) NULL,
    home_lat        DECIMAL(10,7) NULL,  -- for travel time from home
    home_lng        DECIMAL(10,7) NULL,
    default_territory_id ULID NULL,
    created_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE fsm_skills (
    id          ULID PRIMARY KEY,
    company_id  ULID NOT NULL REFERENCES companies(id),
    name        VARCHAR(100) NOT NULL,
    category    VARCHAR(100) NULL,  -- e.g. "HVAC", "Electrical", "Plumbing"
    description TEXT NULL
);

CREATE TABLE fsm_technician_skills (
    id              ULID PRIMARY KEY,
    technician_id   ULID NOT NULL REFERENCES fsm_technicians(id),
    skill_id        ULID NOT NULL REFERENCES fsm_skills(id),
    level           TINYINT DEFAULT 1,  -- 1=basic, 2=intermediate, 3=expert
    certified_at    DATE NULL,
    expires_at      DATE NULL,
    certification_ref VARCHAR(255) NULL
);

CREATE TABLE fsm_territories (
    id          ULID PRIMARY KEY,
    company_id  ULID NOT NULL REFERENCES companies(id),
    name        VARCHAR(100) NOT NULL,
    postcodes   JSON NULL,    -- array of postcode prefixes
    geojson     JSON NULL     -- polygon boundary
);

CREATE TABLE fsm_technician_territories (
    id              ULID PRIMARY KEY,
    technician_id   ULID NOT NULL REFERENCES fsm_technicians(id),
    territory_id    ULID NOT NULL REFERENCES fsm_territories(id)
);
```

---

## Performance Metrics

Calculated from `fsm_job_updates` and `fsm_jobs`:

| Metric | Calculation |
|---|---|
| Jobs completed / week | COUNT jobs WHERE status = completed in period |
| On-time rate | % jobs where actual_start ≤ scheduled_start + 15 min |
| First-time fix rate | % jobs completed without follow-up visit within 14 days |
| Average job duration | AVG(actual_end - actual_start) per job type |
| Customer satisfaction | Average rating from sign-off forms |
| Parts usage accuracy | Parts logged vs parts invoiced |

---

## Certification Expiry Alerts

`CertificationExpiringJob` runs weekly → notifies technician + manager 60, 30, 7 days before expiry.  
Expired certification → skill level locked, job assignment blocked for that skill.  
Integrates with [[MOC_LMS]] for renewal course links.

---

## Related

- [[MOC_FieldService]]
- [[job-dispatch-scheduling]]
- [[MOC_HR]] — technicians are HR employees
