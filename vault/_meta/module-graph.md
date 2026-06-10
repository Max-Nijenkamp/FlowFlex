---
type: meta
category: graph
status: draft
last-updated: 2026-06-10
color: "#6B7280"
---

# Module Graph — Whole-Vault Dependency Map

One row per module: the machine-readable graph in a single read. **Generated from spec frontmatter — never hand-edit a row without updating the spec; frontmatter is the source of truth.** Rows are added per rewrite wave; `status: draft` until all 173 rows present.

Legend: deps = `depends-on` (hard, build-blocking) · soft = `soft-depends` · fires/consumes = event class names.

---

## Foundation (8)

| module-key | priority | deps | soft | fires | consumes | tables |
|---|---|---|---|---|---|---|
| foundation.scaffold | v1-core | — | — | — | — | companies, users, admins |
| foundation.docker | v1-core | foundation.scaffold | — | — | — | — |
| foundation.tenancy | v1-core | foundation.scaffold | — | — | — | — |
| foundation.queues | v1-core | foundation.scaffold, foundation.tenancy | — | — | — | — |
| foundation.email | v1-core | foundation.scaffold, foundation.queues | — | — | — | — |
| foundation.panels | v1-core | foundation.scaffold, foundation.tenancy | — | — | — | — |
| foundation.permissions | v1-core | foundation.scaffold, foundation.tenancy, foundation.panels | — | — | — | — |
| foundation.tests | v1-core | foundation.scaffold, foundation.tenancy | — | — | — | — |

## Core Platform (15)

| module-key | priority | deps | soft | fires | consumes | tables |
|---|---|---|---|---|---|---|
| core.settings | v1-core | foundation.panels, foundation.tenancy | core.files | — | — | (spatie settings) |
| core.rbac | v1-core | foundation.panels, foundation.permissions | core.invitations | — | — | (spatie permission) |
| core.invitations | v1-core | foundation.panels, foundation.email, core.rbac | — | — | — | user_invitations |
| core.billing | v1-core | foundation.panels, foundation.tenancy, foundation.queues, core.settings | core.notifications | ModuleActivated, CompanySubscriptionSuspended | — | module_catalog, company_module_subscriptions, billing_invoices, billing_invoice_lines |
| core.marketplace | v1-core | core.billing | — | — | — | — |
| core.audit | v1-core | foundation.panels, foundation.tenancy | — | — | — | activity_log |
| core.notifications | v1-core | foundation.panels, foundation.email, foundation.queues | — | — | ModuleActivated, CompanySubscriptionSuspended, DSARRequestSubmitted | notifications, notification_preferences |
| core.files | v1-core | foundation.tenancy, core.settings | — | — | — | media |
| core.import | v1 | core.files, foundation.queues, core.billing, core.rbac | hr.profiles, crm.contacts | — | — | data_imports |
| core.webhooks | v1 | foundation.queues, core.billing, core.rbac | — | — | — | webhook_endpoints, webhook_deliveries |
| core.api | v1 | core.rbac, core.billing | — | — | — | personal_access_tokens |
| core.setup | v1 | core.settings, core.invitations, core.marketplace | core.files | — | — | — |
| core.privacy | v1 | core.settings, foundation.queues, core.files, core.rbac, core.billing | — | DSARRequestSubmitted | — | dsar_requests, consent_logs |
| core.i18n | v1 | core.settings | — | — | — | — |
| core.health | v1 | foundation.queues, foundation.panels | — | — | — | — |

## HR & People (15)

