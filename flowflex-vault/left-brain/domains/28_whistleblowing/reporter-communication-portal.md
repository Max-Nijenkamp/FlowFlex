---
type: module
domain: Whistleblowing & Ethics Hotline
panel: whistleblowing
module: Reporter Communication Portal
phase: 4
status: planned
cssclasses: domain-whistleblowing
migration_range: 1001500–1001999
last_updated: 2026-05-09
---

# Reporter Communication Portal

Two-way encrypted messaging between anonymous reporter and case investigators. Reporter accesses via `report_token` only — no account, no email needed. Investigators can request additional information without breaking reporter anonymity.

---

## Key Tables

```sql
CREATE TABLE ethics_messages (
    id              ULID PRIMARY KEY,
    case_id         ULID NOT NULL REFERENCES ethics_cases(id),
    direction       ENUM('reporter_to_company','company_to_reporter'),
    sender_token    VARCHAR(64) NULL,   -- reporter's report_token (if reporter→company)
    sender_user_id  ULID NULL REFERENCES users(id),  -- if company→reporter
    body            TEXT NOT NULL,      -- encrypted at rest
    is_read         BOOLEAN DEFAULT FALSE,
    read_at         TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT NOW()
);

-- Tracks when reporter last checked portal (anonymously)
CREATE TABLE ethics_reporter_sessions (
    id              ULID PRIMARY KEY,
    report_token    VARCHAR(64) NOT NULL,
    accessed_at     TIMESTAMP DEFAULT NOW(),
    ip_hash         VARCHAR(64) NULL  -- SHA-256 of IP for abuse prevention only, never stored raw
);
```

---

## Reporter Portal Flow

1. Reporter visits `report.{company}.flowflex.io/track`
2. Enters `report_token` (UUID shown at original submission)
3. Sees: case status, timeline (sanitised — no investigator names), unread messages
4. Can reply to investigator questions
5. Can upload additional evidence
6. Can request case escalation

---

## Investigator View

In Filament case detail page:
- Thread of messages (company-side shows investigator name, reporter-side shows "Anonymous Reporter")
- Send message button → creates `ethics_messages` with `direction = company_to_reporter`
- Unread badge when reporter replies
- Reporter "last seen" timestamp (when they last checked portal)

---

## Encryption

Messages encrypted at rest using per-case AES-256 key.  
Case key derived from `report_token` + server-side master key (HKDF).  
Investigators decrypt via server-side API — keys never leave server.  
If reporter loses `report_token`, messages are not recoverable (no password reset).

---

## Notifications to Reporter

No email. No SMS. Reporter must return to portal to check.  
Optional: if reporter provided contact method (non-mandatory), system can notify "your case has an update" without revealing case content.

---

## Related

- [[MOC_Whistleblowing]]
- [[anonymous-intake-portal]]
- [[case-management-investigation]]
