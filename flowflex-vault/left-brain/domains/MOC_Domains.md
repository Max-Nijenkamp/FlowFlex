---
type: moc
section: left-brain/domains
last_updated: 2026-05-09
---

# Domains — Map of Content

32 domains total: 1 Foundation scaffold (Phase 0) + 31 business domains. Each business domain is a Filament panel section within the Workspace Panel. Plus the public frontend (not a domain).

---

## Domain-Module-Feature Hierarchy

FlowFlex organises all functionality into three levels:

```
Domain
└── Module  (toggled per company via company_module_subscriptions)
    └── Feature  (documented in the module spec — not a separate DB entity)
```

- **Domain** = a business area (HR & People, Finance, CRM…). Maps to a nav group in the workspace panel.
- **Module** = a deployable feature set within a domain (e.g. `hr.payroll`, `finance.invoicing`). Key format: `{domain}.{module}`.
- **Feature** = an individual capability within a module (e.g. "bulk payrun approval"). Documented in the module spec. Governed by permissions in the format `{domain}.{module}.{action}`.

See [[MOC_Roadmap]] for the full phase plan and [[MOC_Foundation]] for the two-panel architecture.

---

## Domain Graph

```mermaid
%%{init: {"theme": "base"}}%%
graph LR
    FOUND["00 Foundation"]:::c00
    CORE["01 Core Platform"]:::c01

    subgraph p23["Phase 2–3 · Business Core"]
        HR["02 HR & People"]:::c02
        PROJ["03 Projects"]:::c03
        FIN["04 Finance"]:::c04
        CRM["05 CRM & Sales"]:::c05
        SUB["23 Subscriptions"]:::c23
        PROC["24 Procurement"]:::c24
    end

    subgraph p45["Phase 4–5 · Operations & Compliance"]
        MKT["06 Marketing"]:::c06
        OPS["07 Operations"]:::c07
        IT["09 IT & Security"]:::c09
        LEG["10 Legal"]:::c10
        ECO["11 E-commerce"]:::c11
        FPA["25 FP&A"]:::c25
        DMS["27 Documents"]:::c27
        WHISTLE["28 Whistleblowing"]:::c28
        PRICE["30 Pricing"]:::c30
        ESG["20 ESG"]:::c20
        EVENTS["26 Events"]:::c26
        CS["22 Customer Success"]:::c22
        FSM["29 Field Service"]:::c29
        RISK["31 Risk Mgmt"]:::c31
        WORK["16 Workplace"]:::c16
    end

    subgraph p68["Phase 6–8 · Intelligence & Scale"]
        ANA["08 Analytics"]:::c08
        COMMS["12 Communications"]:::c12
        AI["14 AI & Automation"]:::c14
        LMS["13 Learning & Dev"]:::c13
        CMT["15 Community"]:::c15
        PSA["17 PSA"]:::c17
        PLG["18 PLG"]:::c18
        TRAVEL["19 Travel"]:::c19
        RE["21 Real Estate"]:::c21
    end

    FOUND --> CORE
    CORE --> HR & PROJ & FIN & CRM
    HR -->|"EmployeeHired"| FIN
    HR -->|"EmployeeHired"| LMS
    HR -->|"EmployeeHired"| IT
    FIN -->|"InvoicePaid"| CRM
    FIN -->|"InvoicePaid"| MKT
    CRM -->|"DealClosed"| FIN
    MKT -->|"FormSubmission"| CRM
    OPS -->|"POApproved"| FIN
    ECO -->|"Checkout"| FIN
    ECO -->|"Checkout"| OPS

    classDef c00 fill:#111827,color:#fff,stroke:#111827
    classDef c01 fill:#374151,color:#fff,stroke:#374151
    classDef c02 fill:#7C3AED,color:#fff,stroke:#7C3AED
    classDef c03 fill:#4F46E5,color:#fff,stroke:#4F46E5
    classDef c04 fill:#059669,color:#fff,stroke:#059669
    classDef c05 fill:#DC2626,color:#fff,stroke:#DC2626
    classDef c06 fill:#DB2777,color:#fff,stroke:#DB2777
    classDef c07 fill:#D97706,color:#fff,stroke:#D97706
    classDef c08 fill:#0284C7,color:#fff,stroke:#0284C7
    classDef c09 fill:#6B7280,color:#fff,stroke:#6B7280
    classDef c10 fill:#92400E,color:#fff,stroke:#92400E
    classDef c11 fill:#0891B2,color:#fff,stroke:#0891B2
    classDef c12 fill:#7C3AED,color:#fff,stroke:#7C3AED
    classDef c13 fill:#16A34A,color:#fff,stroke:#16A34A
    classDef c14 fill:#6366F1,color:#fff,stroke:#6366F1
    classDef c15 fill:#F59E0B,color:#000,stroke:#F59E0B
    classDef c16 fill:#0F766E,color:#fff,stroke:#0F766E
    classDef c17 fill:#7E22CE,color:#fff,stroke:#7E22CE
    classDef c18 fill:#0369A1,color:#fff,stroke:#0369A1
    classDef c19 fill:#1D4ED8,color:#fff,stroke:#1D4ED8
    classDef c20 fill:#15803D,color:#fff,stroke:#15803D
    classDef c21 fill:#57534E,color:#fff,stroke:#57534E
    classDef c22 fill:#0EA5E9,color:#fff,stroke:#0EA5E9
    classDef c23 fill:#10B981,color:#fff,stroke:#10B981
    classDef c24 fill:#F97316,color:#fff,stroke:#F97316
    classDef c25 fill:#6366F1,color:#fff,stroke:#6366F1
    classDef c26 fill:#EC4899,color:#fff,stroke:#EC4899
    classDef c27 fill:#8B5CF6,color:#fff,stroke:#8B5CF6
    classDef c28 fill:#6D28D9,color:#fff,stroke:#6D28D9
    classDef c29 fill:#EA580C,color:#fff,stroke:#EA580C
    classDef c30 fill:#0D9488,color:#fff,stroke:#0D9488
    classDef c31 fill:#B91C1C,color:#fff,stroke:#B91C1C
```