| module-key | priority | deps | soft | fires | consumes | tables |
|---|---|---|---|---|---|---|
| hr.profiles | v1-core | core.billing, core.rbac, core.files | hr.org, core.import | EmployeeHired, EmployeeOffboarded | — | hr_employees, hr_departments, hr_emergency_contacts |
| hr.leave | v1-core | hr.profiles, core.billing, core.rbac, core.notifications | hr.payroll, hr.shifts, hr.self-service | LeaveRequestApproved | — | hr_leave_types, hr_leave_balances, hr_leave_requests |
| hr.onboarding | v1-core | hr.profiles, core.billing, core.rbac, core.notifications | hr.self-service | — | EmployeeHired | hr_onboarding_templates, hr_onboarding_tasks, hr_onboarding_plans, hr_onboarding_plan_tasks |
| hr.payroll | v1-core | hr.profiles, core.billing, core.rbac, core.notifications | finance.ledger, hr.leave, hr.time, hr.compensation, finance.expenses | PayrollRunApproved | EmployeeHired, EmployeeOffboarded, LeaveRequestApproved, TimesheetApproved, ExpenseApproved | hr_payroll_runs, hr_payslips, hr_deduction_types, hr_payroll_employees |
| hr.org | v1 | hr.profiles, core.billing, core.rbac | — | — | — | — |
| hr.self-service | v1 | hr.profiles, core.billing, core.rbac | hr.leave, hr.payroll, hr.onboarding | — | — | — |
| hr.recruitment | v1 | hr.profiles, core.billing, core.rbac, core.files, core.notifications | hr.workforce | — | — | hr_job_requisitions, hr_applicants, hr_interviews, hr_offers |
| hr.performance | v1 | hr.profiles, core.billing, core.rbac, core.notifications | hr.feedback | — | — | hr_review_cycles, hr_reviews, hr_review_goals |
| hr.time | v1 | hr.profiles, core.billing, core.rbac | hr.payroll, hr.shifts | TimesheetApproved | — | hr_time_entries, hr_timesheets |
| hr.shifts | v1 | hr.profiles, core.billing, core.rbac, core.notifications | hr.time, hr.leave | — | LeaveRequestApproved | hr_shifts, hr_shift_swap_requests |
| hr.compensation | v1 | hr.profiles, hr.payroll, core.billing, core.rbac | — | — | — | hr_compensation_bands, hr_benefits, hr_employee_benefits, hr_salary_history |
| hr.analytics | v1 | hr.profiles, core.billing, core.rbac | hr.leave, hr.payroll | — | — | — |
| hr.workforce | v1 | hr.profiles, core.billing, core.rbac | hr.recruitment, finance.budgets | — | — | hr_headcount_plans, hr_planned_roles |
| hr.feedback | v1 | hr.profiles, core.billing, core.rbac, core.notifications | hr.performance | — | — | hr_feedback, hr_one_on_ones |
| hr.dei | v1 | hr.profiles, core.billing, core.rbac, core.privacy | hr.compensation, hr.recruitment | — | — | hr_dei_attributes, hr_dei_snapshots |

## Finance & Accounting (13)

| module-key | priority | deps | soft | fires | consumes | tables |
|---|---|---|---|---|---|---|
| finance.ledger | v1-core | core.billing, core.rbac, core.settings | finance.invoicing, finance.expenses, hr.payroll | — | PayrollRunApproved | fin_accounts, fin_journal_entries, fin_journal_lines, fin_fiscal_periods |
| finance.invoicing | v1-core | finance.ledger, core.billing, core.rbac, core.settings, foundation.queues | crm.deals, crm.contacts, finance.tax, finance.ar, finance.currency | InvoicePaid | DealWon | fin_customers, fin_invoices, fin_invoice_lines, fin_payments |
| finance.expenses | v1-core | finance.ledger, core.billing, core.rbac, core.files, core.notifications | hr.profiles, hr.payroll, finance.ap | ExpenseApproved | — | fin_expenses, fin_expense_categories, fin_expense_reports |
| finance.bank | v1-core | finance.ledger, core.billing, core.rbac, core.files | finance.invoicing, finance.expenses | — | — | fin_bank_accounts, fin_bank_transactions |
| finance.ar | v1 | finance.invoicing, core.billing, core.rbac, core.notifications | — | — | InvoicePaid | fin_ar_dunning_rules, fin_ar_writeoffs |
| finance.ap | v1 | finance.ledger, core.billing, core.rbac, core.files | operations.purchase-orders, operations.goods-receipt, finance.expenses | — | GoodsReceived | fin_suppliers, fin_bills, fin_bill_lines, fin_payment_runs |
| finance.budgets | v1 | finance.ledger, core.billing, core.rbac, core.notifications | finance.forecasting, procurement.requisitions, hr.workforce | — | — | fin_budgets, fin_budget_lines |
| finance.reporting | v1 | finance.ledger, core.billing, core.rbac, core.settings | finance.budgets, analytics.exports | — | — | — |
| finance.tax | v1 | finance.ledger, core.billing, core.rbac | finance.invoicing, finance.ap, finance.expenses | — | — | fin_tax_rates, fin_tax_classes, fin_tax_periods |
| finance.currency | v1 | finance.ledger, core.billing, core.rbac, core.settings | finance.invoicing, finance.ap, finance.expenses | — | — | fin_currencies, fin_exchange_rates |
| finance.forecasting | v1 | finance.ledger, finance.budgets, core.billing, core.rbac | crm.forecasting, hr.workforce, finance.cashflow | — | — | fin_forecasts, fin_forecast_lines |
| finance.cashflow | v1 | finance.invoicing, finance.bank, core.billing, core.rbac, core.notifications | finance.ap, hr.payroll, finance.forecasting | — | — | fin_cashflow_projections, fin_cashflow_items |
| finance.assets | v1 | finance.ledger, core.billing, core.rbac | it.assets | — | — | fin_fixed_assets, fin_depreciation_entries |

