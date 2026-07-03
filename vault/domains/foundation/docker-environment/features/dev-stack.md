---
domain: foundation
module: docker-environment
feature: dev-stack
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Local Dev Stack (`docker compose up`)

One command brings up all 9 services and a reproducible dev environment; the developer touches `localhost:8080`.

## Behaviour

- `docker compose up -d` starts app · nginx · postgres · redis · meilisearch · mailpit · horizon · scheduler · reverb.
- `postgres`/`redis` healthchecks gate the `app` container start (dependency ordering).
- `migrate --seed` runs the seeder chain ([[../permissions-seed/_module|permissions-seed]]) → working demo logins.
- App reachable at `localhost:8080`; Mailpit/Reverb internal (publish a free host port for browser work).
- File `watch`/sync keeps the container in step with local edits *(paths not enumerated — see unknowns)*.

## UI

- **Kind**: background (dev infrastructure — no app screen). The only "UI" is the Mailpit inbox (`:8025`, internal) and `localhost:8080` serving the panels built by other modules.

## Data

- Owns: no tables. Provides the containerised runtime for every module's data.
- Cross-domain writes: none.

## Relations

- Consumes: nothing at the app layer. Feeds: every module (they run inside these containers).
- Shared entity: the compose stack itself → [[../../../../infrastructure/docker-stack]].

## Test Checklist

### Unit
- [ ] Compose config declares all 9 services with postgres/redis healthcheck `depends_on` gates

### Feature (Pest)
- [ ] `docker compose up -d` brings the stack healthy; `localhost:8080` serves the app
- [ ] `migrate --seed` runs clean from an empty DB (M0 exit gate)

## Unknowns

> [!warning] UNVERIFIED — exact `watch` sync paths and whether Mailpit/Reverb host ports should default to
> published. See [[../unknowns]].

## Related

- [[../_module|Docker Environment]] · [[../../../../infrastructure/docker-stack]] · [[../../../../architecture/local-dev]]
