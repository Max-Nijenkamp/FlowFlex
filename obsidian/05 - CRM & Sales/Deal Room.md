---
tags: [flowflex, domain/crm, deal-room, digital-sales-room, phase/6]
domain: CRM & Sales
panel: crm
color: "#2563EB"
status: planned
last_updated: 2026-05-08
---

# Deal Room (Digital Sales Room)

A shared, branded space for buyers and sellers. Stop emailing back and forth — every document, message, proposal, and next step lives in one link you send to the prospect.

**Who uses it:** Sales reps, sales managers, buyers (external)
**Filament Panel:** `crm` (admin); public URL for buyers
**Depends on:** [[Sales Pipeline]], [[Quotes & Proposals]], [[File Storage]]
**Phase:** 6
**Build complexity:** High — public-facing, 3 tables

---

## Features

### Deal Room Creation

- Create a Deal Room from any deal in the pipeline (one click)
- Branded: company logo, brand colour, rep's photo and contact info
- Unique shareable URL (no login required for buyer — optional password protection)
- Expiry date option (link expires after deal is closed or on set date)

### Content Sections

- **Welcome message** — personalised video or text from rep
- **Proposal** — embed live from [[Quotes & Proposals]] (updates auto-reflect)
- **Resources** — documents, case studies, ROI calculators, product sheets
- **About Us** — company overview, testimonials, certifications
- **Next Steps** — mutual action plan (both sides can add/check off items)
- **Q&A** — threaded questions from buyer, answered by rep
- **Stakeholder Map** — buyer adds their team members, seller adds theirs
- **Meeting Scheduler** — embedded booking link (from [[Booking & Appointment Scheduling]])

### Mutual Action Plan

- Shared checklist: "These are the steps to get to a decision"
- Both rep and buyer can add items, mark complete, set owners and due dates
- Progress bar shows % of mutual plan completed
- Timeline view of remaining steps to close

### Engagement Tracking (Seller View)

- Who viewed the Deal Room (by email or anonymous)
- Which sections they spent time on (time-per-section heatmap)
- Document download tracking
- Last active timestamp per buyer contact
- Alert when buyer visits room (especially if not visited in 7+ days)
- Share with your manager: "Buyer engagement is high — they viewed pricing 4 times"

### Notifications

- Rep notified when buyer opens room for first time
- Rep notified when buyer views pricing section
- Rep notified when buyer downloads proposal
- Rep notified when buyer adds a question
- Buyer notified when rep updates content or adds a message

---

## Database Tables (3)

### `crm_deal_rooms`
| Column | Type | Notes |
|---|---|---|
| `deal_id` | ulid FK | |
| `slug` | string unique | URL-safe |
| `password_hash` | string nullable | |
| `expires_at` | timestamp nullable | |
| `welcome_message` | text nullable | |
| `welcome_video_url` | string nullable | |
| `is_published` | boolean | |
| `view_count` | integer | |
| `last_viewed_at` | timestamp nullable | |

### `crm_deal_room_views`
| Column | Type | Notes |
|---|---|---|
| `deal_room_id` | ulid FK | |
| `viewer_email` | string nullable | if identified |
| `viewer_name` | string nullable | |
| `section_viewed` | string | |
| `time_spent_seconds` | integer | |
| `viewed_at` | timestamp | |
| `ip_address` | string nullable | |

### `crm_mutual_action_items`
| Column | Type | Notes |
|---|---|---|
| `deal_room_id` | ulid FK | |
| `title` | string | |
| `owner_side` | enum | `seller`, `buyer` |
| `owner_name` | string nullable | |
| `due_date` | date nullable | |
| `is_complete` | boolean | |
| `completed_at` | timestamp nullable | |
| `sort_order` | integer | |

---

## Permissions

```
crm.deal-rooms.view
crm.deal-rooms.create
crm.deal-rooms.edit
crm.deal-rooms.delete
crm.deal-rooms.view-analytics
```

---

## Competitor Comparison

| Feature | FlowFlex | Dock.us | Aligned | Notion (DIY) |
|---|---|---|---|---|
| Branded deal room | ✅ | ✅ | ✅ | partial |
| Mutual action plan | ✅ | ✅ | ✅ | ❌ |
| Buyer engagement tracking | ✅ | ✅ | ✅ | ❌ |
| Live proposal embed | ✅ | ❌ | ❌ | ❌ |
| Meeting scheduler embed | ✅ | ✅ | ✅ | ❌ |
| No extra subscription | ✅ | ❌ (€29+/user/mo) | ❌ | ❌ |

---

## Related

- [[CRM Overview]]
- [[Sales Pipeline]]
- [[Quotes & Proposals]]
- [[Booking & Appointment Scheduling]]