## CRM & Sales (15)

| module-key | priority | deps | soft | fires | consumes | tables |
|---|---|---|---|---|---|---|
| crm.contacts | v1-core | core.billing, core.rbac | core.import, crm.activities, crm.deals | — | FormSubmissionReceived, EventRegistrationReceived, InvoicePaid | crm_contacts, crm_accounts, crm_contact_accounts |
| crm.deals | v1-core | crm.contacts, crm.pipeline, core.billing, core.rbac | finance.invoicing, crm.quotes, crm.pricing, crm.activities | DealWon, DealLost | — | crm_deals, crm_deal_contacts, crm_deal_products |
| crm.pipeline | v1-core | crm.deals, core.billing, core.rbac | — | — | — | crm_pipeline_stages |
| crm.activities | v1-core | crm.contacts, core.billing, core.rbac, core.notifications | crm.deals | — | — | crm_activities |
| crm.quotes | v1-core | crm.deals, core.billing, core.rbac, foundation.queues | crm.pricing, finance.tax, finance.invoicing | — | — | crm_quotes, crm_quote_lines |
| crm.email | v1 | crm.contacts, crm.activities, core.billing, core.rbac, foundation.queues | crm.deals | — | — | crm_email_connections, crm_emails |
| crm.segments | v1 | crm.contacts, core.billing, core.rbac | crm.sequences, marketing.campaigns, comms.broadcast | — | — | crm_segments, crm_segment_members |
| crm.sequences | v1 | crm.contacts, crm.activities, core.billing, core.rbac, foundation.queues | crm.email, crm.deals, crm.segments | — | DealWon, InvoicePaid | crm_sequences, crm_sequence_steps, crm_sequence_enrolments |
| crm.forecasting | v1 | crm.deals, core.billing, core.rbac | finance.forecasting | — | — | crm_quotas, crm_forecast_snapshots |
| crm.scheduling | v1 | crm.contacts, crm.activities, core.billing, core.rbac, foundation.email | — | — | — | crm_meeting_types, crm_bookings, crm_availability |
| crm.pricing | v1 | crm.deals, core.billing, core.rbac | crm.quotes, crm.segments, finance.currency | — | — | crm_products, crm_price_books, crm_price_book_entries, crm_volume_discounts |
| crm.contracts | v1 | crm.deals, core.billing, core.rbac, core.files, core.notifications | crm.quotes, legal.contracts | — | — | crm_contracts |
| crm.deal-rooms | v1 | crm.deals, core.billing, core.rbac, core.files | crm.contacts | — | — | crm_deal_rooms, crm_deal_room_documents, crm_deal_room_action_items, crm_deal_room_stakeholders |
| crm.revenue-intelligence | v1 | crm.deals, crm.activities, core.billing, core.rbac | ai.copilot, crm.forecasting | — | — | crm_deal_health, crm_win_loss |
| crm.referrals | v1 | crm.contacts, core.billing, core.rbac, core.notifications | crm.deals, ecommerce.promotions | — | — | crm_referral_programs, crm_referrals |

## Projects & Work (11)