---

## Domain Registry

> Row 00 (Foundation) is the technical scaffold, not a business domain. No tenant user sees "Foundation" in their panel. All other rows are business domains surfaced as navigation sections in the Workspace Panel.

| # | Domain | Panel | Colour | Phase | Modules |
|---|---|---|---|---|---|
| 00 | [[MOC_Foundation\|Foundation]] | `admin/app` | `#111827` Gray | 0 | 3 |
| 01 | [[MOC_CorePlatform\|Core Platform]] | `admin` | `#111827` Gray | 1 | 12 |
| 02 | [[MOC_HR\|HR & People]] | `hr` | `#7C3AED` Violet | 2/8 | 21 |
| 03 | [[MOC_Projects\|Projects & Work]] | `projects` | `#4F46E5` Indigo | 2/8 | 13 |
| 04 | [[MOC_Finance\|Finance & Accounting]] | `finance` | `#059669` Emerald | 3/6 | 23 |
| 05 | [[MOC_CRM\|CRM & Sales]] | `crm` | `#DC2626` Red | 3/8 | 22 |
| 06 | [[MOC_Marketing\|Marketing & Content]] | `marketing` | `#DB2777` Pink | 5 | 19 |
| 07 | [[MOC_Operations\|Operations & Field Service]] | `operations` | `#D97706` Amber | 4/5 | 18 |
| 08 | [[MOC_Analytics\|Analytics & BI]] | `analytics` | `#0284C7` Sky | 6 | 10 |
| 09 | [[MOC_IT\|IT & Security]] | `it` | `#6B7280` Gray-500 | 4/6 | 12 |
| 10 | [[MOC_Legal\|Legal & Compliance]] | `legal` | `#92400E` Amber-800 | 4/7 | 8 |
| 11 | [[MOC_Ecommerce\|E-commerce]] | `ecommerce` | `#0891B2` Cyan | 4/5 | 15 |
| 12 | [[MOC_Communications\|Communications]] | `comms` | `#7C3AED` Violet-600 | 5 | 11 |
| 13 | [[MOC_LMS\|Learning & Development]] | `lms` | `#16A34A` Green | 7 | 11 |
| 14 | [[MOC_AI\|AI & Automation]] | `ai` | `#6366F1` Indigo-500 | 6 | 10 |
| 15 | [[MOC_Community\|Community & Social]] | `community` | `#F59E0B` Amber-400 | 7 | 7 |
| 16 | [[MOC_Workplace\|Workplace & Facility]] | `workplace` | `#0F766E` Teal | 6 | 6 |
| 17 | [[MOC_PSA\|Professional Services (PSA)]] | `psa` | `#7E22CE` Purple | 7 | 6 |
| 18 | [[MOC_PLG\|Product-Led Growth]] | `plg` | `#0369A1` Sky-700 | 7 | 6 |
| 19 | [[MOC_Travel\|Business Travel]] | `travel` | `#1D4ED8` Blue | 7 | 6 |
| 20 | [[MOC_ESG\|ESG & Sustainability]] | `esg` | `#15803D` Green-700 | 5 | 6 |
| 21 | [[MOC_RealEstate\|Real Estate & Property]] | `realestate` | `#57534E` Stone | 6 | 6 |
| 22 | [[MOC_CustomerSuccess\|Customer Success]] | `cs` | `#0EA5E9` Sky | 5 | 6 |
| 23 | [[MOC_SubscriptionBilling\|Subscription Billing & RevOps]] | `subscriptions` | `#10B981` Emerald | 3 | 6 |
| 24 | [[MOC_Procurement\|Procurement & Spend Management]] | `procurement` | `#F97316` Orange | 3 | 6 |
| 25 | [[MOC_FPA\|Financial Planning & Analysis (FP&A)]] | `fpa` | `#6366F1` Indigo | 4 | 6 |
| 26 | [[MOC_Events\|Events Management]] | `events` | `#EC4899` Pink | 5 | 6 |
| 27 | [[MOC_DMS\|Document Management]] | `dms` | `#8B5CF6` Violet | 4 | 6 |
| 28 | [[MOC_Whistleblowing\|Whistleblowing & Ethics Hotline]] | `whistleblowing` | `#6D28D9` Violet-700 | 4 | 6 |
| 29 | [[MOC_FieldService\|Field Service Management]] | `fsm` | `#EA580C` Orange-600 | 5 | 8 |
| 30 | [[MOC_Pricing\|Pricing Management]] | `pricing` | `#0D9488` Teal-600 | 4 | 5 |
| 31 | [[MOC_RiskManagement\|Enterprise Risk Management]] | `risk` | `#B91C1C` Red-700 | 5 | 6 |

