---
type: meta
status: stable
last_updated: 2026-05-09
---

# Vault File Index

Complete inventory of all files in this vault.

---

## Root

- [[00_README]] — vault overview, quick start
- [[00_PHILOSOPHY]] — Left Brain / Right Brain system

## _core

- [[_conventions]] — naming, frontmatter, linking, Mermaid standards
- [[_index]] — this file

### _core/_templates

- [[tpl_module]] — module note template
- [[tpl_entity]] — entity note template
- [[tpl_domain-moc]] — domain MOC template
- [[tpl_concept]] — concept note template
- [[tpl_gap]] — gap / missing spec note template
- [[tpl_adr]] — architectural decision record template
- [[tpl_builder-log]] — per-module build session log template

---

## left-brain

- [[00_MOC_LeftBrain]] — master Left Brain index

### architecture

- [[MOC_Architecture]] — system overview, request flow, decisions
- [[tech-stack]] — full technology stack
- [[auth-rbac]] — 2-layer RBAC, Sanctum, Spatie Permission
- [[multi-tenancy]] — company isolation, global scopes
- [[module-system]] — Interface/Service/ServiceProvider/Controller pattern
- [[event-bus]] — cross-domain event architecture
- [[data-architecture]] — DTOs, migrations, ULID, soft deletes, multi-currency
- [[analytics-data-architecture]] — read replica vs warehouse decision, dbt structure
- [[ai-gdpr-data-residency]] — LLM routing, EU AI Act, GDPR for AI inference
- [[portal-architecture]] — unified 6-portal framework, guard isolation

### frontend (public Vue+Inertia pages)

- [[MOC_Frontend]] — all public pages overview
- [[marketing-site]] — public website, landing pages, pricing, blog
- [[client-portal]] — customer self-service portal
- [[public-pages]] — storefront, checkout, booking, learner portal, community

### domains

- [[MOC_Domains]] — all 27 domains with event map
- [[MOC_CorePlatform]] — 01 Core Platform
- [[MOC_HR]] — 02 HR & People
- [[MOC_Projects]] — 03 Projects & Work
- [[MOC_Finance]] — 04 Finance & Accounting
- [[MOC_CRM]] — 05 CRM & Sales
- [[MOC_Marketing]] — 06 Marketing & Content
- [[MOC_Operations]] — 07 Operations & Field Service
- [[MOC_Analytics]] — 08 Analytics & BI
- [[MOC_IT]] — 09 IT & Security
- [[MOC_Legal]] — 10 Legal & Compliance
- [[MOC_Ecommerce]] — 11 E-commerce & Sales Channels
- [[MOC_Communications]] — 12 Communications
- [[MOC_LMS]] — 13 Learning & Development
- [[MOC_AI]] — 14 AI & Automation
- [[MOC_Community]] — 15 Community & Social
- [[MOC_Workplace]] — 16 Workplace & Facility Management
- [[MOC_PSA]] — 17 Professional Services Automation
- [[MOC_PLG]] — 18 Product-Led Growth
- [[MOC_Travel]] — 19 Business Travel
- [[MOC_ESG]] — 20 ESG & Sustainability
- [[MOC_RealEstate]] — 21 Real Estate & Property Management
- [[MOC_CustomerSuccess]] — 22 Customer Success
- [[MOC_SubscriptionBilling]] — 23 Subscription Billing & RevOps
- [[MOC_Procurement]] — 24 Procurement & Spend Management
- [[MOC_FPA]] — 25 Financial Planning & Analysis
- [[MOC_Events]] — 26 Events Management
- [[MOC_DMS]] — 27 Document Management
- [[MOC_Whistleblowing]] — 28 Whistleblowing & Ethics Hotline
- [[MOC_FieldService]] — 29 Field Service Management
- [[MOC_Pricing]] — 30 Pricing Management
- [[MOC_RiskManagement]] — 31 Enterprise Risk Management

