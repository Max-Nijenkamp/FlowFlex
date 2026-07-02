---
domain: marketing
module: marketing-analytics
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Marketing Analytics — Security

Parent: [[_module]]

A read-only aggregation surface — its security posture is about scope, not writes.

## Read-only boundary

Owns and writes nothing. It **reads** other marketing modules' tables via their read models under `CompanyContext`; the aggregates never leave the company scope ([[../../../security/tenancy-isolation]], [[../../../security/data-ownership]]).

## Permissions

`marketing.analytics.view`. The dashboard + all widgets gate on `canAccess()`. Soft-dep sections additionally require the source module active (else hidden).

## Export throttle (medium)

The CSV export action carries a rate limiter (throttle) — large aggregate exports are a DoS/exfil vector ([[../../../architecture/security]]).

## Related

- [[_module]] · [[api]] · [[../../../security/data-ownership]] · [[../../../architecture/security]]
