---
domain: projects
module: tasks
feature: comments-mentions
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Comments & @mentions

Threaded task discussion with @mention notifications and attachments.

## Behaviour

- Threaded comments (`parent_comment_id`); body purified with HTMLPurifier.
- `@mention` in a comment notifies the mentioned user (only them) via core.notifications.
- Attachments via Media Library (MIME whitelist, size cap, tenant-scoped path).

## UI

- **Kind**: simple-resource fragment (comment thread on task detail).
- **Page**: "Comments" tab under the task detail view.
- **Layout**: chronological thread, reply-to nesting, rich-text composer with @mention autocomplete + attachment drop.
- **Key interactions**: post comment → optimistic append + broadcast; @mention picker; attach file → upload progress.
- **States**: empty (no comments → "Start the discussion") · loading · error (upload rejected → toast) · selected (reply target highlighted).
- **Gating**: `projects.tasks.comment` (view within task access).

## Data

- Owns / writes: `proj_task_comments` (+ Media Library records via core.files service).
- Reads: `users` (mention autocomplete).
- Cross-domain writes: none — notifications + files created through their owning services' APIs ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: `NotificationService::notify` (mention) → core.notifications delivers.
- Shared entity: `users`, Media Library (core.files).

## Test Checklist

### Unit
- [ ] `@mention` parser extracts exactly the mentioned users from a comment body (no false positives).
- [ ] Comment body is HTMLPurified before persistence (script/markup stripped).

### Feature (Pest)
- [ ] Posting a comment with an `@mention` notifies only the mentioned user via `NotificationService` (no broadcast to others); notification carries `company_id`.
- [ ] Tenant scope: a user cannot comment on or read comments of a task in another company/project they cannot see; commenting requires `projects.tasks.comment`.
- [ ] Attachment upload enforces the MIME whitelist, size cap, and tenant-scoped `companies/{id}/` path.

### Livewire
- [ ] Comment composer denied without `projects.tasks.comment`; hidden when `projects.tasks` inactive.
- [ ] Rejected attachment upload surfaces an error toast (no partial write).

## Unknowns

- Reactions/emoji + comment editing window undecided *(assumed simple v1)*. See [[../unknowns]].

## Related

- [[../_module|Tasks]] · [[../../../core/notifications/_module|Notifications]] · [[../../../core/file-storage/_module|File Storage]]
