---
tags: [flowflex, setup, environment, dev, commands]
domain: Platform
status: built
last_updated: 2026-05-06
---

# Environment Setup

Quick reference for getting a FlowFlex dev environment running from scratch.

---

## Prerequisites

- PHP 8.3+
- Composer 2+
- Node.js 20+ / npm
- PostgreSQL 16+
- Redis 7+

---

## Initial Setup

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
```

---

## Running the App

```bash
# Web server
php artisan serve

# Queue worker (required for background jobs, payroll, reports, etc.)
php artisan queue:work --queue=high,default,low

# Queue monitoring (Horizon)
php artisan horizon

# Local debugging (Telescope)
# Available at /telescope in local env
```

---

## Key `.env` Variables

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

---

## Useful Artisan Commands

```bash
# Clear all caches
php artisan optimize:clear

# Run tests
php artisan test

# Create new module migration
php artisan make:migration create_employees_table --path=Modules/HR/database/migrations

# Create Filament resource
php artisan make:filament-resource Employee --panel=hr

# Reseed test data
php artisan db:seed --class=Database\\Seeders\\TestDataSeeder

# List all registered routes
php artisan route:list --path=api/v1

# Check queue status
php artisan queue:monitor
```

---

## Multi-Tenancy Notes

- Central domain: `flowflex.com` — resolves to central DB (admin panel, workspace registration)
- Tenant domains: `{slug}.flowflex.com` — all module panels
- Tenant context set automatically via `spatie/laravel-multitenancy` middleware
- Run tenant migrations: `php artisan tenants:artisan "migrate"`

---

## Module Development Quick Start

1. Create `Modules/{Name}/Providers/{Name}ServiceProvider.php`
2. Register in `config/app.php`
3. Create migrations under `Modules/{Name}/database/migrations/`
4. Run `php artisan migrate`
5. Create Filament resources: `php artisan make:filament-resource {Model} --panel={panel}`

See [[Module Development Checklist]] for full step-by-step.

---

## Related

- [[Architecture]]
- [[Tech Stack]]
- [[Module Development Checklist]]
- [[Multi-Tenancy]]
