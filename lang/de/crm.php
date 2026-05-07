<?php

return [
    'navigation' => [
        'groups' => [
            'contacts' => 'Kontakte',
            'sales'    => 'Vertrieb',
            'support'  => 'Support',
        ],
    ],
    'resources' => [
        'crm_contacts' => [
            'label'  => 'Kontakt',
            'plural' => 'Kontakte',
            'sections' => [
                'details' => 'Kontaktdaten',
            ],
            'fields' => [
                'first_name'   => 'Vorname',
                'last_name'    => 'Nachname',
                'email'        => 'E-Mail',
                'phone'        => 'Telefon',
                'job_title'    => 'Berufsbezeichnung',
                'type'         => 'Typ',
                'company'      => 'Unternehmen',
                'tags'         => 'Tags',
                'linkedin_url' => 'LinkedIn URL',
                'notes'        => 'Notizen',
            ],
            'columns' => [
                'name'    => 'Name',
                'email'   => 'E-Mail',
                'type'    => 'Typ',
                'company' => 'Unternehmen',
                'added'   => 'Hinzugefügt',
            ],
        ],
        'crm_companies' => [
            'label'  => 'Unternehmen',
            'plural' => 'Unternehmen',
            'sections' => [
                'details' => 'Unternehmensdetails',
            ],
            'fields' => [
                'name'     => 'Name',
                'website'  => 'Website',
                'phone'    => 'Telefon',
                'industry' => 'Branche',
                'notes'    => 'Notizen',
            ],
            'columns' => [
                'name'     => 'Name',
                'website'  => 'Website',
                'industry' => 'Branche',
                'contacts' => 'Kontakte',
                'deals'    => 'Deals',
            ],
        ],
        'deals' => [
            'label'  => 'Deal',
            'plural' => 'Deals',
            'sections' => [
                'details' => 'Deal-Details',
            ],
            'fields' => [
                'title'               => 'Titel',
                'contact'             => 'Kontakt',
                'company'             => 'Unternehmen',
                'pipeline'            => 'Pipeline',
                'stage'               => 'Phase',
                'value'               => 'Wert',
                'currency'            => 'Währung',
                'status'              => 'Status',
                'close_probability'   => 'Abschlusswahrscheinlichkeit',
                'expected_close_date' => 'Erwartetes Abschlussdatum',
                'lost_reason'         => 'Verlustgrund',
            ],
            'columns' => [
                'title'               => 'Titel',
                'contact'             => 'Kontakt',
                'value'               => 'Wert',
                'status'              => 'Status',
                'stage'               => 'Phase',
                'expected_close_date' => 'Erwartetes Abschlussdatum',
                'probability'         => 'Wahrscheinlichkeit',
            ],
            'actions' => [
                'mark_won'    => 'Als gewonnen markieren',
                'mark_lost'   => 'Als verloren markieren',
                'lost_reason' => 'Verlustgrund',
            ],
        ],
        'pipelines' => [
            'label'  => 'Pipeline',
            'plural' => 'Pipelines',
            'sections' => [
                'details' => 'Pipeline-Details',
            ],
            'fields' => [
                'name'       => 'Name',
                'is_default' => 'Standard',
            ],
            'columns' => [
                'name'    => 'Name',
                'stages'  => 'Phasen',
                'deals'   => 'Deals',
                'default' => 'Standard',
            ],
        ],
        'deal_stages' => [
            'label'  => 'Deal-Phase',
            'plural' => 'Deal-Phasen',
            'sections' => [
                'details' => 'Deal-Phase',
            ],
            'fields' => [
                'pipeline'    => 'Pipeline',
                'name'        => 'Name',
                'sort_order'  => 'Sortierreihenfolge',
                'probability' => 'Wahrscheinlichkeit',
            ],
            'columns' => [
                'pipeline'    => 'Pipeline',
                'name'        => 'Name',
                'order'       => 'Reihenfolge',
                'probability' => 'Wahrscheinlichkeit',
            ],
        ],
        'tickets' => [
            'label'  => 'Ticket',
            'plural' => 'Tickets',
            'sections' => [
                'details' => 'Ticket-Details',
            ],
            'fields' => [
                'subject'       => 'Betreff',
                'contact'       => 'Kontakt',
                'company'       => 'Unternehmen',
                'priority'      => 'Priorität',
                'status'        => 'Status',
                'assigned_to'   => 'Zugewiesen an',
                'sla_breach_at' => 'SLA-Frist',
            ],
            'columns' => [
                'subject'     => 'Betreff',
                'contact'     => 'Kontakt',
                'priority'    => 'Priorität',
                'status'      => 'Status',
                'assigned_to' => 'Zugewiesen an',
                'created'     => 'Erstellt',
            ],
            'actions' => [
                'resolve' => 'Lösen',
            ],
        ],
        'canned_responses' => [
            'label'  => 'Vorgefertigte Antwort',
            'plural' => 'Vorgefertigte Antworten',
            'sections' => [
                'details' => 'Vorgefertigte Antwort',
            ],
            'fields' => [
                'title'     => 'Titel',
                'body'      => 'Inhalt',
                'is_shared' => 'Mit allen Agenten teilen',
            ],
            'columns' => [
                'title'  => 'Titel',
                'body'   => 'Inhalt',
                'shared' => 'Geteilt',
            ],
        ],
        'ticket_sla_rules' => [
            'label'  => 'SLA-Regel',
            'plural' => 'SLA-Regeln',
            'sections' => [
                'details' => 'SLA-Regeldetails',
            ],
            'fields' => [
                'name'                 => 'Name',
                'priority'             => 'Priorität',
                'first_response_hours' => 'Erste Reaktionszeit (Std.)',
                'resolution_hours'     => 'Lösungszeit (Std.)',
                'is_active'            => 'Aktiv',
            ],
            'columns' => [
                'name'                 => 'Name',
                'priority'             => 'Priorität',
                'first_response_hours' => 'Erste Reaktion (Std.)',
                'resolution_hours'     => 'Lösungszeit (Std.)',
                'is_active'            => 'Aktiv',
            ],
        ],
        'chatbot_rules' => [
            'label'  => 'Chatbot-Regel',
            'plural' => 'Chatbot-Regeln',
            'sections' => [
                'details' => 'Chatbot-Regel',
            ],
            'fields' => [
                'name'             => 'Name',
                'trigger_keywords' => 'Auslöser-Schlüsselwörter',
                'response_body'    => 'Antworttext',
                'sort_order'       => 'Sortierreihenfolge',
                'is_active'        => 'Aktiv',
            ],
            'columns' => [
                'name'      => 'Name',
                'keywords'  => 'Schlüsselwörter',
                'is_active' => 'Aktiv',
                'order'     => 'Reihenfolge',
            ],
        ],
    ],
];
