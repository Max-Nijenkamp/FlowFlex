// Product + domain-detail content — source of truth:
// vault/product/design_handoff_flowflex_site 2/pages/product.jsx
import type { Flow } from './marketing'

export interface ProductDomain {
    key: string
    name: string
    tagline: string
    desc: string
    lede: string
    mods: Array<[string, string]> // [name, price-label]
    flowBullets: string[]
    detailModules: Array<{ name: string; price: string; on: boolean; desc: string }>
    detailFlows: Flow[]
    playsWellWith: Array<[string, string]> // [domain-key, label]
    ctaTitle: string
    ctaSub: string
}

export const productDomains: ProductDomain[] = [
    {
        key: 'hr',
        name: 'HR & people',
        tagline: 'Recruiting to payroll, one record.',
        desc: 'Every employee is one profile shared by leave, payroll, onboarding and reviews. Approve a holiday and scheduling already knows.',
        lede: 'Recruiting to payroll on one employee record. Six modules, each its own switch — most teams start with two.',
        mods: [['Employee profiles', 'included'], ['Leave & absence', '€1,50'], ['Payroll', '€2,50'], ['Recruiting', '€1,50'], ['Onboarding', '€1,00'], ['Time tracking', '€1,00']],
        flowBullets: ['Offer accepted → salary lands in the next payroll run', 'Leave approved → shifts unassign, coverage flagged'],
        detailModules: [
            { name: 'Employee profiles', price: 'included', on: true, desc: 'One record per person — contracts, documents, history.' },
            { name: 'Leave & absence', price: '€1,50', on: true, desc: 'Requests, balances, approval chains, team calendar.' },
            { name: 'Payroll', price: '€2,50', on: false, desc: 'Salary runs that read contracts and approved leave.' },
            { name: 'Recruiting', price: '€1,50', on: false, desc: 'Vacancies, candidate pipeline, structured scoring.' },
            { name: 'Onboarding', price: '€1,00', on: false, desc: 'Checklists that provision IT, LMS and payroll in one go.' },
            { name: 'Time tracking', price: '€1,00', on: false, desc: 'Hours that flow into payroll and project billing.' },
        ],
        detailFlows: [
            { from: 'HR', to: 'Payroll', event: 'Offer accepted', effect: 'The salary lands in the next payroll run' },
            { from: 'HR', to: 'Scheduling', event: 'Leave approved', effect: 'Shifts unassign and coverage gaps get flagged' },
            { from: 'HR', to: 'IT', event: 'Onboarding started', effect: 'Accounts and hardware provisioning kick off' },
            { from: 'LMS', to: 'HR', event: 'Course completed', effect: 'The certification shows on the employee profile' },
        ],
        playsWellWith: [['finance', 'Finance — payroll & expense flows'], ['projects', 'Projects — capacity & time'], ['lms', 'Learning — certifications'], ['it', 'IT — provisioning']],
        ctaTitle: 'Start with HR. Grow from there.',
        ctaSub: "Employee profiles are free — add leave for €1,50 per person and you're running.",
    },
    {
        key: 'finance',
        name: 'Finance & accounting',
        tagline: 'Ledger-first books.',
        desc: 'Invoices, expenses and reporting on the same ledger your CRM and projects write to. Nothing is imported, so nothing is stale.',
        lede: 'Invoices, expenses and reporting on one ledger. Four modules, each its own switch.',
        mods: [['Invoicing', '€2,00'], ['Expenses', '€1,00'], ['AP / AR', '€1,50'], ['Reporting', '€1,00']],
        flowBullets: ['Deal won → draft invoice with the deal value on it', 'Invoice paid → account lifetime value updates'],
        detailModules: [
            { name: 'Invoicing', price: '€2,00', on: true, desc: 'Gap-free numbering, VAT, recurring runs, payment tracking.' },
            { name: 'Expenses', price: '€1,00', on: true, desc: 'Receipts, categories, approval chains, reimbursement.' },
            { name: 'AP / AR', price: '€1,50', on: false, desc: 'Supplier bills and receivables on the same ledger.' },
            { name: 'Reporting', price: '€1,00', on: false, desc: 'Trial balance, P&L and VAT views that are never stale.' },
        ],
        detailFlows: [
            { from: 'CRM', to: 'Finance', event: 'Deal won', effect: 'A draft invoice appears with the deal value already on it' },
            { from: 'Finance', to: 'CRM', event: 'Invoice paid', effect: 'Account lifetime value updates on its own' },
            { from: 'Projects', to: 'Finance', event: 'Hours logged', effect: 'Invoice lines drafted for billable work' },
            { from: 'HR', to: 'Finance', event: 'Expense approved', effect: 'The reimbursement posts to the ledger' },
        ],
        playsWellWith: [['crm', 'CRM — deal-to-invoice'], ['projects', 'Projects — billable hours'], ['hr', 'HR — payroll & expenses'], ['analytics', 'Analytics — live P&L']],
        ctaTitle: 'Books that write themselves.',
        ctaSub: 'Invoicing is €2,00 per person per month — and the ledger underneath is free.',
    },
    {
        key: 'crm',
        name: 'CRM & sales',
        tagline: 'Pipeline to contract.',
        desc: 'Contacts, deals and pipeline that see support tickets, invoices and projects — because they live in the same database.',
        lede: 'Contacts, deals and pipeline with real context from support, finance and projects.',
        mods: [['Contacts', 'included'], ['Pipeline', '€1,50'], ['Deals & quotes', '€1,50']],
        flowBullets: ['Ticket spike → account health drops before renewal', 'Quote signed → project kickoff scaffolded'],
        detailModules: [
            { name: 'Contacts', price: 'included', on: true, desc: 'People and accounts, deduplicated, shared with every module.' },
            { name: 'Pipeline', price: '€1,50', on: true, desc: 'Drag-and-drop stages, weighted forecast, quick-add.' },
            { name: 'Deals & quotes', price: '€1,50', on: false, desc: 'Quotes that become invoices the moment a deal is won.' },
        ],
        detailFlows: [
            { from: 'Support', to: 'CRM', event: 'Ticket spike', effect: 'The account health score drops before renewal talks' },
            { from: 'CRM', to: 'Finance', event: 'Deal won', effect: 'A draft invoice appears with the deal value already on it' },
            { from: 'CRM', to: 'Projects', event: 'Quote signed', effect: 'Project kickoff is scaffolded from the deal' },
            { from: 'Finance', to: 'CRM', event: 'Invoice paid', effect: 'Account lifetime value updates on its own' },
        ],
        playsWellWith: [['finance', 'Finance — invoices & LTV'], ['support', 'Support — account health'], ['projects', 'Projects — delivery'], ['marketing', 'Marketing — campaigns']],
        ctaTitle: 'A pipeline with peripheral vision.',
        ctaSub: 'Contacts are free — add pipeline for €1,50 per person and see everything.',
    },
    {
        key: 'projects',
        name: 'Projects & work',
        tagline: 'Boards, sprints, time.',
        desc: 'Kanban, sprints and time tracking with real awareness of who is on leave, what was invoiced, and which deal this work came from.',
        lede: 'Boards, sprints and time tracking that know about leave, deals and invoices.',
        mods: [['Projects & boards', '€1,50'], ['Sprints', '€1,00'], ['Time tracking', '€1,00']],
        flowBullets: ['Hours logged → invoice lines drafted for billable work', 'Leave approved → assignments flagged for handover'],
        detailModules: [
            { name: 'Projects & boards', price: '€1,50', on: true, desc: 'Kanban boards, milestones, cross-team visibility.' },
            { name: 'Sprints', price: '€1,00', on: false, desc: 'Sprint planning with real capacity from the HR calendar.' },
            { name: 'Time tracking', price: '€1,00', on: false, desc: 'Hours that flow into payroll and project billing.' },
        ],
        detailFlows: [
            { from: 'Projects', to: 'Finance', event: 'Hours logged', effect: 'Invoice lines drafted for billable work' },
            { from: 'HR', to: 'Projects', event: 'Leave approved', effect: 'Assignments flagged for handover' },
            { from: 'CRM', to: 'Projects', event: 'Quote signed', effect: 'Project kickoff scaffolded from the deal' },
            { from: 'Projects', to: 'CRM', event: 'Milestone shipped', effect: 'The account timeline shows delivery progress' },
        ],
        playsWellWith: [['hr', 'HR — capacity & leave'], ['finance', 'Finance — billable hours'], ['crm', 'CRM — deal context'], ['dms', 'Documents — deliverables']],
        ctaTitle: 'Work that knows its context.',
        ctaSub: 'Boards are €1,50 per person per month. Capacity awareness comes free.',
    },
]
