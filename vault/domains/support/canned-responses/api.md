---
domain: support
module: canned-responses
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Canned Responses — DTOs & API

## DTOs

### CreateCannedResponseData (input)

| Field | Type | Validation |
|---|---|---|
| title | string | required |
| shortcut | string | required, regex `[a-z0-9-]+`, unique per company |
| body | string | required, purified |
| category | ?string | nullable |
| is_shared | bool | personal (owner-only) vs team-wide |

---

## Public / Portal Endpoints

None. Canned responses are inserted only inside authenticated agent composers (ticket reply, chat) via `RenderCannedResponseAction`.
