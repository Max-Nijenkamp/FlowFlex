---
domain: core
module: rbac
feature: ownership
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Company Ownership — single owner, transferable

## Behaviour

- A company has **exactly one owner** at all times — not "at least one", exactly one.
- Canonical owner = the sole user holding the `owner` role, denormalised to `companies.owner_user_id`
  *(assumed)*.
- **Transfer** is the only way to change owner: `TransferOwnershipAction` atomically assigns `owner` to the
  new user and demotes the previous owner (to `admin` by default). No window with zero or two owners.
- The `owner` role holds every permission for the company (and is the only role exempt from the
  module-scoped-permission bound — see [[module-scoped-permissions]]).
- Owner cannot delete their own account or leave the company without transferring first.
- Enforced server-side (`AssignRolesAction` refuses a second `owner`); the last/only owner cannot be demoted
  except via transfer ([[last-owner-guard]]).

## UI

- **Kind**: custom-page (a small "Ownership" panel inside RBAC / company settings), plus a guarded action.
- **Page**: "Transfer ownership" modal on the Users/Roles screen (`/app/roles` or company settings).
- **Layout**: current owner card + "Transfer ownership" button → confirm modal (select member, type company
  name to confirm — destructive-action pattern).
- **Key interactions**: pick new owner → double-confirm → atomic transfer → toast + re-render; previous owner
  now `admin`.
- **States**: default (shows current owner) · confirming (typed confirmation required) · error (cannot
  transfer to a non-member / unverified user) · success.
- **Gating**: visible only to the current `owner`. No other role can transfer.

## Data

- Writes: Spatie role assignments + `companies.owner_user_id` (platform pointer via core service). Owns no
  new tables. Never touches other domains ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing. Feeds: an `OwnershipTransferred` event *(assumed)* → audit log + notifications.

## Test Checklist

### Unit
- [ ] Exactly-one-owner invariant: a second `owner` assignment is rejected; canonical owner = holder of `owner` role

### Feature (Pest)
- [ ] `TransferOwnershipAction` promotes the new owner and demotes the previous one atomically — never zero or two owners
- [ ] Concurrent transfers on the same company serialize under lock; final state has exactly one owner
- [ ] Transfer to a non-member / unverified user is rejected
- [ ] Owner cannot delete their account or leave without transferring first

### Livewire
- [ ] Transfer-ownership modal requires typed company-name confirmation; visible only to the current owner
- [ ] Non-owner cannot see or invoke the transfer action

## Unknowns

> [!warning] UNVERIFIED — `companies.owner_user_id` denormalisation, default demotion target (`admin`), and
> the `OwnershipTransferred` event are proposed, not from a prior spec.

## Related

- [[../_module|RBAC]] · [[module-scoped-permissions]] · [[last-owner-guard]] · [[../../../../decisions/decision-2026-06-20-full-mapping-conventions]]
