---
type: moc
domain: Community & Social
panel: community
cssclasses: domain-community
phase: 7
color: "#F59E0B"
last_updated: 2026-05-08
---

# Community & Social — Map of Content

Branded customer or employee community. Discussion forums, member directory, events, gamification, and content gating.

**Panel:** `community`  
**Phase:** 7  
**Migration Range:** `800000–849999`  
**Colour:** Amber-400 `#F59E0B` / Light: `#FFFBEB`  
**Icon:** `heroicon-o-user-group`

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| [[discussion-forums\|Discussion Forums]] | 7 | planned | Topic-based boards, threaded replies, reactions, solved answers |
| [[member-profiles-reputation\|Member Profiles & Reputation]] | 7 | planned | Profiles, trust levels, badges, reputation score |
| [[community-events\|Community Events]] | 7 | planned | AMAs, office hours, meetups, pre-event Q&A voting |
| [[gamification-points\|Gamification & Points]] | 7 | planned | Points engine, levels, leaderboards, challenges, rewards |
| [[moderation-tools\|Moderation Tools]] | 7 | planned | Flagging, spam detection, warnings, bans, appeals |
| Content Gating & Membership Tiers | 7 | planned | Access control, paid tiers via Stripe, free/premium |
| Community Analytics | 7 | planned | Engagement metrics, top contributors, growth trends |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `MemberJoined` | Community | Notifications (welcome), Gamification (first badge) |
| `PostPublished` | Forums | Notifications (subscribers), Gamification (points) |
| `EventRegistered` | Events | Notifications (confirmation), CRM (if external member) |
| `MembershipUpgraded` | Membership Tiers | Finance (record payment), Notifications |
| `BadgeEarned` | Gamification | Notifications |

---

## Public Frontend

Community pages are Vue+Inertia (public ring-fenced or members-only).  
See [[public-pages#community]].

---

## Permissions Prefix

`community.forums.*` · `community.members.*` · `community.events.*`  
`community.gamification.*` · `community.tiers.*`

---

## Competitors Displaced

Circle.so · Discord · Mighty Networks · Discourse · Bettermode · Hivebrite

---

## Related

- [[MOC_Domains]]
- [[MOC_CRM]] — community members → CRM contacts
- [[MOC_Marketing]] — community events → marketing events
- [[MOC_LMS]] — community discussions linked to courses
- [[MOC_Frontend]] — community pages = public Vue+Inertia
