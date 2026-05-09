---
tags: [flowflex, domain/community, overview, phase/7]
domain: Community & Social
panel: community
color: "#E11D48"
status: planned
last_updated: 2026-05-08
---

# Community & Social Overview

Run a branded customer or employee community directly inside FlowFlex. No Circle.so subscription, no Discord server to manage — your community lives where your business lives.

**Filament Panel:** `community`
**Domain Colour:** Rose `#E11D48` / Light: `#FFE4E6`
**Domain Icon:** `user-group` (Heroicons)
**Phase:** 7

## Why This Domain Exists

By 2026, communities are a primary growth and retention channel for SaaS and service businesses:
- Customer communities reduce churn by 26% (Salesforce research)
- Communities drive organic product adoption without support team overhead
- Employee communities replace Workplace by Meta for internal engagement

FlowFlex customers can run:
- **Customer community** — help customers connect, share tips, give feedback
- **Employee intranet community** — internal engagement, beyond just announcements
- **Partner/reseller community** — exclusive space for channel partners
- **Student/learner community** — links to LMS, peer discussion

## Modules in This Domain

| Module | Phase | Status | Description |
|---|---|---|---|
| [[Discussion Forums & Channels]] | 7 | planned | Topic-based discussion boards |
| [[Member Directory & Profiles]] | 7 | planned | Searchable member profiles, expertise tags |
| [[Events & Meetups]] | 7 | planned | Virtual and in-person events, RSVP |
| [[Gamification & Reputation]] | 7 | planned | Points, badges, leaderboards |
| [[Content Gating & Membership Tiers]] | 7 | planned | Access control, paid tiers, invite-only spaces |
| [[Community Analytics]] | 7 | planned | Engagement metrics, health score, growth trends |

## Competitive Position

| Competitor | Weakness | FlowFlex Advantage |
|---|---|---|
| Circle.so | Separate platform, no business data | Community members ARE your CRM contacts, employees, customers |
| Discord | No business structure, no analytics | Branded, governed, integrated with your workflow |
| Slack (for community) | Not designed for community, no gating | Proper community features + moderation |
| Tribe/Bettermode | Standalone tool | Integrated — forum reply can auto-create support ticket |
| Mighty Networks | Focus on creators/courses | Full business platform integration |

## Key Events from This Domain

| Event | Source | Consumed By |
|---|---|---|
| `MemberJoinedCommunity` | Member Directory | CRM (update contact), LMS (welcome), Notifications |
| `PostCreated` | Discussion Forums | AI (moderation check), Analytics |
| `BadgeAwarded` | Gamification | Notifications, LMS (certification display) |
| `EventRSVP` | Events | Calendar (Communications domain), Notifications |

## Related

- [[Discussion Forums & Channels]]
- [[Member Directory & Profiles]]
- [[Events & Meetups]]
- [[Gamification & Reputation]]
- [[Content Gating & Membership Tiers]]
- [[Internal Messaging & Chat]]
- [[LMS Overview]]
