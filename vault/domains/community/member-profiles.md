---
type: module
domain: Community & Social
panel: community
module-key: community.profiles
status: planned
color: "#4ADE80"
---

# Member Profiles

> Public community member profiles showing bio, skills, activity history, badges earned, and current membership tier.

**Panel:** `community`
**Module key:** `community.profiles`

---

## What It Does

Member Profiles gives every community member a public-facing profile page that serves as their community identity. Members fill in a bio, list skills and areas of expertise, and choose a display name and avatar. The profile automatically aggregates their community activity — threads started, replies posted, events attended, badges earned, and current membership tier. Members can discover and follow each other, and the profile acts as the landing page for reputation within the community.

---

## Features

### Core
- Profile setup: display name, avatar, bio, location, website, and area of expertise
- Skills and interests: free-text tags that members add themselves
- Activity feed: recent threads, replies, events attended, and badges earned
- Badges display: all earned badges visible on profile with earn date
- Membership tier badge: current tier displayed prominently
- Follow/following: members can follow others and see a feed of their activity

### Advanced
- Profile privacy controls: members control which sections are visible to other community members
- Social links: link external profiles (LinkedIn, Twitter/X, GitHub)
- Contributions summary: total posts, helpful replies (marked by thread author), events attended
- Profile completeness score: nudge members to complete their profile
- Featured member spotlight: admins can feature a member on the community homepage

### AI-Powered
- Profile completion suggestions: AI suggests what to add based on missing fields and peer profiles
- Member matching: surface "members you might know" based on shared skills and interests
- Expertise routing: when a question is posted in forums, AI suggests relevant expert members to tag

---

## Data Model

```erDiagram
    member_profiles {
        ulid id PK
        ulid company_id FK
        ulid user_id FK
        string display_name
        string avatar_url
        text bio
        string location
        string website_url
        json skills
        json social_links
        boolean is_public
        integer post_count
        integer helpful_count
        timestamps created_at_updated_at
    }

    member_follows {
        ulid id PK
        ulid follower_id FK
        ulid following_id FK
        timestamp created_at
    }

    member_profiles ||--o{ member_follows : "has"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `member_profiles` | Profile data | `id`, `company_id`, `user_id`, `display_name`, `bio`, `skills`, `is_public` |
| `member_follows` | Follow relationships | `id`, `follower_id`, `following_id` |

---

## Permissions

```
community.profiles.view
community.profiles.edit-own
community.profiles.feature-member
community.profiles.view-private
community.profiles.manage
```

---

## Filament

- **Resource:** `App\Filament\Community\Resources\MemberProfileResource`
- **Pages:** `ListMemberProfiles`, `ViewMemberProfile`
- **Custom pages:** `MemberProfilePage` (member self-edit), `MemberDirectoryPage`
- **Widgets:** `NewMembersWidget`, `TopContributorsWidget`
- **Nav group:** Members

---

## Displaces

| Feature | FlowFlex | Circle.so | Discourse | Mighty Networks |
|---|---|---|---|---|
| Rich member profiles | Yes | Yes | Partial | Yes |
| Skills and expertise listing | Yes | No | No | No |
| Follow network | Yes | Yes | No | Yes |
| LMS activity on profile | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[badges]] — badges displayed on profile
- [[tiers]] — current tier shown on profile
- [[forums]] — post and reply counts from forum activity
- [[events-calendar]] — attended events shown on profile
