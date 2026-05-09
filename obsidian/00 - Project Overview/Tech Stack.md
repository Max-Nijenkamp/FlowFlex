---
tags: [flowflex, tech-stack, backend, frontend, infrastructure, phase/1]
domain: Platform
status: built
last_updated: 2026-05-08
---

# Tech Stack

Complete technology stack for FlowFlex. Every architectural decision traces back here. Laravel 13 with Vue Starter Kit (Inertia) for the app, Filament 5 for domain panels.

---

## Backend

| Technology | Version | Purpose |
|---|---|---|
| **Laravel** | 13 | Primary PHP framework |
| **Filament** | 5 | Domain admin panel framework (TALL stack for panels) |
| **PostgreSQL** | 17+ | Primary relational database |
| **Redis** | 7+ | Caching, queues, sessions, real-time pub/sub |
| **Laravel Queues** | (built-in) | Background jobs, event listeners |
| **Laravel Events + Listeners** | (built-in) | Cross-module event bus |
| **Laravel Sanctum** | (built-in) | API token authentication |
| **Spatie Laravel Permission** | Latest | RBAC (roles and permissions, team-scoped per company) |
| **Spatie Laravel Multitenancy** | Latest | Multi-tenant workspace isolation |
| **Spatie Laravel Activity Log** | Latest | Immutable audit trail across all modules |
| **Spatie Laravel Data** | Latest | Typed DTOs for input, output, and service contracts |
| **Spatie TypeScript Transformer** | Latest | Auto-generate TypeScript types from Data classes |

---

## Frontend: Application (Vue + Inertia)

Used for **everything except** Filament panels: marketing site, all tenant app pages, client portal, community, public booking, learner portal, public checkout, public org chart.

| Technology | Version | Purpose |
|---|---|---|
| **Vue** | 3 | UI framework |
| **Inertia.js** | Latest | SPA-style navigation without a separate API |
| **TypeScript** | 5+ | Type safety across frontend codebase |
| **Vite** | Latest | Build tool, HMR in development |
| **Tailwind CSS** | 4 | Utility-first styling (CSS-first config format) |
| **@inertiajs/vue3** | Latest | Vue adapter for Inertia |

### Laravel Vue Starter Kit

The official Laravel Vue starter kit is the base. It provides:
- Inertia.js wired up with Vue 3 + TypeScript
- Authentication pages (login, register, password reset)
- Vite configuration
- Ziggy (route() helper in Vue)
- Base layout components

### Vue Conventions

```
resources/
  js/
    app.ts                     ← Inertia setup + global plugins
    ssr.ts                     ← SSR entry (if enabled)
    bootstrap.ts               ← Axios config, CSRF
    types/
      generated.d.ts           ← auto-generated from Spatie Data DTOs
      index.d.ts               ← manual type declarations
    composables/               ← shared Vue composables
    components/
      ui/                      ← base UI components (Button, Input, Modal, etc.)
      shared/                  ← app-specific shared components
    layouts/
      AppLayout.vue            ← main authenticated layout (sidebar + topbar)
      GuestLayout.vue          ← public/auth pages
      MinimalLayout.vue        ← error pages, full-screen views
    pages/
      Auth/                    ← login, register, password
      Dashboard/
      HR/
        Employees/
          Index.vue
          Show.vue
          Create.vue
        Leave/
        ...
      Finance/
      Projects/
      CRM/
      ...
      Errors/
        Error.vue              ← 404, 500, 403, 429 etc.
```

---

## Frontend: Domain Panels (Filament 5 — TALL Stack)

Used for all domain admin panels (HR, Finance, CRM, etc.), the workspace panel (company settings), and the super-admin panel.

| Technology | Purpose |
|---|---|
| **Filament** 5 | Panel framework with tables, forms, widgets, pages |
| **Livewire** 3 | Reactive PHP components within panels |
| **Alpine.js** | Lightweight JS interactivity within Filament |
| **Tailwind CSS** 4 | Same styling system as the Vue app |

