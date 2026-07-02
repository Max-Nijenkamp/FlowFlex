---
domain: infrastructure
type: infrastructure
build-status: planned
status: unverified
color: "#F97316"
updated: 2026-06-20
---

# WebSockets — Laravel Reverb

Realtime via **Laravel Reverb** in its own container: `php artisan reverb:start --host=0.0.0.0
--port=8081`. **Host port unpublished** (`expose: 8081`) — publish a free `<host>:8081` when doing
browser WebSocket work. Package: `laravel/reverb`.

> [!warning] Audit correction
> `architecture/websockets.md` + `local-dev.md` show Reverb on **8080** — wrong. The container runs
> on **8081** (and 8080 is nginx). Per-domain channel tables in that note are **planned** (those
> domains are stripped). See AUDIT E5.

Channel auth + broadcasting conventions: [[../architecture/websockets]]. Today only platform events
(e.g. `NotificationCreated` for the in-app bell) can broadcast; domain channels return when rebuilt.

## Related

- [[docker-stack]] · [[queue-horizon]] · [[../architecture/websockets]] · [[_moc|Infrastructure MOC]]
