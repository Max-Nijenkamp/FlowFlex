---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.deal-rooms
status: planned
color: "#4ADE80"
---

# Deal Rooms

Shared digital space for buyer and seller during a deal — documents, mutual action plan, Q&A, and stakeholder tracking. A modern "digital sales room".

## Core Features

- Deal room per deal: shared space accessible by external buyers (tokenised link)
- Document sharing: proposals, contracts, case studies (view tracking)
- Mutual action plan: shared checklist of steps to close (both sides update)
- Q&A thread: buyer asks, seller answers
- Stakeholder map: who's involved on the buyer side, their role
- Engagement tracking: which buyers viewed what, when
- Branded room (company logo/colours)
- Expiry/access control on the room link

## Data Model

| Table | Key Columns |
|---|---|
| `crm_deal_rooms` | company_id, deal_id, access_token, branding (json), expires_at |
| `crm_deal_room_documents` | room_id, company_id, media_id, view_count |
| `crm_deal_room_action_items` | room_id, company_id, description, owner_side (buyer/seller), status, due_date |
| `crm_deal_room_stakeholders` | room_id, company_id, name, role, contact_id |

## Filament

**Nav group:** Activities

- `DealRoomResource` — create/manage room per deal, see engagement
- Public room view via Vue + Inertia (external buyer access)

## Cross-Domain / Security

- External access via signed/tokenised link (no login) — see [[architecture/security]]
- Documents from DMS / Media Library

## Related

- [[domains/crm/deals]]
- [[frontend/_index]]
