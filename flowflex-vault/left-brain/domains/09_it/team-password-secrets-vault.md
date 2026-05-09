---
type: module
domain: IT & Security
panel: it
cssclasses: domain-it
phase: 4
status: planned
migration_range: 500000–549999
last_updated: 2026-05-09
---

# Team Password & Secrets Vault

Shared credential management, API key storage, secret rotation, and access-controlled vault for teams. Replaces 1Password Teams and Bitwarden Business.

---

## Features

### Credential Storage
- AES-256 encrypted vault (zero-knowledge architecture)
- Store: passwords, API keys, SSH keys, licence keys, secure notes, credit cards
- Organisation: folders, tags, favourites
- Secure sharing: share specific items with specific users or groups
- Auto-generated strong passwords (configurable length + rules)

### Access Control
- RBAC per vault item (owner, edit, view, one-time view)
- Collections per team (Marketing team can see Marketing credentials)
- Emergency access (nominated person gets access after X-hour delay)
- Read-only audit-mode for compliance officers

### Browser & App Integration
- Browser extension (Chrome, Firefox, Safari, Edge)
- Autofill on known URLs
- iOS/Android mobile app
- CLI tool for developer API keys

### Secrets Management (Developer-Focused)
- Environment variable secrets (`.env` values stored and shared via CLI)
- API key rotation reminders
- Expiry alerts for API keys, SSL certs, service accounts
- Inject secrets into CI/CD pipeline (GitHub Actions integration)

### Security & Compliance
- Zero-knowledge encryption (FlowFlex cannot read stored secrets)
- MFA required for vault access
- Activity log: who accessed what, when
- Breach monitoring (HIBP integration for password breach detection)
- Orphaned credentials detection (credentials for departed employees)
- Passkey (FIDO2) vault unlock support

---

## Data Model

```erDiagram
    vault_items {
        ulid id PK
        ulid company_id FK
        string type
        string name
        text encrypted_data
        ulid created_by FK
        timestamp expires_at
    }

    vault_access_grants {
        ulid id PK
        ulid item_id FK
        ulid grantee_id FK
        string permission_level
        timestamp granted_at
        timestamp revoked_at
    }

    vault_access_log {
        ulid id PK
        ulid item_id FK
        ulid accessed_by FK
        string action
        timestamp accessed_at
        string ip_address
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `SecretExpiring` | 30 days before expiry | Notifications (item owner) |
| `BreachDetected` | HIBP match on stored password | Notifications (item owner + IT admin) |
| `VaultAccessByDepartedEmployee` | Offboarded user accesses vault | Notifications (IT admin — security alert) |

---

## Permissions

```
it.vault.view-own
it.vault.manage-own
it.vault.admin
it.vault.audit
```

---

## Competitors Displaced

1Password Teams · Bitwarden Business · Keeper Business · LastPass Teams · HashiCorp Vault (secrets mgmt)

---

## Related

- [[MOC_IT]]
- [[auth-rbac]]
- [[MOC_HR]] — offboarding triggers vault access revocation
