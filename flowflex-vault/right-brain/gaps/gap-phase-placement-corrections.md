---
type: gap
status: closed
color: "#F97316"
resolved: 2026-05-09
priority: high
identified: 2026-05-09
source: competitive-research
---

# Phase Placement Corrections

Several key features are currently planned for Phase 7–8 but should be moved earlier based on competitive analysis. These features are table-stakes in the target market (SME/mid-market) and are expected by early customers.

---

## 1. ATS / Recruitment → Move to Phase 4

**Current:** Phase 8 (if planned at all)  
**Should be:** Phase 4

**Why:** Companies 50–500 employees are FlowFlex's ICP. They hire constantly. Without ATS, they use Workable (€250/month), Recruitee, or Greenhouse — separate tools they won't abandon FlowFlex for.

**Competitive evidence:** Personio (Phase 1 feature), TeamTailor, Recruitee are standalone products with 10k+ customers. FlowFlex HR claiming to replace Personio without an ATS is a weak proposition.

**Scope for Phase 4:**
- Job postings (internal + careers page)
- Candidate pipeline (Kanban stages)
- Application forms
- Interview scheduling (integrates with [[MOC_Communications]] booking)
- Offer letters (integrates with [[MOC_DMS]])
- Hire → Employee conversion (trigger [[EmployeeHired]] event)

---

## 2. Sales Sequences / Cadences → Move to Phase 3

**Current:** Not explicitly planned (implied in CRM Phase 5+)  
**Should be:** Phase 3

**Why:** CRM without sequences is Pipedrive without automation. Outbound sales teams will use Salesloft/Outreach/Lemlist instead. These tools cost €60–100/user/month. Sales teams adopt FlowFlex CRM only if it has sequences.

**Competitive evidence:** HubSpot Sales Hub (Sequences from Starter tier), Salesloft ($150/user/month), Apollo.io all include sequences as core CRM feature.

**Scope for Phase 3:**
- Sequence builder (steps: email, call, LinkedIn task, wait)
- Auto-enroll contacts by list or trigger
- Step execution tracking (opened, replied, bounced)
- Auto-unenroll on reply or meeting booked
- Performance stats (reply rate, meeting rate per sequence)

---

## 3. Bank Feeds / Open Banking → Move to Phase 3

**Current:** Implied Phase 5–6 (not explicitly planned)  
**Should be:** Phase 3

**Why:** Finance reconciliation is unusable without bank feeds. Manual CSV import is the alternative — finance teams reject platforms that can't do automatic bank reconciliation. Xero's most-used feature is bank feeds. QuickBooks won the SME market partly on this.

**Competitive evidence:** Xero (bank feeds since 2007), QuickBooks, Exact all offer automatic bank reconciliation as core Phase 1 feature. Not having this in Phase 3 means FlowFlex Finance is a hard sell.

**Scope for Phase 3:**
- Open Banking connection (Plaid for US, Salt Edge / Nordigen for EU)
- Transaction import (daily auto-sync)
- Auto-match transactions to invoices (by amount + date ± 3 days)
- Bank reconciliation screen (match, create new record, or ignore)
- Multi-account support (current, savings, credit card)

---

## 4. Partner Management → Move to Phase 5

**Current:** Phase 8  
**Should be:** Phase 5

**Why:** FlowFlex is a horizontal platform — channel partners, resellers, and referral partners will drive a significant % of revenue. Without Partner Relationship Management (PRM), partner-sourced deals can't be tracked, commissions can't be paid automatically, and partner portals don't exist.

**Competitive evidence:** Salesforce PRM, PartnerStack ($1,500+/month), Alliances are all separate platforms companies pay for while also paying for their CRM. Embedding PRM in FlowFlex = immediate displacement of these tools.

**Scope for Phase 5:** See [[partner-relationship-management]] in CRM domain.

---

## 5. AI Agent Acceleration → Evaluate Phase 4 Readiness

**Current:** AI domain planned Phase 6–7  
**Recommendation:** Evaluate moving core AI agents to Phase 4

**Why:** By Phase 4 (likely 2025–2026 real-world build), LLM APIs are commodity. AI agents are expected, not differentiators. Competitors are shipping AI features from day 1. FlowFlex waiting until Phase 6 risks launch with "coming soon" AI features while competitors have shipped.

**Minimum viable AI for Phase 4:**
- AI email compose (in [[email-integration]])
- AI meeting summaries (in [[video-meeting-integration]])
- AI document extraction (in [[MOC_DMS]])
- AI knowledge base search (in [[knowledge-base-wiki]])

**True agentic automation (multi-step, LLM orchestration):** Phase 5–6 still realistic.

---

## Actions

| Item | Action | Status |
|---|---|---|
| ATS/Recruitment | MOC_HR already has Recruitment & ATS at Phase 4 | ✅ done |
| Sales Sequences | MOC_CRM updated: Phase 8 → Phase 3 | ✅ done |
| Bank Feeds | MOC_Finance updated: Open Banking Phase 6 → Phase 3 | ✅ done |
| Partner Portal | [[partner-relationship-management]] — Phase already 5, no change needed | ✅ done |
| AI Phase review | Core AI features (compose, summaries, doc extraction) noted in module specs; agentic remains Phase 6 | ✅ accepted |