**Total: 32 domains · 312 modules** (Foundation scaffold = 3; 31 business domains = 309)

---

## Cross-Domain Event Map

| Event | From | To |
|---|---|---|
| `EmployeeHired` | HR | Payroll, Onboarding, IT, LMS |
| `EmployeeOffboarded` | HR | IT, Payroll, Operations (assets) |
| `LeaveApproved` | HR | Payroll, Projects (scheduling) |
| `TimeEntryApproved` | Projects | Payroll, Finance (client billing) |
| `TaskCompleted` | Projects | Finance (milestone invoice) |
| `InvoicePaid` | Finance | CRM, Analytics, Marketing (trigger) |
| `PurchaseOrderApproved` | Operations | Finance (create bill) |
| `FormSubmissionReceived` | Marketing | CRM (create contact), Email (trigger) |
| `EventRegistrationReceived` | Marketing | CRM, Email (confirmation) |
| `AffiliateCommissionEarned` | Marketing | Finance (payable) |
| `CheckoutCompleted` | E-commerce | Finance (record sale), Operations (stock) |
| `CartAbandoned` | E-commerce | Marketing (recovery sequence) |
| `FieldJobCompleted` | Operations | Finance (invoice), Operations (stock) |
| `FieldJobCompleted` | Field Service | Finance (field invoice), Parts (deduct stock) |
| `ReportSubmitted` | Whistleblowing | Case Management (create case), Notifications |
| `ControlTestFailed` | Risk Management | IT (security incident), Legal (compliance) |
| `PriceBookUpdated` | Pricing | Ecommerce (storefront sync), CRM (quote templates) |
| `DiscountApproved` | Pricing | CRM (quote unblocked), Finance (margin recorded) |
| `CertificationExpired` | HR | LMS (renewal), Notifications |
| `TicketResolved` | CRM | Marketing (CSAT survey) |

---

## Competitive Displacement Map

| Domain | Replaces |
|---|---|
| HR & People | Personio, BambooHR, Workday |
| Projects & Work | Jira, Asana, Notion, Google Drive |
| Finance | Xero, QuickBooks, Exact, Afas |
| CRM & Sales | Salesforce, HubSpot, Pipedrive |
| Marketing | Mailchimp, HubSpot Marketing, Hootsuite |
| Operations | NetSuite Inventory, ServiceMax, Fishbowl |
| Analytics | Tableau, Power BI, Metabase |
| IT & Security | Freshservice, Jamf, Okta |
| Legal | DocuSign, ContractSafe, Juro |
| E-commerce | Shopify, WooCommerce, Magento |
| Communications | Slack, Teams, Calendly |
| Learning | Docebo, TalentLMS, Cornerstone |
| AI & Automation | Zapier, Microsoft Copilot, Make |
| Community | Circle.so, Discord, Mighty Networks |
| Customer Success | Gainsight, ChurnZero, Totango |
| Subscription Billing | Chargebee, Recurly, Paddle |
| Procurement | Coupa, Procurify, Spendesk |
| FP&A | Anaplan, Pigment, Mosaic |
| Events | Eventbrite, Cvent, Hopin |
| Document Management | DocuSign, Juro, ContractSafe, Notion |
| Whistleblowing & Ethics | NAVEX EthicsPoint, Speakfully, WhistleB, Convercent |
| Field Service Management | ServiceMax, Salesforce Field Service, Jobber, Commusoft, FieldPulse |
| Pricing Management | Salesforce CPQ, Pricefx, Vendavo, Zilliant |
| Enterprise Risk Management | LogicManager, MetricStream, Riskonnect, ServiceNow GRC |

---

## Related

- [[00_MOC_LeftBrain]]
- [[MOC_Foundation]] — Phase 0 scaffold (panels, multi-tenancy, company creation flow)
- [[MOC_Roadmap]] — 9-phase build plan
- [[MOC_Frontend]] — public pages (not a domain)
- [[MOC_Entities]] — shared data models
