---
type: module
domain: AI & Automation
panel: ai
module-key: ai.recommendations
status: planned
color: "#4ADE80"
---

# Recommendation Engine

> Cross-domain AI recommendations — next-best-action in CRM, product recommendations in ecommerce, and learning content suggestions in the LMS.

**Panel:** `ai`
**Module key:** `ai.recommendations`

---

## What It Does

The Recommendation Engine provides a centralised AI recommendation service that is consumed by multiple FlowFlex panels. Rather than each panel building its own recommendation logic, they call the shared engine with context and receive ranked suggestions. In CRM, it suggests the next action a sales rep should take with a prospect. In ecommerce, it recommends products to customers based on purchase history. In the LMS, it suggests courses to close skill gaps. Administrators configure which recommendation types are active per panel.

---

## Features

### Core
- Recommendation types: next-best-action (CRM), product recommendation (ecommerce), course recommendation (LMS), task suggestion (projects)
- Context-aware ranking: recommendations consider the entity's history, current state, and peer group behaviour
- Inline surfacing: recommendations appear inside the relevant panel resource view (not just in the AI panel)
- Confidence scoring: each recommendation carries a confidence percentage
- Feedback loop: users accept or dismiss recommendations; feedback improves future ranking

### Advanced
- Collaborative filtering: recommendations based on what similar companies or users have done
- Content-based filtering: recommend based on attribute similarity to previously successful items
- Hybrid model: combine collaborative and content-based signals for better coverage
- A/B testing framework: test recommendation variants against each other and measure conversion
- Recommendation explanations: show users why a recommendation was made in plain language

### AI-Powered
- Contextual bandits: online learning that adapts recommendation strategy based on real-time feedback
- Cross-domain signals: incorporate signals from multiple panels (e.g. CRM engagement + support ticket history) for richer recommendations
- Personalisation profiles: build per-user preference models that evolve over time

---

## Data Model

```erDiagram
    recommendation_configs {
        ulid id PK
        ulid company_id FK
        string recommendation_type
        string target_panel
        boolean is_active
        json model_params
        timestamps created_at_updated_at
    }

    recommendation_events {
        ulid id PK
        ulid company_id FK
        string recommendation_type
        ulid context_entity_id FK
        string context_entity_type
        json recommendations
        string user_action
        ulid acted_by FK
        timestamp generated_at
        timestamp acted_at
    }

    recommendation_configs ||--o{ recommendation_events : "generates"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `recommendation_configs` | Recommendation type settings | `id`, `company_id`, `recommendation_type`, `target_panel`, `is_active` |
| `recommendation_events` | Recommendations served | `id`, `recommendation_type`, `context_entity_id`, `recommendations`, `user_action` |

---

## Permissions

```
ai.recommendations.view
ai.recommendations.configure
ai.recommendations.view-performance
ai.recommendations.manage-ab-tests
ai.recommendations.export
```

---

## Filament

- **Resource:** `App\Filament\Ai\Resources\RecommendationConfigResource`
- **Pages:** `ListRecommendationConfigs`, `EditRecommendationConfig`
- **Custom pages:** `RecommendationPerformancePage`, `AbTestPage`
- **Widgets:** `AcceptanceRateWidget`, `RecommendationVolumeWidget`
- **Nav group:** Intelligence

---

## Displaces

| Feature | FlowFlex | Salesforce Einstein | Custom ML | Pendo |
|---|---|---|---|---|
| CRM next-best-action | Yes | Yes | Custom | No |
| Ecommerce product recs | Yes | No | Custom | No |
| LMS course suggestions | Yes | No | Custom | No |
| Feedback-based improvement | Yes | Yes | Custom | No |
| Included in platform | Yes | No | No | No |

---

## Implementation Notes

**Relationship to `ecommerce/recommendations`:** The `ecommerce/recommendations` module handles product-to-product recommendations (co-purchase matrix) for the storefront. This module (`ai.recommendations`) is the cross-domain recommendation engine that also covers CRM next-best-action, LMS course suggestions, and project task suggestions. They share the `ec_recommendation_sets` and `ec_recommendation_events` tables for ecommerce, but the AI recommendation engine uses its own `recommendation_configs` and `recommendation_events` tables for non-ecommerce use cases. Avoid duplicating the ecommerce product recommendation logic here — the AI engine delegates to the ecommerce module's own service for that recommendation type.

**Next-best-action in CRM:** The CRM next-best-action recommendation is implemented as a PHP rule engine (`app/Services/AI/CrmNextBestActionService.php`), not an LLM. Rules: if deal has not had activity in 7 days → suggest "Follow up". If deal probability has not changed in 14 days → suggest "Schedule meeting". If close date is within 14 days → suggest "Send proposal". The rule outputs are ranked by the deal's stage and age. LLM involvement is optional (for generating the suggested action text) but the core ranking logic is PHP.

**LMS course suggestions:** Read `employee_skills` (from talent-intelligence module) and `role_skill_requirements` for the employee's job title. Find skill gaps (required but missing or below required proficiency). Query `course_sections` and `learning_path_steps` for courses that teach those skills (requires `courses.skills` pivot table — see courses module). Rank by relevance and return top 5. Pure PHP/SQL — no LLM.

**Contextual bandits for online learning:** The spec mentions contextual bandits. For MVP, implement a simple epsilon-greedy strategy: 90% of the time, show the highest-ranked recommendation (exploit); 10% of the time, show a random recommendation from the top 10 (explore). Track which strategy was used in `recommendation_events.user_action` for analysis.

**`recommendation_events.user_action`** should be an enum: `accepted | dismissed | ignored | converted`. `accepted` = user clicked the recommendation. `dismissed` = user explicitly dismissed. `ignored` = recommendation was shown but user took no action (tracked by a TTL job that marks old events as ignored). `converted` = the recommended action was completed (e.g. the deal was moved to the next stage after a "Follow up" recommendation was accepted).

**Filament:** `RecommendationPerformancePage` is a custom `Page` showing acceptance rate, conversion rate, and revenue attribution per recommendation type as chart.js bar charts. `AbTestPage` is a custom `Page` for configuring A/B test parameters — which algorithm to test (rule-based vs ML-based) and traffic split.

## Related

- [[crm/INDEX]] — next-best-action surfaced in CRM
- [[ecommerce/INDEX]] — product recommendations in ecommerce
- [[lms/skills]] — skill gaps drive course recommendations
- [[workflow-builder]] — accepted recommendations can trigger workflow steps
