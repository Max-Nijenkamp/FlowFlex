---
domain: communications
module: automations
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Automations — Data Model

## `comms_automation_rules`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed, `BelongsToCompany` |
| `name` | string | |
| `channel_filter` | string nullable | channel type or null = all |
| `trigger` | string | inbound-message / conversation-created / outside-hours |
| `conditions` | jsonb | AND rules, registry-validated |
| `actions` | jsonb | typed action configs |
| `order` | int | execution order |
| `stop_processing` | boolean | default false |
| `is_active` | boolean | default true |
| `run_count` | int | default 0 — in-rule counter |
| `deleted_at` | timestamp nullable | Soft delete |

## `comms_chatbot_flows`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `name` | string | |
| `channel` | string | |
| `flow_definition` | jsonb | nodes: `{id, message, options: [{match, next/action}]}` |
| `is_active` | boolean | one active flow per channel *(assumed)* |

Chatbot flow position per conversation is held in `comms_conversations` meta *(assumed jsonb meta column, owned by the inbox)*.

## ERD

```mermaid
erDiagram
    comms_automation_rules }o--|| companies : "scoped"
    comms_chatbot_flows }o--|| companies : "scoped"
    comms_automation_rules {
        ulid id PK
        ulid company_id
        string name
        string channel_filter
        string trigger
        jsonb conditions
        jsonb actions
        int order
        boolean stop_processing
        boolean is_active
        int run_count
        timestamp deleted_at
    }
    comms_chatbot_flows {
        ulid id PK
        ulid company_id
        string name
        string channel
        jsonb flow_definition
        boolean is_active
    }
```

No separate execution-log table — counters (`run_count`) + `spatie/laravel-activitylog` *(assumed)*.

## Related

- [[_module]] · [[architecture]]
