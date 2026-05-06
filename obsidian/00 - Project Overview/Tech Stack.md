---
tags: [flowflex, tech-stack, backend, frontend, infrastructure, phase/1]
domain: Platform
status: built
last_updated: 2026-05-06
---

# Tech Stack

Complete technology stack for the FlowFlex platform. Every architectural decision must trace back to these choices.

## Backend

| Technology | Version | Purpose |
|---|---|---|
| **Laravel** | 12 | Primary PHP framework |
| **Filament** | 5 | Admin panel framework (TALL stack) |
| **PostgreSQL** | Latest stable | Primary relational database |
| **Redis** | Latest stable | Caching, queues, sessions, real-time pub/sub |
| **Laravel Queues** | (built-in) | Background jobs |
| **Laravel Events + Listeners** | (built-in) | Cross-module event bus |
| **Laravel Sanctum** | (built-in) | API token authentication |
| **Spatie Laravel Permission** | Latest | RBAC (roles and permissions) |
| **Spatie Laravel Multitenancy** | Latest | Multi-tenant workspace isolation |
| **Spatie Laravel Activity Log** | Latest | Immutable audit trail across all modules |

### TALL Stack

Filament uses the TALL stack:
- **T** — Tailwind CSS
- **A** — Alpine.js
- **L** — Livewire
- **L** — Laravel

## Frontend

| Technology | Purpose |
|---|---|
| **Livewire 3** | Reactive components within Filament panels |
| **Alpine.js** | Lightweight interactivity |
| **Tailwind CSS** | Utility-first styling |
| **Filament Panels** | Separate panel per domain (admin, HR, finance, etc.) |

## Infrastructure & Services

| Service | Purpose |
|---|---|
| **Laravel Horizon** | Queue monitoring |
| **Laravel Telescope** | Local debugging |
| **Stripe** | Subscription billing, module metering, usage-based charges |
| **Twilio** | SMS notifications |
| **Resend / Mailgun** | Transactional email |
| **AWS S3 / Cloudflare R2** | File storage (documents, assets, media) |
| **Pusher / Soketi** | Real-time WebSocket events (notifications, live updates) |

## Architecture Pattern

**Modular Monolith** — single Laravel application, but modules are fully isolated internally.

- Each module has its own: `Models/`, `Filament/`, `Services/`, `Listeners/`, `Policies/`, `database/migrations/`
- Modules communicate only via **Events** — never by directly calling another module's internal classes
- The monolith can later be split into microservices per module when scale demands it, without rewriting

## Database Strategy

- **PostgreSQL** — primary relational database
- **Row-level tenancy** via `tenant_id` on every module table (alternatively one schema per tenant)
- **Redis** — caching, queues, session store

## Environment Setup

```bash
# Clone and install
composer install
npm install

# Environment
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate --seed

# Build assets
npm run dev

# Queue worker (required for background jobs)
php artisan queue:work --queue=high,default,low

# Start development server
php artisan serve
```

## Key .env Variables

```dotenv
APP_NAME=FlowFlex
APP_URL=https://app.flowflex.com

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

# Stripe
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=

# Storage
FILESYSTEM_DISK=s3
AWS_BUCKET=
AWS_DEFAULT_REGION=

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
```

## Related

- [[Architecture]]
- [[Multi-Tenancy]]
- [[Filament Implementation]]
- [[Panel Map]]
- [[Security Rules]]
- [[Performance Rules]]
