// Shared Switchboard+ marketing content — sourced from vault/product/*.md
// and the design handoff bundle (design_handoff_flowflex_site/directions/data.js).

export const domainColors: Record<string, string> = {
    hr: '#8B5CF6',
    finance: '#10B981',
    crm: '#F43F5E',
    projects: '#6366F1',
    comms: '#3B82F6',
    support: '#F97316',
    dms: '#64748B',
    marketing: '#EC4899',
    operations: '#FB923C',
    analytics: '#38BDF8',
    it: '#06B6D4',
    legal: '#F59E0B',
    ecommerce: '#14B8A6',
    lms: '#22C55E',
    ai: '#818CF8',
    workplace: '#84CC16',
    events: '#FB7185',
}

export interface MarketingFlow {
    from?: string
    to?: string
    event: string
    effect: string
}

export const flows: MarketingFlow[] = [
    { from: 'CRM', to: 'Finance', event: 'Deal won', effect: 'A draft invoice appears with the deal value already on it' },
    { from: 'Finance', to: 'CRM', event: 'Invoice paid', effect: 'Account lifetime value updates on its own' },
    { from: 'HR', to: 'Payroll', event: 'Offer accepted', effect: 'The salary lands in the next payroll run' },
    { from: 'HR', to: 'Scheduling', event: 'Leave approved', effect: 'Shifts unassign and coverage gaps get flagged' },
    { from: 'Support', to: 'CRM', event: 'Ticket spike', effect: 'The account health score drops before renewal talks' },
    { from: 'LMS', to: 'HR', event: 'Course completed', effect: 'The certification shows on the employee profile' },
]

export const replaces = [
    'BambooHR', 'Asana', 'Xero', 'HubSpot', 'Mailchimp', 'Notion', 'Zapier', 'Freshdesk',
    'TalentLMS', 'Monday.com', 'QuickBooks', 'Salesforce', 'Intercom', 'Brevo', 'Jira',
]

export const domains: { key: string; name: string; modules: number }[] = [
    { key: 'hr', name: 'HR & people', modules: 8 },
    { key: 'finance', name: 'Finance & accounting', modules: 7 },
    { key: 'crm', name: 'CRM & sales', modules: 6 },
    { key: 'projects', name: 'Projects & work', modules: 6 },
    { key: 'comms', name: 'Communications', modules: 4 },
    { key: 'support', name: 'Support & help desk', modules: 4 },
    { key: 'dms', name: 'Documents', modules: 3 },
    { key: 'marketing', name: 'Marketing', modules: 5 },
    { key: 'operations', name: 'Operations', modules: 5 },
    { key: 'analytics', name: 'Analytics & BI', modules: 3 },
    { key: 'it', name: 'IT & security', modules: 4 },
    { key: 'legal', name: 'Legal & compliance', modules: 3 },
    { key: 'ecommerce', name: 'E-commerce', modules: 4 },
    { key: 'lms', name: 'Learning & dev', modules: 4 },
    { key: 'ai', name: 'AI & automation', modules: 3 },
    { key: 'workplace', name: 'Workplace', modules: 3 },
]

export const euro = (cents: number): string => '€' + (cents / 100).toFixed(2).replace('.', ',')

export const euroShort = (cents: number): string => '€' + Math.round(cents / 100).toLocaleString('nl-NL')
