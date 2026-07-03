---
domain: projects
module: projects
feature: project-membership
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Membership & Visibility

Project members and their roles; membership gates who can see a project.

## Behaviour

- Roles: `owner` / `member` / `viewer`. Creator is auto-owner.
- Listing is member-scoped: a user sees only projects they belong to, unless they hold `projects.projects.view-any`.
- Add/remove members and change roles requires `projects.projects.manage-members`.

## UI

- **Kind**: simple-resource (relation manager on the project detail page).
- **Page**: "Members" tab / relation manager under `ProjectResource` detail.
- **Layout**: user + role columns; add-member action (user picker + role select).
- **Key interactions**: add member → optimistic row; change role → inline select; remove → confirm.
- **States**: empty (only owner listed) · loading · error (toast) · selected (row).
- **Gating**: view with project access; mutate with `projects.projects.manage-members`.

## Data

- Owns / writes: `proj_project_members` only.
- Reads: `users` in the company (member picker).
- Cross-domain writes: none.

## Relations

- Consumes / Feeds: nothing (no events).
- Shared entity: `users`.

## Test Checklist

### Unit
- [ ] Role assignment restricted to `owner` / `member` / `viewer`; creator resolves to `owner`.
- [ ] Unique `(project_id, user_id)` rule prevents duplicate membership rows.

### Feature (Pest)
- [ ] Member-scoped listing: a non-member without `projects.projects.view-any` cannot see or open the project.
- [ ] Add/remove member and role change require `projects.projects.manage-members`.
- [ ] Tenant isolation: company A cannot add/list company B's project members.

### Livewire
- [ ] Members relation manager hides its add/remove/role actions without `projects.projects.manage-members`.

## Unknowns

- Whether `viewer` role is exposed in v1 or deferred *(assumed present)*. See [[../unknowns]].

## Related

- [[../_module|Projects]] · [[project-record|Project Record]] · [[../security]]
