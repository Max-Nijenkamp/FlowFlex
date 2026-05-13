---
type: module
domain: Pricing Management
panel: pricing
module: Competitor Price Monitoring
phase: 5
status: complete
cssclasses: domain-pricing
migration_range: 1101500–1101999
last_updated: 2026-05-12
---

# Competitor Price Monitoring

Track competitor pricing via automated web scraping and manual entry. Triggers alerts when competitor prices change significantly. Feeds data into AI price optimisation model.

---

## Key Tables

```sql
CREATE TABLE pricing_competitors (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    name            VARCHAR(100) NOT NULL,
    website         VARCHAR(255) NULL,
    notes           TEXT NULL
);

CREATE TABLE pricing_competitor_products (
    id              ULID PRIMARY KEY,
    competitor_id   ULID NOT NULL REFERENCES pricing_competitors(id),
    our_product_id  ULID NOT NULL,        -- maps to our product
    competitor_product_name VARCHAR(255),
    competitor_url  VARCHAR(500) NULL,    -- URL to scrape
    scrape_selector VARCHAR(500) NULL,    -- CSS selector for price element
    monitoring_active BOOLEAN DEFAULT TRUE,
    last_checked_at TIMESTAMP NULL
);

CREATE TABLE pricing_competitor_prices (
    id              ULID PRIMARY KEY,
    competitor_product_id ULID NOT NULL REFERENCES pricing_competitor_products(id),
    price           DECIMAL(12,2) NOT NULL,
    currency        CHAR(3) DEFAULT 'EUR',
    source          ENUM('scrape','manual','api'),
    scraped_html    TEXT NULL,            -- raw HTML for audit
    recorded_at     TIMESTAMP DEFAULT NOW()
);

CREATE TABLE pricing_price_alerts (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    competitor_product_id ULID NOT NULL REFERENCES pricing_competitor_products(id),
    alert_type      ENUM('price_drop','price_increase','below_our_price','above_our_price'),
    threshold_pct   DECIMAL(5,2) DEFAULT 5,  -- alert when change > threshold%
    notified_users  JSON,                 -- array of user IDs
    is_active       BOOLEAN DEFAULT TRUE
);
```

---

## Scraping Engine

Scheduled scraper runs nightly (or hourly for high-priority competitors).  
Uses headless browser (Playwright) for JS-rendered prices.  
Respects `robots.txt` — only used for publicly visible pricing pages.  
Falls back to manual entry if scraping blocked.

---

## Price Gap Analysis

Dashboard shows: Our Price vs Competitor Price, for each product-competitor pair.  
Calculates: `gap_pct = (our_price - competitor_price) / competitor_price * 100`.

`gap_pct > 0` → we're more expensive → review justification  
`gap_pct < -20%` → we're significantly cheaper → potential margin recovery opportunity

---

## Alert Rules

Configurable per product:
- "Alert if [Competitor X] drops price by more than 10%"
- "Alert if we are more than 15% above [Competitor Y]"
- "Alert if [Competitor Z] price falls below our min_price"

---

## Related

- [[MOC_Pricing]]
- [[ai-price-optimization]]
- [[pricing-analytics]]
