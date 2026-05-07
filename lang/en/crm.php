<?php

return [
    'navigation' => [
        'groups' => [
            'contacts' => 'Contacts',
            'sales'    => 'Sales',
            'support'  => 'Support',
        ],
    ],
    'resources' => [
        'crm_contacts' => [
            'label'  => 'Contact',
            'plural' => 'Contacts',
            'sections' => [
                'details' => 'Contact Details',
            ],
            'fields' => [
                'first_name'   => 'First Name',
                'last_name'    => 'Last Name',
                'email'        => 'Email',
                'phone'        => 'Phone',
                'job_title'    => 'Job Title',
                'type'         => 'Type',
                'company'      => 'Company',
                'tags'         => 'Tags',
                'linkedin_url' => 'LinkedIn URL',
                'notes'        => 'Notes',
            ],
            'columns' => [
                'name'    => 'Name',
                'email'   => 'Email',
                'type'    => 'Type',
                'company' => 'Company',
                'added'   => 'Added',
            ],
        ],
        'crm_companies' => [
            'label'  => 'Company',
            'plural' => 'Companies',
            'sections' => [
                'details' => 'Company Details',
            ],
            'fields' => [
                'name'     => 'Name',
                'website'  => 'Website',
                'phone'    => 'Phone',
                'industry' => 'Industry',
                'notes'    => 'Notes',
            ],
            'columns' => [
                'name'     => 'Name',
                'website'  => 'Website',
                'industry' => 'Industry',
                'contacts' => 'Contacts',
                'deals'    => 'Deals',
            ],
        ],
        'deals' => [
            'label'  => 'Deal',
            'plural' => 'Deals',
            'sections' => [
                'details' => 'Deal Details',
            ],
            'fields' => [
                'title'               => 'Title',
                'contact'             => 'Contact',
                'company'             => 'Company',
                'pipeline'            => 'Pipeline',
                'stage'               => 'Stage',
                'value'               => 'Value',
                'currency'            => 'Currency',
                'status'              => 'Status',
                'close_probability'   => 'Close Probability',
                'expected_close_date' => 'Expected Close Date',
                'lost_reason'         => 'Lost Reason',
            ],
            'columns' => [
                'title'               => 'Title',
                'contact'             => 'Contact',
                'value'               => 'Value',
                'status'              => 'Status',
                'stage'               => 'Stage',
                'expected_close_date' => 'Expected Close Date',
                'probability'         => 'Probability',
            ],
            'actions' => [
                'mark_won'    => 'Mark Won',
                'mark_lost'   => 'Mark Lost',
                'lost_reason' => 'Lost Reason',
            ],
        ],
        'pipelines' => [
            'label'  => 'Pipeline',
            'plural' => 'Pipelines',
            'sections' => [
                'details' => 'Pipeline Details',
            ],
            'fields' => [
                'name'       => 'Name',
                'is_default' => 'Default',
            ],
            'columns' => [
                'name'    => 'Name',
                'stages'  => 'Stages',
                'deals'   => 'Deals',
                'default' => 'Default',
            ],
        ],
        'deal_stages' => [
            'label'  => 'Deal Stage',
            'plural' => 'Deal Stages',
            'sections' => [
                'details' => 'Deal Stage',
            ],
            'fields' => [
                'pipeline'    => 'Pipeline',
                'name'        => 'Name',
                'sort_order'  => 'Sort Order',
                'probability' => 'Probability',
            ],
            'columns' => [
                'pipeline'    => 'Pipeline',
                'name'        => 'Name',
                'order'       => 'Order',
                'probability' => 'Probability',
            ],
        ],
        'tickets' => [
            'label'  => 'Ticket',
            'plural' => 'Tickets',
            'sections' => [
                'details' => 'Ticket Details',
            ],
            'fields' => [
                'subject'       => 'Subject',
                'contact'       => 'Contact',
                'company'       => 'Company',
                'priority'      => 'Priority',
                'status'        => 'Status',
                'assigned_to'   => 'Assigned To',
                'sla_breach_at' => 'SLA Breach At',
            ],
            'columns' => [
                'subject'     => 'Subject',
                'contact'     => 'Contact',
                'priority'    => 'Priority',
                'status'      => 'Status',
                'assigned_to' => 'Assigned To',
                'created'     => 'Created',
            ],
            'actions' => [
                'resolve' => 'Resolve',
            ],
        ],
        'canned_responses' => [
            'label'  => 'Canned Response',
            'plural' => 'Canned Responses',
            'sections' => [
                'details' => 'Canned Response',
            ],
            'fields' => [
                'title'     => 'Title',
                'body'      => 'Body',
                'is_shared' => 'Shared with all agents',
            ],
            'columns' => [
                'title'  => 'Title',
                'body'   => 'Body',
                'shared' => 'Shared',
            ],
        ],
        'ticket_sla_rules' => [
            'label'  => 'SLA Rule',
            'plural' => 'SLA Rules',
            'sections' => [
                'details' => 'SLA Rule Details',
            ],
            'fields' => [
                'name'                 => 'Name',
                'priority'             => 'Priority',
                'first_response_hours' => 'First Response Hours',
                'resolution_hours'     => 'Resolution Hours',
                'is_active'            => 'Active',
            ],
            'columns' => [
                'name'                 => 'Name',
                'priority'             => 'Priority',
                'first_response_hours' => 'First Response (h)',
                'resolution_hours'     => 'Resolution (h)',
                'is_active'            => 'Active',
            ],
        ],
        'chatbot_rules' => [
            'label'  => 'Chatbot Rule',
            'plural' => 'Chatbot Rules',
            'sections' => [
                'details' => 'Chatbot Rule Details',
            ],
            'fields' => [
                'name'             => 'Name',
                'trigger_keywords' => 'Trigger Keywords',
                'response_body'    => 'Response Body',
                'sort_order'       => 'Sort Order',
                'is_active'        => 'Active',
            ],
            'columns' => [
                'name'      => 'Name',
                'keywords'  => 'Keywords',
                'is_active' => 'Active',
                'order'     => 'Order',
            ],
        ],
    ],
];