| module-key | priority | deps | soft | fires | consumes | tables |
|---|---|---|---|---|---|---|
| projects.projects | p2 | core.billing, core.rbac | crm.contacts, projects.tasks, projects.time | — | — | proj_projects, proj_project_members |
| projects.tasks | p2 | projects.projects, core.billing, core.rbac, core.notifications, core.files | projects.kanban, projects.sprints, projects.time, projects.milestones | — | — | proj_tasks, proj_task_sections, proj_task_dependencies, proj_task_comments |
| projects.kanban | p2 | projects.tasks, core.billing, core.rbac | projects.sprints | — | — | — |
| projects.sprints | p2 | projects.tasks, core.billing, core.rbac | — | — | — | proj_sprints, proj_sprint_tasks |
| projects.time | p2 | projects.tasks, core.billing, core.rbac | finance.invoicing | — | — | proj_time_entries |
| projects.milestones | p2 | projects.projects, projects.tasks, core.billing, core.rbac, core.notifications | projects.gantt | — | — | proj_milestones, proj_milestone_tasks |
| projects.gantt | p2 | projects.tasks, projects.milestones, core.billing, core.rbac | — | — | — | — |
| projects.okrs | p2 | core.billing, core.rbac, core.notifications | projects.projects | — | — | proj_objectives, proj_key_results, proj_okr_checkins |
| projects.templates | p2 | projects.projects, projects.tasks, projects.milestones, core.billing, core.rbac | — | — | — | proj_templates, proj_template_sections, proj_template_tasks, proj_template_milestones |
| projects.workload | p2 | projects.tasks, core.billing, core.rbac | hr.profiles, projects.resources | — | — | — |
| projects.resources | p2 | projects.projects, core.billing, core.rbac | projects.time, projects.workload | — | — | proj_resource_allocations |
## Support & Help Desk (7)

| module-key | priority | deps | soft | fires | consumes | tables |
|---|---|---|---|---|---|---|
| support.tickets | p2 | core.billing, core.rbac, core.files, core.notifications, foundation.email | crm.contacts, support.sla, support.canned, support.automations | TicketResolved | — | sup_tickets, sup_ticket_replies, sup_ticket_categories |
| support.kb | p2 | core.billing, core.rbac | support.tickets | — | — | sup_kb_articles, sup_kb_categories |
| support.sla | p2 | support.tickets, core.billing, core.rbac, core.notifications, core.settings | — | — | — | sup_sla_policies, sup_sla_targets, sup_sla_events |
| support.canned | p2 | support.tickets, core.billing, core.rbac | support.chat | — | — | sup_canned_responses |
| support.automations | p2 | support.tickets, core.billing, core.rbac, foundation.queues | support.sla, support.canned | — | — | sup_automation_rules, sup_automation_logs |
| support.chat | p2 | support.tickets, core.billing, core.rbac, foundation.queues | support.canned, crm.contacts | — | — | sup_chats, sup_chat_messages, sup_agent_availability |
| support.analytics | p2 | support.tickets, core.billing, core.rbac | support.sla | — | TicketResolved | sup_csat_responses |
## Communications (8)

| module-key | priority | deps | soft | fires | consumes | tables |
|---|---|---|---|---|---|---|
| comms.inbox | p2 | core.billing, core.rbac, core.files, foundation.queues | crm.contacts, comms.email, comms.whatsapp, comms.sms, comms.automations | — | — | comms_channels, comms_conversations, comms_messages |
| comms.whatsapp | p2 | comms.inbox, core.billing, core.rbac, foundation.queues | comms.broadcast | — | — | comms_whatsapp_templates, comms_whatsapp_config |
| comms.email | p2 | comms.inbox, core.billing, core.rbac, foundation.queues | — | — | — | comms_email_channels |
| comms.sms | p2 | comms.inbox, core.billing, core.rbac, foundation.queues | comms.broadcast | — | — | comms_sms_config, comms_sms_optouts |
| comms.broadcast | p2 | comms.inbox, core.billing, core.rbac, foundation.queues | crm.segments, hr.profiles, comms.whatsapp, comms.sms, core.notifications | — | — | comms_broadcasts, comms_broadcast_recipients |
| comms.automations | p2 | comms.inbox, core.billing, core.rbac, core.settings | — | — | — | comms_automation_rules, comms_chatbot_flows |
| comms.internal | p2 | core.billing, core.rbac, core.files, core.notifications | — | — | — | comms_channels_internal, comms_channel_members, comms_internal_messages |
| comms.analytics | p2 | comms.inbox, core.billing, core.rbac | comms.broadcast | — | — | — |
## Document Management (6)

