---
type: module
domain: CRM & Sales
panel: crm
cssclasses: domain-crm
phase: 3
status: planned
migration_range:
last_updated: 2026-05-11
right_brain_log: "[[builder-log-crm-phase3]]"
---

# Sales Sequences & Cadences

Automated multi-step outreach sequences for lead nurturing: email, call, LinkedIn tasks, and follow-up reminders. Phase 3: skeleton module only. Full automation deferred to Phase 5 when Marketing email infrastructure is built.

**Panel:** `crm`  
**Phase:** 3 (skeleton) · 5 (full implementation)  
**Module key:** `crm.sequences`

---

## Phase 3 — Skeleton

Phase 3 registers the module in the `ModuleCatalogSeeder` and creates a placeholder Filament page. No migrations or functional steps in Phase 3 — the UI shows "Coming in Phase 5" with a description.

---

## Phase 5 — Full Implementation

Full sequences require the Marketing email-sending infrastructure (email domain, templates, deliverability). Phase 5 will implement:

### Data Model (Phase 5)

```erDiagram
    crm_sequences {
        ulid id PK
        ulid company_id FK
        string name
        string description
        boolean is_active
        ulid created_by FK
    }

    crm_sequence_steps {
        ulid id PK
        ulid sequence_id FK
        integer step_number
        string type
        integer delay_days
        string email_subject
        text email_body
        string task_description
    }

    crm_sequence_enrollments {
        ulid id PK
        ulid sequence_id FK
        ulid contact_id FK
        ulid deal_id FK
        integer current_step
        string status
        timestamp enrolled_at
        timestamp completed_at
        timestamp paused_at
        ulid enrolled_by FK
    }
```

**Step type:** `email` | `call` | `task` | `linkedin` | `wait`

**Enrollment status:** `active` | `completed` | `paused` | `unsubscribed`

### Features (Phase 5)
- Build sequences: drag-and-drop step builder with delay between steps
- Enroll contacts manually or via automation rule (e.g. new lead → enroll in cold outreach sequence)
- Email steps: send via connected email account with merge tags
- Task steps: create CRM activity (call, LinkedIn message) for assigned rep
- Auto-pause on reply: detect reply to sequence email → pause enrollment
- Performance: open rate, reply rate, booking rate per sequence

---

## Permissions

```
crm.sequences.view
crm.sequences.create
crm.sequences.manage
crm.sequences.enroll
```

---

## Related

- [[MOC_CRM]]
- [[email-tracking]] — sequence emails use same tracking infrastructure
- [[contact-company-management]] — contacts enrolled in sequences
- [[MOC_Marketing]] — Marketing email domain (Phase 5) provides sending infrastructure
