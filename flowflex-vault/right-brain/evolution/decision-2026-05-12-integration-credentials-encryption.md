---
type: adr
date: 2026-05-12
status: decided
color: "#F97316"
domain: AI & Automation
module: integration-hub
---

# ADR: Integration credentials stored with `encrypted:array` cast

## Context

The `integrations` table stores third-party API credentials (OAuth tokens, API keys, client secrets) for providers such as Stripe, HubSpot, Slack, GitHub, Salesforce, Xero, etc. These are high-value secrets — a credential leak could allow an attacker to impersonate the tenant against the third party.

Two options were considered for the `credentials` column:

## Options Considered

### Option A — `json` cast (plain-text JSON in DB)
- Simple, readable in pg admin
- No overhead
- Vulnerable to: DB dump exposure, backup leaks, rogue DBA access, PostgreSQL log exposure

### Option B — `encrypted:array` cast (application-layer encryption)
- Laravel `Crypt::encrypt()` wraps the JSON value using APP_KEY (AES-256-CBC)
- Encrypted at application layer before hitting the DB wire
- DB dumps contain ciphertext — useless without APP_KEY
- Transparent to application code: `$integration->credentials['api_key']` just works
- Small overhead (~1ms per read/write — acceptable for infrequent credential access)
- Requires APP_KEY rotation protocol if key is compromised

## Decision

**Option B — `encrypted:array`** was chosen.

The security benefit significantly outweighs the overhead. Credential leaks via DB backup or rogue DB access are an industry-standard threat for SaaS platforms that store OAuth tokens. This pattern is also consistent with Laravel's recommended approach for sensitive fields.

## Consequences

- `APP_KEY` must be treated as a Tier-1 secret (already true per Laravel convention)
- `APP_KEY` rotation requires re-encryption of all `credentials` rows (one-time migration script needed)
- Backups of `credentials` data are useless without `APP_KEY` — this is the desired property
- Developers must never log `$integration->credentials` (add to dev guidelines)
- Future integration domains (e.g. CRM OAuth, Finance bank connections) should adopt the same pattern

## Related

- `app/Models/AI/Integration.php` — `'credentials' => 'encrypted:array'` cast
- [[builder-log-ai-phase6]]
- [[MOC_AI]]
