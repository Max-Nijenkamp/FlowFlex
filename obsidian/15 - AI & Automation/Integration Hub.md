---
tags: [flowflex, domain/ai-automation, integrations, api, phase/6]
domain: AI & Automation
panel: ai
color: "#06B6D4"
status: planned
last_updated: 2026-05-08
---

# Integration Hub

200+ pre-built connectors to third-party apps. Bi-directional sync, real-time webhooks, and a universal HTTP action node for connecting anything. The bridge between FlowFlex and the rest of the world.

**Who uses it:** Admins, IT, operations teams
**Filament Panel:** `ai`
**Depends on:** Core, [[Workflow Automation Builder]]
**Phase:** 6
**Build complexity:** High — 3 resources, 2 pages, 5 tables

---

## Features

### Connector Library

Pre-built, maintained connectors with OAuth, token management, and field mapping:

**Communication & Productivity**
- Slack — send messages, receive commands, post summaries
- Microsoft Teams — same as Slack
- Google Workspace — Calendar, Drive, Gmail, Sheets, Docs
- Microsoft 365 — Outlook, OneDrive, SharePoint
- Zoom — create meetings, get recordings
- Notion — sync pages and databases
- Loom — embed video links

**Finance & Payments**
- Stripe — sync subscriptions, charges, customers
- Mollie — payment events
- Exact Online — accounting sync (NL/BE popular)
- AFAS — (NL business software)
- Twinfield — accounting
- Moneybird — invoicing (NL)
- Xero — bi-directional accounting sync
- QuickBooks — bi-directional accounting sync

**CRM & Marketing**
- HubSpot — contact, deal, company sync
- Salesforce — bi-directional object sync
- Mailchimp — subscriber list sync
- ActiveCampaign — contact and automation sync
- LinkedIn — profile enrichment, job ad posting
- Google Ads — campaign data, lead forms

**HR & Recruiting**
- LinkedIn Jobs — post job openings
- Indeed — post job openings
- Teamtailor — ATS sync
- BambooHR — employee data migration
- Personio — employee data migration

**Operations & Logistics**
- Shopify — order and product sync
- WooCommerce — order sync
- Sendcloud — shipping labels, tracking
- DHL, FedEx, UPS — shipment tracking
- Bol.com — marketplace orders (NL/BE)
- Amazon Seller — order import

**Developer & IT**
- GitHub — link commits/PRs to tasks
- GitLab — same
- Jira — issue sync (migration helper)
- PagerDuty — incident alerts
- Datadog — alert forwarding

### Universal HTTP Connector

For anything not in the library:
- Configure any REST or GraphQL endpoint
- OAuth 2.0, API key, Basic Auth, Bearer token support
- Request builder with headers, body templating (use FlowFlex data as values)
- Response mapping (extract fields from JSON response)
- Test button — try request before saving
- Webhook receiver — unique URL per integration, HMAC verification

### Field Mapping

- Visual drag-and-drop field mapper
- Transform functions: uppercase, lowercase, date format, number format, concatenate, split
- Conditional mapping (if field X is Y, map to Z else map to W)
- Default values for missing fields

### Sync Settings

- Initial full sync on connection (with configurable date range)
- Real-time trigger sync (webhook-based, <1 second latency)
- Scheduled sync (every 15 min, hourly, daily)
- Conflict resolution: FlowFlex wins / external wins / newest wins / manual review
- Sync log with success/error counts and sample records

### Integration Marketplace

- Browse all 200+ connectors by category
- Each connector has: description, use cases, setup guide, field map, required permissions
- Community ratings (1-5 stars) and use-count shown
- Request a missing connector (voted on by customers)

---

## Database Tables (5)

### `integrations`
| Column | Type | Notes |
|---|---|---|
| `connector_key` | string | e.g. `slack`, `hubspot`, `http` |
| `name` | string | customer-defined display name |
| `auth_type` | enum | `oauth2`, `api_key`, `basic`, `bearer`, `none` |
| `auth_data` | json encrypted | tokens, keys (encrypted at rest) |
| `config` | json | connector-specific config |
| `is_active` | boolean | |
| `last_sync_at` | timestamp nullable | |
| `sync_status` | enum | `idle`, `syncing`, `error` |
| `created_by` | ulid FK | |

### `integration_field_maps`
| Column | Type | Notes |
|---|---|---|
| `integration_id` | ulid FK | |
| `source_object` | string | e.g. `contact` |
| `target_object` | string | e.g. `crm_contacts` |
| `mappings` | json | field-level mapping rules |
| `transform_rules` | json | transform functions per field |

### `integration_sync_logs`
| Column | Type | Notes |
|---|---|---|
| `integration_id` | ulid FK | |
| `sync_type` | enum | `full`, `incremental`, `webhook` |
| `records_synced` | integer | |
| `records_failed` | integer | |
| `errors` | json nullable | sample error messages |
| `started_at` | timestamp | |
| `completed_at` | timestamp nullable | |

### `integration_webhooks`
| Column | Type | Notes |
|---|---|---|
| `integration_id` | ulid FK nullable | |
| `webhook_url` | string unique | hosted URL to receive events |
| `secret` | string | HMAC secret |
| `event_count` | integer | |
| `last_event_at` | timestamp nullable | |

### `integration_connector_definitions`
| Column | Type | Notes |
|---|---|---|
| `connector_key` | string unique | |
| `name` | string | |
| `category` | string | |
| `description` | text | |
| `logo_url` | string | |
| `auth_type` | enum | |
| `scopes_required` | json | OAuth scopes needed |
| `capabilities` | json | what it can do |
| `setup_guide` | text | markdown |
| `is_published` | boolean | |

---

## Permissions

```
ai.integrations.view
ai.integrations.create
ai.integrations.edit
ai.integrations.delete
ai.integrations.sync-manual
ai.integrations.view-logs
```

---

## Related

- [[AI Overview]]
- [[Workflow Automation Builder]]
- [[API & Integrations Layer]]
- [[Smart Notifications & Triggers]]