### When to Use Filament vs Inertia/Vue

| Use Filament | Use Inertia + Vue |
|---|---|
| Admin/ops tools: HR, Finance, CRM | Public-facing pages |
| Filament-native tables, forms, filters | Client portal |
| CRUD-heavy internal tools | Community, learner portal |
| Reporting widgets, dashboards (internal) | Booking pages |
| Workspace settings, billing | External checkout |
| Super-admin | Public marketing pages |

---

## Infrastructure & Services

| Service | Purpose |
|---|---|
| **Laravel Horizon** | Queue monitoring dashboard |
| **Laravel Telescope** | Local debugging |
| **Stripe** | Subscription billing, module metering, usage-based charges |
| **Twilio** | SMS notifications, WhatsApp (via Twilio API) |
| **Resend** | Transactional email (primary) |
| **AWS S3 / Cloudflare R2** | File storage (documents, assets, media) |
| **Pusher / Soketi** | Real-time WebSocket (chat, notifications, live updates) |
| **Meilisearch** | Full-text search (replaces Laravel Scout default) |
| **pgvector** (PostgreSQL extension) | Vector embeddings for AI semantic search |

### AI Infrastructure (Phase 6)

| Service | Purpose |
|---|---|
| **OpenAI API** | Default LLM (GPT-4o, GPT-4o-mini) |
| **Anthropic API** | Claude fallback / alternative |
| **Ollama** | Local LLM hosting option (on-premise deployments) |

---

## Architecture Pattern

**Modular Monolith** — single Laravel application, fully isolated modules.

- Each module: `Contracts/`, `Services/`, `DTOs/`, `Models/`, `Filament/`, `Events/`, `Listeners/`, `Policies/`, `Providers/`, `database/`, `routes/`
- Modules communicate **only** via Laravel Events or registered service contracts
- The monolith can split into microservices per module when scale demands, without rewriting

See [[Architecture]] for the full pattern including Interface → ServiceProvider → Controller flow.

---

## Database Strategy

- **PostgreSQL 17** — primary relational database
- **Row-level tenancy** via `company_id` on every module table (BelongsToCompany trait)
- **Redis** — caching (query cache, permission cache), queues, session store, presence channels
- **pgvector** — vector search for AI features (semantic search, recommendations)
- **Meilisearch** — full-text search across all modules (employees, tasks, documents, contacts, etc.)

---

## Environment Variables

```dotenv
APP_NAME=FlowFlex
APP_URL=https://app.flowflex.com

# Laravel 13 starter kit settings
VITE_APP_NAME="${APP_NAME}"

# Multi-tenancy
CENTRAL_DOMAIN=flowflex.com

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=flowflex

# Redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Queue
QUEUE_CONNECTION=redis

# Stripe
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=

# Storage
FILESYSTEM_DISK=s3
AWS_BUCKET=
AWS_DEFAULT_REGION=eu-west-1

# Mail
MAIL_MAILER=resend
RESEND_API_KEY=

# SMS
TWILIO_SID=
TWILIO_AUTH_TOKEN=
TWILIO_FROM=

# Real-time
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"

# Search
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://meilisearch:7700
MEILISEARCH_KEY=

# AI
OPENAI_API_KEY=
ANTHROPIC_API_KEY=
```

---

## Development Setup

```bash
# Install dependencies
composer install
npm install

# Environment
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate --seed

# Build assets (Vite)
npm run dev        # development with HMR
npm run build      # production build

# Start servers
php artisan serve                                          # Laravel
php artisan queue:work --queue=high,default,low            # Queue worker
php artisan horizon                                        # Queue monitoring

# Search indexing
php artisan scout:import

# Type generation (after changing Data classes)
php artisan typescript:transform
```

---

## Related

- [[Architecture]]
- [[Multi-Tenancy]]
- [[Filament Implementation]]
- [[Security Rules]]
- [[Performance Rules]]
- [[Error Handling]]
- [[Rate Limiting]]
