---
type: module
domain: Support & Help Desk
panel: support
module-key: support.canned-responses
status: planned
color: "#4ADE80"
---

# Canned Responses

> Shared library of pre-written reply templates with variable substitution, personal/team/company scope, keyboard shortcut search in the ticket reply box, and usage analytics.

**Panel:** `/support`
**Module key:** `support.canned-responses`

## What It Does

Canned Responses gives support agents a library of pre-approved reply templates they can insert into tickets with a single keystroke — eliminating the need to retype common answers. Templates support variable placeholders (`{{customer_name}}`, `{{ticket_id}}`, `{{agent_name}}`) that are automatically substituted at insert time. Responses can be personal (visible only to the creating agent), team-scoped (visible to a team), or company-wide. Agents trigger the search panel by typing `//` in the ticket reply box, which opens an instant fuzzy-search overlay. Usage analytics show which responses are used most frequently, helping managers identify gaps and retire stale templates.

## Features

### Core
- Template library with title, body (plain text or HTML), shortcut code, and category tag
- Variable substitution at insert time: `{{customer_name}}`, `{{ticket_id}}`, `{{ticket_subject}}`, `{{agent_name}}`, `{{company_name}}`, `{{date}}`, `{{time}}`
- Three scope levels: personal (my own templates), team (shared with a specific team), company (available to all agents)
- In-ticket insertion via `//` trigger: typing `//` in the reply textarea opens a Livewire search overlay. Typing continues to filter responses by title or shortcut. Press Enter or click to insert.
- Keyboard shortcut direct insert: if an agent types the exact shortcut code followed by Tab, the response is inserted inline without opening the search overlay
- Categories and tags for organisation within the library

### Advanced
- HTML-formatted canned responses: templates can contain HTML (bold, lists, links) — inserted into the rich text reply editor
- Team scope management: team leaders can manage their team's shared responses. Company-scope responses are managed by admin users.
- Usage count tracking: each insert increments `usage_count`. List view shows most-used responses.
- Bulk import from CSV: import a batch of canned responses via CSV (title, body, shortcut, category, scope)
- Clone and edit: duplicate an existing response to create a variant without starting from scratch
- Stale response detection: flag responses not used in 90 days for review or retirement

### AI-Powered
- AI response drafting: input a description of the use case and Claude drafts a canned response body ready to review and save
- Tone analysis: AI checks whether a canned response body matches the company's configured tone of voice (formal/friendly/technical) and suggests improvements

## Data Model

```erDiagram
    support_canned_responses {
        ulid id PK
        ulid company_id FK
        ulid created_by FK
        ulid team_id FK
        string scope
        string title
        text body
        string shortcut
        string category
        integer usage_count
        timestamp last_used_at
        timestamps created_at/updated_at
        timestamp deleted_at
    }
```

| Column | Notes |
|---|---|
| `scope` | personal / team / company — determines visibility |
| `shortcut` | short alphanumeric code (e.g. `greet`, `close`) — must be unique per company. Direct insert via `//shortcodeTab` |
| `body` | Stored as HTML to support rich formatting; plain text also supported |
| `team_id` | Only populated when `scope = team` — FK to `teams` table |
| `usage_count` | Incremented on every insert via a `IncrementCannedResponseUsage` queued job |
| `last_used_at` | Updated on every insert — used by stale detection |

## Permissions

```
support.canned-responses.view
support.canned-responses.create
support.canned-responses.edit
support.canned-responses.delete
support.canned-responses.manage-company
```

## Filament

- **Resource:** `CannedResponseResource` — standard list/create/edit for managing the template library. List view grouped by scope with usage count column.
- **Pages:** `ListCannedResponses`, `CreateCannedResponse`, `EditCannedResponse`
- **Custom pages:** None
- **Widgets:** None — canned responses surface in `TicketDetailPage` via a Livewire component that intercepts `//` keypress in the reply textarea and renders a floating search overlay. This component is part of `TicketDetailPage`, not a standalone page or widget.
- **Nav group:** Inbox (support panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Zendesk | Macros (response templates) |
| Freshdesk | Canned responses |
| Helpscout | Saved replies |
| Intercom | Saved replies, macros |

## Related

- [[support-tickets]]
- [[ticket-automations]]

## Implementation Notes

- **`//` trigger mechanism:** `TicketDetailPage` includes a Livewire component `CannedResponsePicker`. It registers a JavaScript listener (Alpine.js `x-on:keydown`) on the reply textarea. When `//` is detected, the component switches to search mode. Subsequent keystrokes are forwarded to a debounced `wire:search` property that filters `support_canned_responses` via a Livewire action. Results render in a floating panel (Filament Modal or custom dropdown). Selection dispatches a Livewire event `insertCannedResponse` received by the reply textarea component.
- **Variable substitution:** Performed by a `CannedResponseInterpolator` service at insert time. It receives the raw template body and the current ticket context (ticket, contact, agent) and replaces all `{{variable}}` placeholders. Unknown variables are left as-is with a warning annotation so the agent notices before sending.
- **Scope query:** The `CannedResponseResource` query scopes results based on the current user: always includes `scope = company` rows and `scope = personal` rows where `created_by = auth()->id()`. If the user belongs to a team, also includes `scope = team` rows for their team.
- **HTML body:** Rich text in canned responses is sanitised on save using `HTMLPurifier` (via `mews/purifier` package) to prevent XSS. On insert into the reply box, the sanitised HTML is injected into the Tiptap editor as a HTML fragment.
