---
domain: core
module: workspace-hub
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Workspace Hub — Security

- **Guard**: `web` (tenant `User`) only. Admin (`admin`) users never reach the hub — they land on the
  staff console. See [[../../../security/authn-authz]].
- **Tile visibility = activation ∩ permission**: a domain tile renders only when the company has the
  domain active **and** the user holds its access permission (`access.<domain>`). No permission → no tile,
  and the domain's routes remain guarded independently (defence in depth — the hub is a launcher, not the
  gate).
- **Tenant isolation**: the activated-module + permission lookups run under the current company context
  ([[../../../security/tenancy-isolation]]) — never trust a client-supplied company/domain.
- **No enumeration**: unauthorised domains are simply absent; the hub does not reveal what exists but is
  forbidden.

## Permissions

`core.hub.view` — see the hub (granted to every tenant user by default).

## Related

- [[_module]] · [[architecture]] · [[../rbac/_module]] · [[../../../security/authn-authz]]
