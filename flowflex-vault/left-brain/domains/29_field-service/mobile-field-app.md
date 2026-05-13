---
type: module
domain: Field Service Management
panel: fsm
module: Mobile Field App
phase: 5
status: complete
cssclasses: domain-fsm
migration_range: 1050500–1050999
last_updated: 2026-05-12
---

# Mobile Field App

Progressive Web App (PWA) for iOS and Android. Technicians receive jobs, navigate, record work, capture photos, complete checklists, collect signatures, and log parts — all from their phone. Works offline.

---

## Why PWA (Not Native App)

- No App Store approval delays for updates
- Single codebase (Vue 3 + Capacitor for device APIs)
- InstallToHomeScreen on iOS / Android
- Offline-first with sync-on-reconnect

---

## Key Tables

```sql
CREATE TABLE fsm_job_updates (
    id              ULID PRIMARY KEY,
    job_id          ULID NOT NULL REFERENCES fsm_jobs(id),
    technician_id   ULID NOT NULL REFERENCES fsm_technicians(id),
    event_type      ENUM('arrived','started','paused','resumed','completed','no_access','photo_added','note_added','part_used'),
    notes           TEXT NULL,
    location_lat    DECIMAL(10,7) NULL,
    location_lng    DECIMAL(10,7) NULL,
    recorded_at     TIMESTAMP NOT NULL,
    synced_at       TIMESTAMP NULL,    -- NULL if created offline, set when synced
    created_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE fsm_job_photos (
    id              ULID PRIMARY KEY,
    job_id          ULID NOT NULL REFERENCES fsm_jobs(id),
    technician_id   ULID NOT NULL REFERENCES fsm_technicians(id),
    storage_path    VARCHAR(500),
    caption         VARCHAR(500) NULL,
    taken_at        TIMESTAMP NOT NULL
);

CREATE TABLE fsm_job_checklists (
    id              ULID PRIMARY KEY,
    job_id          ULID NOT NULL REFERENCES fsm_jobs(id),
    template_id     ULID NULL,          -- from fsm_checklist_templates
    title           VARCHAR(255),
    completed_at    TIMESTAMP NULL,
    completed_by    ULID NULL REFERENCES fsm_technicians(id)
);

CREATE TABLE fsm_job_checklist_items (
    id              ULID PRIMARY KEY,
    checklist_id    ULID NOT NULL REFERENCES fsm_job_checklists(id),
    item            TEXT NOT NULL,
    item_type       ENUM('checkbox','text','number','photo','signature'),
    is_required     BOOLEAN DEFAULT FALSE,
    value           TEXT NULL,
    checked_at      TIMESTAMP NULL
);
```

---

## App Screens

1. **Job List** — today's jobs, ordered by scheduled time; tap to open job
2. **Job Detail** — customer info, address, description, skill requirements, history
3. **Navigation** — deep-link to Apple Maps / Google Maps with job address
4. **Clock In/Out** — start / pause / complete job with GPS stamp
5. **Checklist** — step-by-step inspection form, mandatory items blocked until answered
6. **Parts** — search parts, log usage from van stock
7. **Photos** — camera capture, label, attach to job
8. **Signature** — customer sign-off screen (see [[customer-sign-off]])
9. **Notes** — free text + voice-to-text
10. **Summary** — review before submit, trigger invoice generation

---

## Offline Mode

Technician works offline → actions queued in IndexedDB.  
On reconnect (or when dispatch board pings): batch sync to server.  
Conflict resolution: `recorded_at` timestamp wins; server logs both if conflict.

Offline capabilities: view assigned jobs, complete checklists, log parts, capture photos.  
Requires sync: signature capture triggers invoice generation (needs server).

---

## Related

- [[MOC_FieldService]]
- [[job-dispatch-scheduling]]
- [[customer-sign-off]]
- [[parts-inventory-fsm]]
