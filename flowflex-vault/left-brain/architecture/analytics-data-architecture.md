---
type: architecture-note
section: architecture
status: decision-required
last_updated: 2026-05-09
---

# Analytics Data Architecture

Critical architectural decision: does Analytics domain query the production PostgreSQL database directly, or does it use a separate data warehouse? This decision affects Phase 6 design and must be locked before Analytics module development begins.

---

## The Problem

Analytics queries are:
- **Slow** — aggregations over millions of rows
- **Unpredictable** — custom report builder generates arbitrary SQL
- **Contended** — long-running reads fight with OLTP writes
- **Historical** — need data from 3+ years ago (OLTP may archive/soft-delete)
- **Cross-domain** — JOIN across invoices, contacts, employees, orders in a single query

Running these directly against the production PostgreSQL OLTP database causes:
- Query timeout for end users
- Lock contention slowing down invoice creation, order processing
- Inability to optimise schema for both transactional and analytical access patterns
- No good answer for historical data after soft-delete + purge

---

## Decision Options

### Option A: Query Production DB Directly (Simple)

```
Production PostgreSQL (OLTP)
       ↕ (same DB)
Analytics Module queries
```

**Pros:**
- Zero infrastructure addition
- Data always real-time
- No ETL lag

**Cons:**
- Long queries block OLTP writes → customer-visible slowness
- Cannot optimise table structures for analytics (column-oriented)
- Historical data disappears when soft-deleted records are purged
- Custom report builder can generate catastrophically slow queries

**Verdict:** Viable only for small companies (<10k records/domain). Breaks at scale.

---

### Option B: Read Replica (Intermediate)

```
Production PostgreSQL (primary write)
       ↓ streaming replication
Read Replica PostgreSQL
       ↕
Analytics Module queries
```

**Pros:**
- Zero ETL complexity
- Near real-time (seconds lag)
- OLTP writes not impacted
- Standard PostgreSQL feature — no new infrastructure type

**Cons:**
- Same schema as OLTP — still row-oriented, unoptimised for aggregations
- Read replica still has lock/IO pressure under heavy analytics load
- No pre-aggregation layer

**Verdict:** Good interim solution. Implement for Phase 6 launch, plan warehouse migration for Phase 6+.

---

### Option C: Dedicated Data Warehouse (Full)

```
Production PostgreSQL (OLTP)
       ↓ CDC (Change Data Capture) via Debezium/pg_logical
Event Stream (Kafka or Laravel Queue)
       ↓ ETL / dbt transforms
Data Warehouse (ClickHouse or Redshift or BigQuery)
       ↕
Analytics Module queries
```

**Pros:**
- Column-oriented storage → 10–100x faster aggregations
- OLTP completely isolated from analytics load
- dbt models can pre-aggregate common metrics
- Historical data preserved even after OLTP purge (archive tier)
- Enables AI / ML training on clean, normalised data

**Cons:**
- Significant infrastructure addition (Kafka or Debezium, warehouse cluster)
- ETL lag (minutes to hours)
- dbt maintenance as schemas evolve
- Cost (managed warehouse services not free)

**Verdict:** Required at enterprise scale. Overkill for Phase 6 launch.

---

## Recommended Architecture (Phased)

### Phase 6 Launch: Read Replica + Materialised Views

```
Production PostgreSQL (primary)
       ↓ streaming replication
Read Replica (analytics queries only)
       ↓
Materialised Views (pre-computed per domain)
       ↕
Analytics Module → Dashboard, KPI, Report Builder
```

Pre-materialise the 20 most common aggregations (revenue by month, open deals by stage, headcount trend, order fulfilment rate) as PostgreSQL materialised views — refreshed every 15 minutes.

Custom report builder queries materialised views first, falls back to read replica with a query timeout guard (30s hard limit → user sees "query too complex, export to CSV instead").

### Phase 6+ (>100k records): ClickHouse Sidecar

Run ClickHouse as an add-on for high-volume tenants. Sync via Kafka events (already part of Phase 1 event bus). dbt project in `analytics/` folder transforms raw events into `fct_*` and `dim_*` tables.

```
Event Bus (Laravel Horizon queues or Kafka)
       ↓ analytics listeners
ClickHouse (column-store, tenant-sharded)
       ↓
dbt models → fct_revenue, fct_headcount, dim_product, dim_customer
       ↓
Analytics Module (same query interface, different driver)
```

Analytics module uses a `AnalyticsQueryDriver` interface — swappable between PostgreSQL read replica and ClickHouse without changing domain code.

---

## Data Retention for Analytics

| Layer | Retention | Reason |
|---|---|---|
| OLTP (production) | Active records + 90-day soft-delete | GDPR erasure, performance |
| Read Replica | Mirror of OLTP | Auto |
| Analytics Warehouse | 7 years (anonymised after GDPR erasure) | Tax law, trend analysis |
| AI Training Dataset | Anonymised forever | Model improvement |

GDPR erasure triggers two events:
1. `DSAREraseCompleted` → OLTP anonymises records
2. `AnalyticsEraseRequested` → Warehouse anonymises or deletes linked rows

---

## dbt Project Structure

```
analytics/
├── models/
│   ├── staging/          # raw source tables, no business logic
│   │   ├── stg_invoices.sql
│   │   ├── stg_contacts.sql
│   │   └── stg_orders.sql
│   ├── intermediate/     # joins, type casts
│   │   └── int_revenue_by_customer.sql
│   └── marts/            # business-facing aggregates
│       ├── fct_monthly_revenue.sql
│       ├── fct_headcount.sql
│       └── dim_customer.sql
├── seeds/                # lookup tables (country codes, tax rates)
├── tests/                # schema + data freshness tests
└── dbt_project.yml
```

---

## Related

- [[MOC_Analytics]] — consuming these data layers
- [[left-brain/architecture/event-bus.md]] — events feed the ETL pipeline
- [[left-brain/architecture/data-architecture.md]] — OLTP schema conventions
- [[left-brain/architecture/ai-gdpr-data-residency.md]] — GDPR erasure in analytics tier
