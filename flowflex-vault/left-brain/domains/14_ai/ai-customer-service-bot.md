---
type: module
domain: AI & Automation
panel: ai
cssclasses: domain-ai
phase: 6
status: planned
migration_range: 750000–799999
last_updated: 2026-05-09
---

# AI Customer Service Bot

Conversational AI for customer support — handles L1/L2 queries autonomously, seamlessly escalates to human agents, and learns from every resolved ticket. Replaces Intercom Fin, Tidio AI, and Zendesk Answer Bot.

---

## Features

### Knowledge Base Training
- Train on: FAQ articles, help docs, product descriptions, past resolved tickets
- Connect to CRM Knowledge Base & Wiki (auto-syncs)
- URL crawl for public help centre content
- Manual Q&A pairs
- Product catalogue awareness (answers "Is X in stock?", "What's the price of Y?")

### Conversation Engine
- Embeds in External Chat Widget (Communications module)
- Embeds in Client Portal (CRM module)
- Email auto-response (for new ticket emails)
- WhatsApp/SMS support channel
- Multi-language (auto-detects language, responds in same)

### Resolution Capabilities
- Answer FAQ-type questions
- Order status lookup (E-commerce integration)
- Invoice status lookup (Finance integration)
- Password reset trigger
- Return initiation (E-commerce returns flow)
- Appointment booking (Communications booking module)
- Create support ticket on behalf of customer

### Escalation Intelligence
- Confidence threshold: below X% → offer human handoff
- Sentiment detection: frustrated customer → immediate escalation
- High-value customer detection (lifetime value > threshold → prioritise human)
- Seamless handoff with full conversation context to human agent in CRM helpdesk

### Continuous Learning
- Flag conversations where bot failed to resolve → human review queue
- Accepted corrections feed back into training
- A/B test response variations
- CSAT rating per bot conversation

### Analytics
- Containment rate (% resolved without human)
- First contact resolution
- Average handle time (bot vs human)
- Top unanswered questions → surfaces gaps in knowledge base
- Deflection cost savings estimate

---

## Data Model

```erDiagram
    ai_bot_configs {
        ulid id PK
        ulid company_id FK
        string name
        string personality_tone
        json training_sources
        decimal confidence_threshold
        boolean human_handoff_enabled
        boolean is_active
    }

    ai_bot_conversations {
        ulid id PK
        ulid bot_config_id FK
        string channel
        string session_id
        json messages
        string resolution_status
        integer csat_rating
        ulid escalated_to_ticket_id FK
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `BotConversationEscalated` | Human handoff triggered | CRM (create ticket with context), Notifications (agent) |
| `BotResolutionFailed` | Not resolved + no escalation | Analytics (gap flagging), AI (training queue) |

---

## Permissions

```
ai.chatbot.view-any
ai.chatbot.configure
ai.chatbot.manage-training
```

---

## Competitors Displaced

Intercom Fin · Tidio AI · Zendesk Answer Bot · Freshdesk Freddy · Drift

---

## Related

- [[MOC_AI]]
- [[MOC_CRM]] — helpdesk escalation
- [[MOC_Communications]] — External Chat Widget host
- [[MOC_Ecommerce]] — order/return queries
