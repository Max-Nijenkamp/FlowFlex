---
type: module
domain: HR & People
panel: hr
module: Talent Intelligence
phase: 5
status: complete
cssclasses: domain-hr
migration_range: 108500–108999
last_updated: 2026-05-12
---

# Talent Intelligence

Skills taxonomy, competency framework, skill gap analysis, career pathing, and internal mobility matching. Bridges HR performance data with LMS learning plans and hiring decisions.

---

## Why This Matters

Most HR systems store job titles and org chart positions — not skills. Without a skills taxonomy:
- "Who can lead this project?" → look at org chart + ask around
- Training budgets allocated by role, not by individual skill gaps
- Internal mobility is invisible → top performers leave for external roles

With Talent Intelligence:
- Company has a live skills map of every employee
- Skill gaps identified → auto-suggest LMS courses
- Internal job openings matched to employees with closest skill profile
- Succession plans backed by skill data, not just seniority

---

## Key Tables

```sql
CREATE TABLE hr_skills (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    name            VARCHAR(100) NOT NULL,
    category        VARCHAR(100) NULL,   -- e.g. "Technical", "Leadership", "Domain"
    description     TEXT NULL,
    parent_id       ULID NULL REFERENCES hr_skills(id),  -- taxonomy tree
    is_active       BOOLEAN DEFAULT TRUE
);

CREATE TABLE hr_employee_skills (
    id              ULID PRIMARY KEY,
    employee_id     ULID NOT NULL REFERENCES hr_employees(id),
    skill_id        ULID NOT NULL REFERENCES hr_skills(id),
    level           TINYINT DEFAULT 1,   -- 1=learning, 2=competent, 3=proficient, 4=expert
    verified_by     ULID NULL REFERENCES users(id),
    verified_at     DATE NULL,
    source          ENUM('self_assessed','manager_assessed','test_verified','certification'),
    updated_at      TIMESTAMP DEFAULT NOW(),
    UNIQUE(employee_id, skill_id)
);

CREATE TABLE hr_role_skill_requirements (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    role_title      VARCHAR(100),
    skill_id        ULID NOT NULL REFERENCES hr_skills(id),
    required_level  TINYINT DEFAULT 2,
    is_mandatory    BOOLEAN DEFAULT TRUE
);

CREATE TABLE hr_career_paths (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    from_role       VARCHAR(100),
    to_role         VARCHAR(100),
    typical_months  INT NULL,
    notes           TEXT NULL
);

CREATE TABLE hr_internal_job_postings (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    position_id     ULID NULL REFERENCES hr_positions(id),
    title           VARCHAR(100),
    description     TEXT,
    required_skills JSON,    -- [{skill_id, min_level}]
    status          ENUM('draft','open','closed','filled'),
    posted_at       TIMESTAMP NULL,
    closes_at       DATE NULL
);
```

---

## Skill Gap Analysis

Per employee:
1. Load their `hr_employee_skills` with levels
2. Load `hr_role_skill_requirements` for their current role
3. Gap = required_level − current_level (where > 0)
4. Sort gaps by gap size → "Top 3 development priorities"
5. Auto-suggest LMS courses tagged to each skill (via `lms_course_skills` join table)

---

## Internal Mobility Matching

When an internal job posted:
1. System scores all employees: SUM(skill matches × weight) / required_skill_count
2. Top N matches surfaced to hiring manager (HR admin sees matches, employee privacy respected)
3. Optional: employees can opt-in to "interested in new opportunities" flag

---

## EU AI Act Note

If skill scoring used to make employment decisions → classified as **High Risk** under EU AI Act.  
Must implement `ai_human_oversight_log` for any AI-influenced promotion/hiring decisions.  
See [[eu-ai-act-compliance]].

---

## Related

- [[MOC_HR]]
- [[performance-reviews-360]] — reviews feed skill assessments
- [[org-chart-workforce-planning]] — positions define required skills
- [[MOC_LMS]] — skill gaps auto-suggest courses
- [[eu-ai-act-compliance]] — if AI used in employment decisions