#### New Module Notes Added
- Finance: [[corporate-cards-spend-management]], [[travel-expense-management]], [[accounts-receivable-automation]], [[multi-entity-consolidation]]
- CRM: [[cpq]], [[partner-relationship-management]], [[territory-quota-management]]
- Operations: [[warehouse-management]], [[fleet-management]], [[manufacturing-bom]]
- E-commerce: [[product-reviews-ratings]], [[gift-cards-vouchers]], [[promotions-discount-engine]]
- Marketing: [[referral-program]], [[review-reputation-management]], [[digital-asset-management]]
- AI: [[ai-document-processing]], [[ai-meeting-intelligence]], [[ai-customer-service-bot]]
- IT: [[team-password-secrets-vault]]
- HR: [[employee-wellbeing-mental-health]], [[global-payroll]], [[employee-self-service-portal]]
- Legal: [[dsar-self-service-portal]], [[contract-management]], [[policy-management]], [[data-privacy]], [[insurance-licence-tracking]], [[ai-contract-intelligence]], [[esignature-native]]
- Core: [[notification-preferences]]
- Concepts: [[concept-platform-features]]
- Core: [[audit-log]], [[data-import-engine]], [[sandbox-environment]]
- Finance: [[general-ledger-chart-of-accounts]], [[payroll-tax-filing]], [[intercompany-billing]]
- CRM: [[telephony-call-center]], [[email-tracking]], [[meeting-scheduler]]
- Operations: [[lot-batch-serial-tracking]], [[demand-planning-forecasting]], [[supplier-qualification-onboarding]]
- HR: [[time-attendance]], [[hr-people-analytics]]
- Marketing: [[landing-page-builder]], [[utm-link-management]]
- LMS: [[scorm-xapi-support]], [[course-builder-lms]], [[skills-matrix]], [[succession-planning]], [[mentoring-coaching]], [[external-training]], [[ai-learning-coach]], [[certification-compliance-training]], [[external-learner-portal]], [[live-virtual-classroom]]
- IT: [[it-procurement-requests]]
- Ecommerce: [[product-bundles]], [[headless-commerce-api]]
- Analytics: [[scheduled-reports]], [[embedded-analytics]]
- ESG: [[carbon-footprint-tracking]], [[esg-report-builder]], [[social-metrics-management]], [[governance-reporting]], [[net-zero-roadmap]], [[supply-chain-sustainability]]
- Workplace: [[hot-desk-space-booking]], [[meeting-room-management]], [[visitor-management]], [[facility-maintenance-requests]], [[office-resource-management]], [[workplace-analytics]]
- PSA: [[client-engagement-management]], [[utilisation-capacity-tracking]], [[project-profitability]], [[retainer-sow-management]], [[resource-scheduling-psa]], [[agency-billing-intelligence]]
- PLG: [[feature-flags]], [[in-app-tours-onboarding]], [[product-usage-analytics]], [[in-app-changelog-announcements]], [[in-app-nps-feedback]], [[user-segmentation]]
- Travel: [[travel-booking-portal]], [[travel-policy-engine]], [[trip-approvals-workflow]], [[duty-of-care-traveller-safety]], [[preferred-supplier-management]], [[corporate-carbon-tracking]]
- Real Estate: [[property-register]], [[lease-management]], [[tenant-occupancy-management]], [[rental-billing-arrears]], [[ifrs-16-lease-accounting]], [[property-maintenance]]
- Customer Success: [[customer-health-scoring]], [[cs-playbooks-alerts]], [[customer-onboarding-tracking]], [[renewal-forecasting]], [[qbr-preparation]], [[expansion-revenue-tracking]]
- Subscription Billing: [[subscription-lifecycle-management]], [[recurring-billing-engine]], [[dunning-management]], [[mrr-arr-analytics]], [[usage-based-billing]], [[revenue-recognition]]
- Procurement: [[purchase-requisitions]], [[purchase-orders]], [[goods-received-notes-grn]], [[three-way-match-invoice-approval]], [[supplier-catalog]], [[spend-analytics]]
- FP&A: [[annual-budget-builder]], [[budget-vs-actual-reporting]], [[rolling-forecasts]], [[scenario-modeling]], [[headcount-planning]], [[board-reporting-pack]]
- Events: [[event-creation-branding]], [[registration-ticketing]], [[attendee-management]], [[session-speaker-management]], [[event-checkin-app]], [[post-event-analytics]]
- DMS: [[document-templates]], [[document-workflows]], [[e-signature]], [[contract-repository]], [[version-control]], [[document-automation]]
- Finance (new): [[fixed-assets]], [[vat-tax-filing]], [[cash-flow-forecasting]], [[credit-control]]
- HR (new): [[compensation-benefits]], [[org-chart-workforce-planning]], [[performance-reviews-360]]
- CRM (new): [[sales-forecasting]], [[commission-management]], [[contract-lifecycle-management]]
- Marketing (new): [[social-media-management]], [[affiliate-program]], [[marketing-attribution]]
- Operations (new): [[equipment-maintenance-cmms]], [[returns-management-rma]], [[quality-management-qms]]
- Analytics (new): [[dashboard-builder]], [[data-connectors-etl]], [[anomaly-detection-alerting]]
- IT (new): [[itsm-helpdesk]], [[service-catalog-it]], [[change-management-itil]]
- Projects (new): [[task-management]], [[kanban-boards]], [[gantt-timeline]], [[sprint-agile]], [[project-templates]], [[project-time-tracking]], [[project-budget-costs]]
- Ecommerce (new): [[subscription-products]], [[marketplace-integration]]
- Communications (new): [[team-messaging]], [[email-integration]], [[knowledge-base-wiki]], [[company-announcements]], [[video-meeting-integration]]
- Community (new): [[discussion-forums]], [[member-profiles-reputation]], [[community-events]], [[moderation-tools]], [[gamification-points]]
- Finance (research): [[embedded-payments]]
- HR (research): [[talent-intelligence]]
- Marketing (research): [[contact-behavioral-scoring]]
- AI (research): [[eu-ai-act-compliance]]
- Whistleblowing (new domain): [[anonymous-intake-portal]], [[case-management-investigation]], [[eu-whistleblower-directive-compliance]], [[reporter-communication-portal]]
- Field Service (new domain): [[job-dispatch-scheduling]], [[mobile-field-app]], [[technician-management]], [[customer-sign-off]], [[field-invoicing]], [[parts-inventory-fsm]]
- Pricing (new domain): [[price-book-management]], [[discount-approval-workflows]], [[ai-price-optimization]], [[competitor-price-monitoring]], [[pricing-analytics]]
- Risk Management (new domain): [[risk-register]], [[risk-assessments-rcsa]], [[controls-library]], [[heat-maps-risk-reporting]], [[incident-management-risk]], [[business-continuity-planning]]
- Concepts (research): [[concept-custom-objects]], [[concept-formula-engine]], [[concept-workflow-rules]]
- Gaps (research): [[gap-phase-placement-corrections]]

### entities

- [[MOC_Entities]] — all entities with master ERD
- [[entity-company]] — tenant anchor
- [[entity-user]] — platform user (admin panels)
- [[entity-employee]] — HR employee profile
- [[entity-contact]] — CRM contact
- [[entity-project]] — project container
- [[entity-invoice]] — financial document
- [[entity-product]] — sellable/physical item
- [[entity-module-subscription]] — module access control

### concepts

- [[MOC_Concepts]] — all cross-cutting concepts
- [[concept-multi-tenancy]]
- [[concept-interface-service-pattern]]
- [[concept-dto-pattern]]
- [[concept-event-driven]]

### design-system

- [[MOC_DesignSystem]] — brand, colours, typography, components

### roadmap

- [[MOC_Roadmap]] — 8-phase build plan

---

## right-brain

- [[STATUS_Dashboard]] — current build state per domain
- [[ACTIVATION_GUIDE]] — how to start a build session

### builder-logs _(empty — created per module during build)_

### validation

- [[MOC_Validation]] — validation checklist index
- [[build-checklist]] — pre/post-build checklist (copy per module)

### gaps

- [[MOC_Gaps]] — open gaps and tech debt

### evolution

- [[MOC_Evolution]] — architectural decisions and pivots
