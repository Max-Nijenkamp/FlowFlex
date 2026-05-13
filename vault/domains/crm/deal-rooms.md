---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.deal-rooms
status: planned
color: "#4ADE80"
---

# Deal Rooms

> Shared digital workspace for buyer and seller — documents, proposals, Q&A thread, mutual action plan, and stakeholder access — to close complex deals collaboratively.

**Panel:** `crm`
**Module key:** `crm.deal-rooms`

## What It Does

Deal Rooms provides a secure, shared microsite for each deal where the sales team and the buyer's stakeholders collaborate throughout the evaluation and closing process. The deal room consolidates everything the buyer needs — proposals, case studies, product specs, pricing — in one link rather than scattered email attachments. A mutual action plan keeps both sides accountable to a close timeline. A Q&A thread captures buyer questions and responses. Buyer engagement data (page views, document opens, video plays) is tracked and fed into Revenue Intelligence as deal health signals.

## Features

### Core
- Deal room creation: linked to a deal — one deal room per deal (or more for complex multi-phase deals)
- Content sections: documents (upload PDFs, link to Quotes module), text sections (rich text for custom content), video embeds (Loom, Vimeo links)
- Mutual action plan: numbered checklist of steps for both parties with owner (buyer or seller), due date, and completion status
- Q&A thread: buyer submits questions; seller responds — full threaded conversation visible to both parties
- Secure access: buyer accesses via a magic link (no FlowFlex account needed) — link expires or can be revoked

### Advanced
- Stakeholder management: seller adds multiple buyer stakeholders (email addresses) to the deal room — each receives a personalised access link; engagement tracked per stakeholder
- Buyer engagement analytics: page views, document opens, time on page, video play duration — all tracked per stakeholder and surfaced in the deal record
- Notifications: when a buyer views the deal room for the first time, opens a document, or submits a Q&A question — rep notified via notification module
- Branding: deal room uses company branding (logo, primary colour) from Company Settings — white-label appearance for the buyer
- Deal room templates: reusable room structures for common deal types (SMB, Enterprise, Partner) — sections and mutual action plan pre-populated

### AI-Powered
- Engagement scoring: AI combines all buyer engagement signals into a deal room score — heavily engaged = buying signal; low engagement = risk; score surfaced in Revenue Intelligence
- Suggested content: when creating a deal room for a specific industry, AI suggests which case studies and documents from the library to include based on past winning deals for that vertical

## Data Model

```erDiagram
    deal_rooms {
        ulid id PK
        ulid company_id FK
        ulid deal_id FK
        string name
        string slug "unique"
        string status
        json sections
        timestamp expires_at
        timestamps created_at/updated_at
    }

    deal_room_stakeholders {
        ulid id PK
        ulid deal_room_id FK
        string name
        string email
        string access_token
        integer page_views
        timestamp last_visit_at
        timestamps created_at/updated_at
    }

    deal_room_mutual_actions {
        ulid id PK
        ulid deal_room_id FK
        string title
        string owner_side
        date due_date
        boolean is_complete
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `slug` | URL-safe identifier used in deal room public URL |
| `owner_side` | buyer / seller |
| `sections` | JSON content blocks for the deal room page |

## Permissions

- `crm.deal-rooms.create`
- `crm.deal-rooms.edit`
- `crm.deal-rooms.view-engagement`
- `crm.deal-rooms.manage-stakeholders`
- `crm.deal-rooms.manage-templates`

## Filament

- **Resource:** `DealRoomResource`
- **Pages:** `ListDealRooms`, `ViewDealRoom` (with sections editor, stakeholder list, engagement analytics)
- **Custom pages:** `DealRoomEngagementPage` — per-deal stakeholder engagement heatmap
- **Widgets:** `DealRoomEngagementWidget` — deals with high buyer engagement this week on CRM dashboard
- **Nav group:** Activities (crm panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Dock | Digital sales rooms |
| Aligned | Buyer engagement platform |
| DealRoom (M&A) | Virtual deal room |
| Pitch | Shared presentation and collaboration |

## Implementation Notes

**Buyer-facing deal room (not a Filament page):** The deal room microsite visited by buyers is NOT a Filament resource view. It is a public Vue 3 + Inertia page (or a standalone Blade page with no auth middleware) served from a route like `GET /room/{slug}`. The buyer authenticates via their unique `deal_room_stakeholders.access_token` — no FlowFlex account needed. The `DealRoomController` reads the `slug`, verifies `expires_at`, and returns the `deal_rooms.sections` JSON rendered as a Vue page with the company's branding.

**Filament side (`DealRoomResource`):** The seller-facing Filament interface provides standard `ListDealRooms`, `ViewDealRoom` pages. `ViewDealRoom` uses a Filament `Tabs` layout: Tab 1 = Sections editor (Livewire component for adding/editing content blocks in `sections` JSON), Tab 2 = Stakeholders (standard RelationManager for `deal_room_stakeholders`), Tab 3 = Mutual Action Plan (Livewire component for the checklist), Tab 4 = Q&A (Livewire threaded discussion component), Tab 5 = Engagement (chart.js timeline of stakeholder activity).

**`deal_rooms.sections` JSON schema:** Document this schema explicitly — it is the core content structure. Each section is `{type: "text"|"document"|"video_embed"|"quote_embed", title: string, content: object}`. Text content: `{html: string}`. Document content: `{media_id: ulid, filename: string, size_bytes: int}`. Video embed: `{url: string, provider: "loom"|"vimeo"|"youtube"}`. Quote embed: `{quote_id: ulid}` (links to the CRM quotes module).

**Buyer engagement tracking:** When a buyer accesses the deal room, increment `deal_room_stakeholders.page_views` and update `last_visit_at`. For document opens, log via an API endpoint `POST /api/v1/room/{slug}/track` called from the Vue buyer page. This endpoint is unauthenticated — rate-limit by IP and token to prevent inflated tracking. Store engagement events in an `deal_room_engagement_events {ulid id, ulid stakeholder_id, string event_type, string section_ref, timestamp occurred_at}` table — not currently defined. Add it.

**Q&A thread:** The Q&A uses a simple Eloquent model `deal_room_qa_threads {ulid id, ulid deal_room_id, ulid stakeholder_id FK (nullable for seller), string author_type (buyer|seller), text content, ulid parent_id FK nullable, timestamps}` — not currently defined. Add it. Buyer posts are submitted via the public API endpoint; seller replies are submitted via Filament.

**Real-time notifications:** When a buyer views the deal room for the first time (`page_views = 1`), a `DealRoomFirstViewedNotification` is dispatched to the deal owner via the notifications module. Q&A questions from buyers also trigger a notification. These use Laravel's standard notification + queued mail — no Reverb required.

**AI features:** Engagement scoring is a weighted PHP formula: `(page_views × 1) + (document_opens × 3) + (video_plays × 2) + (mutual_action_completes × 5) + (qa_questions × 4)`. Normalised to 0–100. Updated by `UpdateDealRoomEngagementScoreJob` dispatched after each engagement event. Suggested content calls `app/Services/AI/DealRoomSuggestionService.php` which queries won deals in the same industry vertical and finds the most-used document types in their deal rooms.

## Related

- [[deals]]
- [[quotes]]
- [[contracts]]
- [[revenue-intelligence]]
- [[activities]]
