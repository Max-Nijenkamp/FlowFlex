---
domain: core
module: invitation-system
type: api
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Invitation System — API (DTOs)

Parent: [[_module]] · See also [[architecture]]

Fires no events, consumes no events. Cross-module surface is the four actions (see [[architecture]]) and two DTOs.

## DTOs

### CreateInvitationData (input)

| Field | Type | Validation |
|---|---|---|
| email | string | required, email, not already a user in this company, no pending invite in this company |
| role | string | required, exists for this company team, not `owner` *(assumed: owners transferred, not invited)* |

Message: "This email already has a pending invitation."

### AcceptInvitationData (input — public route)

| Field | Type | Validation |
|---|---|---|
| token | string | required, uuid, valid (unexpired, unaccepted, unrevoked) |
| name | string | required, max:200 |
| password | string | required, `Password::defaults()` (12+ chars, uncompromised) |

Consumed by `SetupWizardPage` step 3 via `CreateInvitationData` — see [[../setup-wizard/_module]].
