---
type: module
domain: Marketing & Content
panel: marketing
module: Contact Behavioral Scoring
phase: 4
status: planned
cssclasses: domain-marketing
migration_range: 407500–407999
last_updated: 2026-05-09
---

# Contact Behavioral Scoring

Real-time contact scoring based on website activity, email engagement, content downloads, product usage, and firmographic fit. Scores sync to CRM for sales prioritisation and trigger automated workflows when threshold reached.

---

## Why This Matters

Without scoring:
- Sales team reviews 200 MQLs/week manually
- No way to distinguish "downloaded a whitepaper" from "viewed pricing 5 times"
- Marketing can't prove their pipeline quality to sales

With scoring:
- Score 80+ → auto-assign to sales rep, trigger `HighIntentContactDetected`
- Score drops below 40 → move back to nurture sequence
- Scoring model explains *why* (field-by-field breakdown)

---

## Key Tables

```sql
CREATE TABLE mkt_scoring_models (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    name            VARCHAR(100),
    type            ENUM('manual','ml_trained'),
    status          ENUM('active','inactive','training'),
    total_points    INT DEFAULT 100,
    decay_enabled   BOOLEAN DEFAULT TRUE,
    decay_period_days INT DEFAULT 30,   -- score decays if no activity
    created_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE mkt_scoring_rules (
    id              ULID PRIMARY KEY,
    model_id        ULID NOT NULL REFERENCES mkt_scoring_models(id),
    category        ENUM('behaviour','firmographic','demographic','engagement','product','negative'),
    trigger_event   VARCHAR(100),   -- 'page_viewed', 'email_opened', 'form_submitted', 'pricing_visited'
    trigger_config  JSON NULL,      -- {page_url: '/pricing'} or {email_subject: '*'}
    points          INT NOT NULL,   -- positive or negative
    max_times       INT NULL,       -- max times this rule applies (NULL = unlimited)
    description     VARCHAR(255)
);

CREATE TABLE mkt_contact_scores (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    contact_id      ULID NOT NULL REFERENCES contacts(id),
    model_id        ULID NOT NULL REFERENCES mkt_scoring_models(id),
    score           INT DEFAULT 0,
    grade           CHAR(1) NULL,    -- A/B/C/D/F derived from score bands
    status          ENUM('cold','warm','hot','mql','sql','disqualified'),
    last_activity_at TIMESTAMP NULL,
    score_breakdown JSON NULL,       -- [{rule_id, points, triggered_at}]
    updated_at      TIMESTAMP DEFAULT NOW(),
    UNIQUE(contact_id, model_id)
);

CREATE TABLE mkt_score_events (
    id              ULID PRIMARY KEY,
    contact_id      ULID NOT NULL REFERENCES contacts(id),
    rule_id         ULID NOT NULL REFERENCES mkt_scoring_rules(id),
    points_applied  INT,
    score_before    INT,
    score_after     INT,
    triggered_at    TIMESTAMP DEFAULT NOW()
);
```

---

## Scoring Categories

### Behaviour (real-time)
| Action | Points |
|---|---|
| Visited `/pricing` page | +10 |
| Visited `/demo` page | +8 |
| Downloaded ROI calculator | +12 |
| Opened email | +2 |
| Clicked email link | +5 |
| Watched video >50% | +7 |
| Submitted contact form | +20 |
| Attended webinar | +15 |

### Firmographic (static)
| Attribute | Points |
|---|---|
| Company size 50–500 | +10 |
| Industry = target ICP | +15 |
| Country = target market | +10 |

### Negative (decay)
| Trigger | Points |
|---|---|
| Unsubscribed from email | −50 |
| Marked email as spam | −100 |
| Visited `/careers` page | −5 |
| 30 days no activity | −20/30days |

---

## CRM Integration

Score syncs to CRM contact record.  
Score change event fires → CRM view updated in real-time.  
Threshold reached (e.g. score ≥ 80) → `ContactReachedMQLThreshold` event.  
Event consumed by: Sales team notification, CRM workflow rule (auto-assign), Marketing sequence trigger.

---

## ML-Trained Model (Phase 5+)

Manual rules → start.  
ML model (logistic regression on won/lost deal data) → eventually predicts "probability of conversion" instead of rule-based score.  
Requires 500+ closed deals for training data.

---

## Related

- [[MOC_Marketing]]
- [[marketing-attribution]]
- [[MOC_CRM]] — score visible on contact, triggers assignment
- [[concept-workflow-rules]] — score thresholds trigger workflows