| module-key | priority | deps | soft | fires | consumes | tables |
|---|---|---|---|---|---|---|
| dms.library | p2 | core.billing, core.rbac, core.files | dms.versions, dms.approvals, dms.retention | — | — | dms_folders, dms_folder_access, dms_documents |
| dms.versions | p2 | dms.library, core.billing, core.rbac | — | — | — | dms_document_versions, dms_document_locks |
| dms.wiki | p2 | core.billing, core.rbac | — | — | — | dms_wiki_pages, dms_wiki_page_versions |
| dms.templates | p2 | dms.library, core.billing, core.rbac | hr.profiles, crm.contacts | — | — | dms_templates |
| dms.approvals | p2 | dms.library, core.billing, core.rbac, core.notifications | dms.versions | — | — | dms_approval_workflows, dms_approval_requests, dms_approval_actions |
| dms.retention | p2 | dms.library, core.billing, core.rbac, core.notifications, foundation.queues | core.privacy | — | — | dms_retention_policies, dms_legal_holds, dms_retention_log |
## Marketing (7)

| module-key | priority | deps | soft | fires | consumes | tables |
|---|---|---|---|---|---|---|
| marketing.campaigns | p3 | crm.contacts, crm.segments, core.billing, core.rbac, foundation.queues, foundation.email | marketing.utm, marketing.analytics | — | — | mkt_campaigns, mkt_campaign_recipients, mkt_unsubscribes |
| marketing.forms | p3 | core.billing, core.rbac, foundation.queues | crm.contacts, marketing.sequences, marketing.landing-pages | FormSubmissionReceived | — | mkt_forms, mkt_form_submissions |
| marketing.sequences | p3 | crm.contacts, core.billing, core.rbac, foundation.queues, foundation.email | marketing.forms, crm.segments | — | FormSubmissionReceived | mkt_sequences, mkt_sequence_steps, mkt_sequence_enrolments |
| marketing.landing-pages | p3 | core.billing, core.rbac, core.files | marketing.forms, marketing.utm | — | — | mkt_landing_pages |
| marketing.cms | p3 | core.billing, core.rbac, core.files | — | — | — | mkt_posts, mkt_post_categories |
| marketing.utm | p3 | crm.contacts, core.billing, core.rbac | marketing.forms, marketing.landing-pages, crm.deals | — | FormSubmissionReceived | mkt_utm_touches |
| marketing.analytics | p3 | marketing.campaigns, core.billing, core.rbac | marketing.forms, marketing.landing-pages, marketing.sequences, marketing.utm | — | — | — |
## Operations (7)

| module-key | priority | deps | soft | fires | consumes | tables |
|---|---|---|---|---|---|---|
| operations.warehouses | p3 | core.billing, core.rbac | operations.inventory | — | — | ops_warehouses, ops_warehouse_transfers |
| operations.inventory | p3 | operations.warehouses, core.billing, core.rbac | core.import, operations.purchase-orders, operations.adjustments | — | — | ops_items, ops_stock_levels, ops_stock_movements |
| operations.suppliers | p3 | operations.inventory, core.billing, core.rbac | finance.ap | — | — | ops_suppliers, ops_supplier_items |
| operations.purchase-orders | p3 | operations.inventory, operations.suppliers, core.billing, core.rbac, foundation.queues | operations.goods-receipt, finance.ap, procurement.requisitions | — | — | ops_purchase_orders, ops_po_lines |
| operations.goods-receipt | p3 | operations.purchase-orders, operations.inventory, core.billing, core.rbac | finance.ap | GoodsReceived | — | ops_goods_receipts, ops_grn_lines |
| operations.adjustments | p3 | operations.inventory, core.billing, core.rbac | finance.ledger | — | — | ops_stock_adjustments |
| operations.reporting | p3 | operations.inventory, core.billing, core.rbac | operations.purchase-orders, operations.suppliers | — | — | — |
## Analytics & BI (5)

| module-key | priority | deps | soft | fires | consumes | tables |
|---|---|---|---|---|---|---|
| analytics.dashboards | p3 | core.billing, core.rbac | analytics.kpis, analytics.reports | — | — | bi_dashboards, bi_widgets |
| analytics.reports | p3 | core.billing, core.rbac | analytics.exports | — | — | bi_reports |
| analytics.kpis | p3 | analytics.dashboards, core.billing, core.rbac, core.notifications | projects.okrs | — | — | bi_kpis, bi_kpi_snapshots |
| analytics.data-views | p3 | analytics.dashboards, core.billing, core.rbac | — | — | — | — |
| analytics.exports | p3 | analytics.reports, core.billing, core.rbac, foundation.queues, foundation.email | analytics.dashboards, finance.reporting | — | — | bi_export_schedules, bi_export_log |
## IT & Security (6)

