---
type: module
domain: CRM & Sales
panel: crm
cssclasses: domain-crm
phase: 3
status: in-progress
migration_range:
last_updated: 2026-05-11
right_brain_log: "[[builder-log-crm-phase3]]"
---

# Shared Inbox & Email

Team inbox for incoming customer emails (sales@, support@). Phase 3: shared view of CRM ticket emails + manual assignment. Phase 5: full email thread ingestion with threading, collision detection, and auto-CRM sync.

**Panel:** `crm`  
**Phase:** 3 (basic inbox built on crm_tickets) · 5 (full email integration)  
**Module key:** `crm.inbox`

---

## Phase 3 — Basic Inbox (Built on crm_tickets)

No dedicated migration in Phase 3. The inbox is a Filament resource view that:
- Shows all `crm_tickets` with `source = 'email'` or `source = 'manual'`
- Allows manual assignment to an agent
- Displays ticket title, contact, status, and last comment
- Supports bulk assign and bulk status change

This gives teams a shared view of inbound requests without requiring email integration plumbing in Phase 3.

---

## Phase 5 — Full Email Integration (Deferred)

Full implementation deferred to Phase 5 alongside [[email-integration]] in Communications domain.

Phase 5 will add:
- Connect shared inbox email account (Gmail/Outlook OAuth, IMAP/SMTP)
- Inbound email → auto-create ticket or append comment to existing thread
- Email threading: group replies by Message-ID / In-Reply-To headers
- Collision detection: show "Jane is typing a reply..." warning
- Send reply from inbox → logged as ticket comment, sent via connected account
- Auto-assignment rules (by sender domain, keywords, round-robin)
- Snooze: hide ticket until a date, then surface again

---

## Permissions

```
crm.inbox.view
crm.inbox.assign
crm.inbox.reply
crm.inbox.manage-connections
```

---

## Related

- [[MOC_CRM]]
- [[customer-support-helpdesk]] — tickets are the underlying data model
- [[email-tracking]] — individual sales email tracking (different from shared inbox)
- [[MOC_Communications]] — email-integration (Phase 5) feeds this inbox
