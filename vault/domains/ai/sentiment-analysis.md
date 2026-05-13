---
type: module
domain: AI & Automation
panel: ai
module-key: ai.sentiment
status: planned
color: "#4ADE80"
---

# Sentiment Analysis

> Automated sentiment analysis on customer feedback, support tickets, and employee survey responses — with trend monitoring and alerting.

**Panel:** `ai`
**Module key:** `ai.sentiment`

---

## What It Does

Sentiment Analysis automatically scores text-based inputs across FlowFlex for positive, neutral, or negative sentiment, and extracts the key topics or themes driving that sentiment. It runs on customer feedback submitted through ecommerce reviews or CRM notes, support ticket descriptions and resolution notes, and employee survey open-text responses. Sentiment trends are tracked over time so L&D and support leaders can see whether initiatives are having a measurable effect on customer or employee sentiment.

---

## Features

### Core
- Automated scoring: classify each text input as positive, neutral, or negative with a confidence score
- Topic extraction: identify the key subjects mentioned (e.g. pricing, delivery, management, workload)
- Source integration: run automatically on support tickets, ecommerce reviews, survey responses, and chatbot transcripts
- Sentiment timeline: trend line of aggregate sentiment score over time for each source
- Alert thresholds: notify when average sentiment drops below a configured threshold

### Advanced
- Aspect-level sentiment: score sentiment separately for different aspects within a single text (e.g. "delivery was fast but packaging was poor")
- Segmented analysis: break down sentiment by product, department, region, or customer segment
- Competitor mention detection: flag when a competitor is mentioned and what sentiment surrounds it
- Volume-weighted scoring: weight sentiment by the importance of the source (e.g. high-value customer reviews weighted higher)
- Bulk reprocessing: reprocess historical records when model is updated

### AI-Powered
- Multilingual sentiment: detect and score sentiment in 20+ languages automatically
- Emerging theme detection: identify new topics appearing in feedback that were not previously tracked
- Root cause correlation: correlate negative sentiment spikes with operational events (e.g. a product update or a delivery delay)

---

## Data Model

```erDiagram
    sentiment_analyses {
        ulid id PK
        ulid company_id FK
        string source_module
        string source_record_type
        ulid source_record_id FK
        string sentiment
        decimal sentiment_score
        json topics
        json aspect_sentiments
        string language
        timestamp analysed_at
    }

    sentiment_snapshots {
        ulid id PK
        ulid company_id FK
        string source_module
        decimal avg_sentiment_score
        integer record_count
        date snapshot_date
    }

    sentiment_analyses }o--|| companies : "belongs to"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `sentiment_analyses` | Per-record sentiment | `id`, `company_id`, `source_module`, `source_record_id`, `sentiment`, `sentiment_score`, `topics` |
| `sentiment_snapshots` | Aggregated trends | `id`, `company_id`, `source_module`, `avg_sentiment_score`, `snapshot_date` |

---

## Permissions

```
ai.sentiment.view
ai.sentiment.view-employee-sentiment
ai.sentiment.configure-alerts
ai.sentiment.view-trends
ai.sentiment.export
```

---

## Filament

- **Resource:** None (read-only, no CRUD)
- **Pages:** N/A
- **Custom pages:** `SentimentDashboardPage`, `SentimentTopicsPage`, `SentimentAlertConfigPage`
- **Widgets:** `SentimentTrendWidget`, `TopNegativeTopicsWidget`, `SentimentBySourceWidget`
- **Nav group:** Intelligence

---

## Displaces

| Feature | FlowFlex | Medallia | Qualtrics | Custom NLP |
|---|---|---|---|---|
| Multi-source sentiment | Yes | Partial | Partial | Custom |
| Aspect-level analysis | Yes | Yes | Yes | Custom |
| Native platform integration | Yes | No | No | No |
| Trend alerting | Yes | Yes | Yes | No |
| Included in platform | Yes | No | No | No |

---

## Implementation Notes

**AI mechanism:** Sentiment scoring uses the **OpenAI GPT-4o** API via a structured output prompt. The service `app/Services/AI/SentimentService.php` sends the text to the API with a system prompt requesting a JSON response: `{sentiment: "positive"|"neutral"|"negative", score: -1.0 to 1.0, topics: [string], aspect_sentiments: [{aspect: string, sentiment: string}], language: string}`. The structured outputs feature (JSON mode) ensures parseable responses.

**Alternative (no external AI):** For basic sentiment scoring without LLM cost, consider `php-ai/php-ml` (PHP ML library) with a pre-trained sentiment classifier. Accuracy is lower but cost is zero. This is suitable for high-volume sources like ecommerce reviews where per-record LLM costs become significant. Use a hybrid: ML library for high-volume sources, GPT-4o for important/low-volume sources (e.g. high-value customer feedback).

**Auto-trigger on source events:** Register `SentimentListener` for each source event type:
- `EcommerceReviewCreated` → analyse `product_reviews.content`
- `SupportTicketCreated` → analyse `tickets.description`
- `SurveyResponseSubmitted` → analyse open-text responses
- `ChatbotConversationEnded` → analyse full transcript

Each listener dispatches `AnalyseSentimentJob` (queued on the `ai` queue) passing the source model type and ID.

**Multilingual support:** GPT-4o handles 20+ languages natively. The response includes the detected `language` field. For the PHP ML alternative, language detection requires a separate library (`php-text-analyzer`).

**`sentiment_snapshots` daily aggregation:** `ComputeSentimentSnapshotsJob` runs nightly — it aggregates all `sentiment_analyses` records from the past day grouped by `(company_id, source_module)`, computes the average score and record count, and inserts into `sentiment_snapshots`. The trend line on `SentimentDashboardPage` reads from this table for performance — not from the raw `sentiment_analyses` table.

**Filament:** `SentimentDashboardPage` is a custom `Page` — it renders a multi-line chart.js line chart with one series per source module, a topics word cloud (third-party `wordcloud2.js`), and a filterable list of recent low-sentiment records. `SentimentTopicsPage` and `SentimentAlertConfigPage` are simpler custom Pages. None are standard Resource lists.

**GDPR:** `sentiment_analyses` stores `source_record_id` linking back to PII-containing source records (messages, tickets, reviews). On GDPR erasure, delete the `sentiment_analyses` row entirely (it is derivative data, not the primary PII holder). The source record's own GDPR erasure handles the primary PII.

## Related

- [[chatbot]] — chatbot transcripts processed for sentiment
- [[anomaly-detection]] — sentiment drops can trigger anomaly alerts
- [[workflow-builder]] — negative sentiment can trigger escalation workflows
- [[analytics/INDEX]] — sentiment data in cross-platform analytics
