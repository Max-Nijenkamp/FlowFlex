---
domain: infrastructure
type: infrastructure
build-status: planned
status: unverified
color: "#F97316"
updated: 2026-06-20
---

# Cache / Session — Redis 8

One `redis:8-alpine` container backs **cache, queue, and session**. phpredis client,
password-protected (`--requirepass secret`), unpublished to the host (reach as `redis:6379`).

| Concern | Value (docker runtime) |
|---|---|
| `REDIS_CLIENT` | `phpredis` |
| `REDIS_HOST` / `PORT` | `redis` / `6379` |
| `REDIS_PASSWORD` | `secret` |
| `CACHE_STORE` | `redis` |
| `SESSION_DRIVER` | `redis` |

> [!warning] Audit correction
> `architecture/local-dev.md` showed `REDIS_PASSWORD=null` and host-published `:6379`. The real
> stack requires auth and publishes **no** host port (a host Redis already owns 6379). See AUDIT E4/E6.

Queue use of Redis is documented in [[queue-horizon]]. Cache conventions (per-company keys such as
`company:{id}:modules`, forget-on-write) live with the modules that own them — e.g.
[[../domains/core/module-marketplace/_module]]. Cross-cutting cache strategy: [[../architecture/caching]].

> [!note] `architecture/caching.md` has a code snippet using the PHP 8.5 pipe operator `|>` that
> won't parse on the 8.3 floor — flagged for rewrite (AUDIT E8).

## Related

- [[docker-stack]] · [[queue-horizon]] · [[_moc|Infrastructure MOC]]
