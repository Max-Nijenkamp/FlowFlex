# /flowflex:patterns

Fetch a specific architecture pattern from the vault. Read-only.

## Usage

```
/flowflex:patterns states
/flowflex:patterns encryption
/flowflex:patterns testing
/flowflex:patterns caching
/flowflex:patterns events
/flowflex:patterns security
/flowflex:patterns pdf
/flowflex:patterns search
/flowflex:patterns queues
/flowflex:patterns email
/flowflex:patterns performance
/flowflex:patterns websockets
/flowflex:patterns api
/flowflex:patterns errors
/flowflex:patterns seeding
/flowflex:patterns policy
```

## Arguments

- First arg: concern keyword (see table below)

## Pattern Lookup Table

| Keyword | File to read |
|---|---|
| `states` | `vault/architecture/patterns/states.md` |
| `encryption` | `vault/architecture/patterns/encryption.md` |
| `testing` | `vault/architecture/patterns/testing-pattern.md` |
| `service` or `interface` | `vault/architecture/patterns/interface-service.md` |
| `actions` | `vault/architecture/patterns/actions-pattern.md` |
| `dto` | `vault/architecture/patterns/dto-pattern.md` |
| `model` or `traits` or `belongs-to-company` | `vault/architecture/patterns/belongs-to-company.md` |
| `custom-pages` or `custom` or `pages` | `vault/architecture/patterns/custom-pages.md` |
| `policy` or `permissions` or `authorization` | `vault/architecture/patterns/policy.md` |
| `seeding` or `seeders` | `vault/architecture/patterns/seeders.md` |
| `caching` or `cache` or `redis` | `vault/architecture/caching.md` |
| `events` or `event-bus` | `vault/architecture/event-bus.md` |
| `security` or `rate-limiting` or `csrf` | `vault/architecture/security.md` |
| `search` or `meilisearch` | `vault/architecture/search.md` |
| `queues` or `horizon` or `jobs` | `vault/architecture/queue-jobs.md` |
| `email` or `mail` | `vault/architecture/email.md` |
| `pdf` | Read `spatie/laravel-pdf` section in `vault/architecture/packages.md` |
| `money` or `currency` | Read `brick/money` section in `vault/architecture/packages.md` |
| `performance` or `n+1` | `vault/architecture/performance.md` |
| `websockets` or `realtime` or `reverb` | `vault/architecture/websockets.md` |
| `api` | `vault/architecture/api-design.md` |
| `errors` or `exceptions` | `vault/architecture/error-handling.md` |
| `tenancy` or `multitenancy` | `vault/architecture/multi-tenancy.md` |
| `modules` or `billing-service` | `vault/architecture/module-system.md` |
| `deployment` or `env` | `vault/architecture/deployment.md` |
| `ci` or `ci-cd` or `pipeline` | `vault/architecture/ci-cd.md` |
| `packages` | `vault/architecture/packages.md` |
| `filament` | `vault/architecture/filament-patterns.md` |
| `panels` or `colors` | `vault/architecture/domain-panels.md` |
| `tech-stack` or `stack` | `vault/architecture/tech-stack.md` |
| `ui` or `ui-strategy` | `vault/architecture/ui-strategy.md` |
| `dod` or `workflow` or `way-of-working` | `vault/architecture/way-of-working.md` |
| `roadmap` or `milestones` | `vault/build/ROADMAP.md` |
| `custom-fields` or `schemaless` | `vault/architecture/patterns/custom-fields.md` |
| `gdpr` or `retention` or `data-lifecycle` | `vault/architecture/data-lifecycle.md` |
| `dev` or `local-dev` or `troubleshooting` | `vault/architecture/local-dev.md` |
| `ux-states` or `empty-states` or `wizard` | `vault/architecture/patterns/ux-states.md` |
| `brand` or `switchboard` | `vault/product/brand.md` |
| `design` or `design-system` or `components` | `vault/frontend/design-system.md` |

## What This Does

1. Match the keyword to a file using the table above
2. Read the full file
3. Display the content

If keyword doesn't match any entry, list available keywords and ask the user to choose.
