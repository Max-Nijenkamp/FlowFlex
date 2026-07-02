---
domain: foundation
module: laravel-scaffold
type: infrastructure
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Laravel Scaffold — Install Manifest

The dependency install for a fresh project, run after `composer create-project`. This is the *intended* package set; many domain-specific packages (HR/Finance/CRM plugins) only matter once those domains are rebuilt.

```bash
# Core backend
composer require filament/filament:"^5.0"
composer require laravel/horizon laravel/pulse laravel/reverb laravel/sanctum
composer require spatie/laravel-data spatie/laravel-permission spatie/laravel-activitylog
composer require spatie/laravel-medialibrary spatie/laravel-typescript-transformer
composer require spatie/laravel-model-states spatie/laravel-settings spatie/laravel-sluggable
composer require spatie/laravel-health spatie/laravel-backup
composer require lorisleiva/laravel-actions brick/money propaganistas/laravel-phone
composer require stripe/stripe-php sentry/sentry-laravel dedoc/scramble

# Dev
composer require --dev pestphp/pest pestphp/pest-plugin-laravel pestphp/pest-plugin-livewire
composer require --dev nunomaduro/larastan laravel/pint brianium/paratest

# Frontend (public site only)
npm install vue@^3.5 @inertiajs/vue3 @vueuse/core typescript
npm install -D vite @vitejs/plugin-vue tailwindcss@^4 ziggy-js pinia vitest @playwright/test
```

> [!warning] UNVERIFIED — needs confirmation: the full plugin list
> The exact installed package set was not re-derived from the live `composer.json` lock here; treat the list above as the intended manifest. Confirmed in code: `php ^8.3`, `laravel/framework ^13.8`.

## Directory Layout

`app/Contracts/{Domain}/`, `app/Services/{Domain}/`, `app/Providers/{Domain}/`, `app/Data/{Domain}/`, `app/Actions/{Domain}/`, `app/Filament/{Admin,App,Auth}/`, `app/Support/{Services,Scopes,Traits}/`. Flat foldering — no `Core/`/`Foundation/` build-phase dirs (ADR 2026-06-11).

## Related

- [[_module|Laravel Scaffold]]
- [[../docker-environment/_module|Docker Environment]]
- [[../../../architecture/packages]]
- [[../../../architecture/tech-stack]]
