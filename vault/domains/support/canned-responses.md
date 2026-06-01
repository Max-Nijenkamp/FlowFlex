---
type: module
domain: Support & Help Desk
panel: support
module-key: support.canned
status: planned
color: "#4ADE80"
---

# Canned Responses

Saved reply templates for common questions. Agents insert them into ticket replies and live chat with one click, with variable substitution.

## Core Features

- Canned response: title, shortcut code (e.g. `/refund`), body (rich text), category
- Variable placeholders: `{{customer_name}}`, `{{agent_name}}`, `{{ticket_number}}` auto-filled on insert
- Shortcut insertion: type `/shortcut` in reply box to insert
- Categories for organisation
- Usage tracking: count how often each response is used
- Shared (team-wide) vs personal canned responses
- Search canned responses by title or content

## Data Model

| Table | Key Columns |
|---|---|
| `sup_canned_responses` | company_id, title, shortcut, body, category, owner_id, is_shared, usage_count |

## Filament

**Nav group:** Settings

- `CannedResponseResource` — list, create, edit
- Insert action available within ticket reply and live chat composers

## Related

- [[domains/support/tickets]]
- [[domains/support/live-chat]]