| module-key | priority | deps | soft | fires | consumes | tables |
|---|---|---|---|---|---|---|
| it.assets | p3 | hr.profiles, core.billing, core.rbac, core.notifications | core.import, finance.assets, it.mdm | — | EmployeeOffboarded | it_assets, it_asset_assignments |
| it.helpdesk | p3 | hr.profiles, core.billing, core.rbac, core.notifications | it.assets | — | — | it_tickets, it_ticket_replies |
| it.access | p3 | hr.profiles, core.billing, core.rbac, core.notifications | hr.onboarding | — | EmployeeHired, EmployeeOffboarded | it_systems, it_access_grants, it_access_templates |
| it.licences | p3 | hr.profiles, core.billing, core.rbac, core.notifications | finance.expenses | — | EmployeeOffboarded | it_licences, it_licence_assignments |
| it.mdm | p3 | it.assets, core.billing, core.rbac, foundation.queues | — | — | — | it_mdm_config, it_mdm_devices |
| it.reporting | p3 | it.assets, core.billing, core.rbac | it.licences, it.helpdesk, it.mdm, it.access | — | — | — |
## Legal & Compliance (6)

| module-key | priority | deps | soft | fires | consumes | tables |
|---|---|---|---|---|---|---|
| legal.contracts | p3 | core.billing, core.rbac, core.files, core.notifications | crm.contacts, operations.suppliers, legal.matters | — | — | legal_contracts, legal_contract_obligations |
| legal.matters | p3 | core.billing, core.rbac, core.files | legal.spend, legal.contracts, dms.library | — | — | legal_matters, legal_matter_events |
| legal.spend | p3 | legal.matters, core.billing, core.rbac | finance.ap | — | — | legal_expenses, legal_budgets |
| legal.policies | p3 | hr.profiles, core.billing, core.rbac, core.notifications | legal.compliance | — | — | legal_policies, legal_policy_acknowledgements |
| legal.compliance | p3 | core.billing, core.rbac, core.files, core.notifications | legal.policies, core.privacy | — | — | legal_frameworks, legal_controls, legal_compliance_tasks |
| legal.dsar | p3 | core.privacy, core.billing, core.rbac | — | — | DSARRequestSubmitted | legal_dsar_actions |
## E-commerce (8) — Wave 5
## Learning & Development (8) — Wave 5
## AI & Automation (4) — Wave 5
## Customer Success (6) — Wave 5
## Procurement (6)

| module-key | priority | deps | soft | fires | consumes | tables |
|---|---|---|---|---|---|---|
| procurement.approvals | p3 | core.billing, core.rbac, core.notifications | procurement.requisitions, procurement.purchase-orders | — | — | proc_approval_rules, proc_approval_delegations |
| procurement.requisitions | p3 | procurement.approvals, core.billing, core.rbac, core.notifications | finance.budgets, operations.purchase-orders, procurement.catalogue | — | — | proc_requisitions, proc_requisition_items, proc_requisition_approvals |
| procurement.catalogue | p3 | core.billing, core.rbac | operations.suppliers, procurement.requisitions | — | — | proc_catalogue_items, proc_supplier_status |
| procurement.purchase-orders | p3 | operations.purchase-orders, procurement.requisitions, procurement.approvals, core.billing, core.rbac | procurement.catalogue | — | — | proc_po_sourcing |
| procurement.goods-receipt | p3 | operations.goods-receipt, finance.ap, core.billing, core.rbac | — | — | — | proc_three_way_matches |
| procurement.spend | p3 | procurement.requisitions, operations.purchase-orders, core.billing, core.rbac | procurement.catalogue, finance.budgets | — | — | — |
## Workplace (5) — Wave 5
## Events Management (7) — Wave 5

---

## Dataview (Obsidian bonus — frontmatter-driven)

```dataview
TABLE module-key AS "Key", priority AS "Priority", depends-on AS "Hard deps", fires-events AS "Fires", consumes-events AS "Consumes"
FROM "domains"
WHERE type = "module"
SORT priority ASC, module-key ASC
```

---

## Related

- [[_meta/spec-template]] — frontmatter schema feeding this graph
- [[architecture/event-bus]] — event contracts
- [[build/BUILD-ORDER]] — build sequencing derived from these edges
