---
tags: [flowflex, domain/finance, cash-flow, forecasting, phase/6]
domain: Finance & Accounting
panel: finance
color: "#059669"
status: planned
last_updated: 2026-05-08
---

# Cash Flow Forecasting & Scenario Planning

Rolling 13-week cash flow forecast built from your actual FlowFlex data. Know your cash position 3 months out — and see what happens to it if a big client pays late.

**Who uses it:** CFO, finance team, business owners
**Filament Panel:** `finance`
**Depends on:** [[Open Banking & Bank Feeds]], [[Invoicing]], [[Accounts Payable & Receivable]], [[Payroll]], [[Subscription & MRR Tracking]]
**Phase:** 6

---

## Features

### Automated Forecast Build

The forecast is built automatically from live FlowFlex data:

| Data Source | What It Drives |
|---|---|
| Open invoices + payment terms | Expected cash inflows by date |
| Scheduled subscription billing | Recurring inflows |
| Open bills / supplier invoices | Expected outflows |
| Approved expenses | Outflows |
| Payroll schedule | Payroll outflows |
| Recurring transactions (from bank history) | Estimated recurring costs |
| Contract values + payment milestones | Future inflows |

### Rolling 13-Week View

- Week-by-week waterfall chart: opening balance → inflows → outflows → closing balance
- Colour coding: green (comfortable), amber (approaching threshold), red (projected negative)
- Drill-down: click any week to see component transactions
- Auto-rolls forward each week

### Scenario Planning

- Create named scenarios: "Base Case", "Late Payer Scenario", "New Contract Won"
- Adjust any input: delay invoice payment by 30 days, add new revenue source, delay a hire
- Compare scenarios side-by-side: cash position chart overlaid
- Save scenarios for board presentations

### AI Insights

- "Your largest cash risk in the next 4 weeks is €45,000 owed by Acme Corp (now 12 days overdue)"
- "If you collect all invoices on time, you have 18 weeks of runway"
- "3 recurring costs have increased significantly vs last quarter"
- "Your slowest-paying customer takes avg 47 days — consider tightening payment terms"

### Cash Runway (For SaaS/Startup mode)

- Current cash ÷ monthly burn rate = weeks of runway
- Burn rate trend chart (rolling 3-month)
- Break-even projection: at current growth rate, break-even in N months
- Toggle: capital-efficient mode (focus on extending runway)

### Board-Ready Reports

- One-page cash flow summary (designed for board pack)
- 13-week detailed forecast table (export CSV/PDF)
- Scenario comparison chart

---

## Competitor Comparison

| Feature | FlowFlex | Float | Dryrun | Fathom |
|---|---|---|---|---|
| Auto-built from platform data | ✅ | ❌ (manual import) | ❌ | partial |
| Scenario planning | ✅ | ✅ | ✅ | ✅ |
| AI insights | ✅ | ❌ | ❌ | ✅ |
| 13-week rolling view | ✅ | ✅ | ✅ | ✅ |
| No extra subscription | ✅ | ❌ (€69/mo) | ❌ (€49/mo) | ❌ (£30/mo) |

---

## Related

- [[Finance Overview]]
- [[Open Banking & Bank Feeds]]
- [[Invoicing]]
- [[Accounts Payable & Receivable]]
- [[Financial Reporting]]
