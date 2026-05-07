<?php

return [
    'navigation' => [
        'groups' => [
            'contacts' => 'Contacten',
            'sales'    => 'Verkoop',
            'support'  => 'Ondersteuning',
        ],
    ],
    'resources' => [
        'crm_contacts' => [
            'label'  => 'Contact',
            'plural' => 'Contacten',
            'sections' => [
                'details' => 'Contactgegevens',
            ],
            'fields' => [
                'first_name'   => 'Voornaam',
                'last_name'    => 'Achternaam',
                'email'        => 'E-mail',
                'phone'        => 'Telefoon',
                'job_title'    => 'Functietitel',
                'type'         => 'Type',
                'company'      => 'Bedrijf',
                'tags'         => 'Tags',
                'linkedin_url' => 'LinkedIn URL',
                'notes'        => 'Notities',
            ],
            'columns' => [
                'name'    => 'Naam',
                'email'   => 'E-mail',
                'type'    => 'Type',
                'company' => 'Bedrijf',
                'added'   => 'Toegevoegd',
            ],
        ],
        'crm_companies' => [
            'label'  => 'Bedrijf',
            'plural' => 'Bedrijven',
            'sections' => [
                'details' => 'Bedrijfsgegevens',
            ],
            'fields' => [
                'name'     => 'Naam',
                'website'  => 'Website',
                'phone'    => 'Telefoon',
                'industry' => 'Sector',
                'notes'    => 'Notities',
            ],
            'columns' => [
                'name'          => 'Naam',
                'website'       => 'Website',
                'industry'      => 'Sector',
                'contacts'      => 'Contacten',
                'deals'         => 'Deals',
            ],
        ],
        'deals' => [
            'label'  => 'Deal',
            'plural' => 'Deals',
            'sections' => [
                'details' => 'Dealgegevens',
            ],
            'fields' => [
                'title'               => 'Titel',
                'contact'             => 'Contact',
                'company'             => 'Bedrijf',
                'pipeline'            => 'Pipeline',
                'stage'               => 'Fase',
                'value'               => 'Waarde',
                'currency'            => 'Valuta',
                'status'              => 'Status',
                'close_probability'   => 'Kanspercentage',
                'expected_close_date' => 'Verwachte sluitingsdatum',
                'lost_reason'         => 'Reden verloren',
            ],
            'columns' => [
                'title'               => 'Titel',
                'contact'             => 'Contact',
                'value'               => 'Waarde',
                'status'              => 'Status',
                'stage'               => 'Fase',
                'expected_close_date' => 'Verwachte sluitingsdatum',
                'probability'         => 'Kans',
            ],
            'actions' => [
                'mark_won'  => 'Gewonnen markeren',
                'mark_lost' => 'Verloren markeren',
                'lost_reason' => 'Reden verloren',
            ],
        ],
        'pipelines' => [
            'label'  => 'Pipeline',
            'plural' => 'Pipelines',
            'sections' => [
                'details' => 'Pipelinegegevens',
            ],
            'fields' => [
                'name'       => 'Naam',
                'is_default' => 'Standaard',
            ],
            'columns' => [
                'name'    => 'Naam',
                'stages'  => 'Fases',
                'deals'   => 'Deals',
                'default' => 'Standaard',
            ],
        ],
        'deal_stages' => [
            'label'  => 'Dealfase',
            'plural' => 'Dealfases',
            'sections' => [
                'details' => 'Dealfase',
            ],
            'fields' => [
                'pipeline'    => 'Pipeline',
                'name'        => 'Naam',
                'sort_order'  => 'Sorteervolgorde',
                'probability' => 'Kanspercentage',
            ],
            'columns' => [
                'pipeline'    => 'Pipeline',
                'name'        => 'Naam',
                'order'       => 'Volgorde',
                'probability' => 'Kans',
            ],
        ],
        'tickets' => [
            'label'  => 'Ticket',
            'plural' => 'Tickets',
            'sections' => [
                'details' => 'Ticketgegevens',
            ],
            'fields' => [
                'subject'      => 'Onderwerp',
                'contact'      => 'Contact',
                'company'      => 'Bedrijf',
                'priority'     => 'Prioriteit',
                'status'       => 'Status',
                'assigned_to'  => 'Toegewezen aan',
                'sla_breach_at' => 'SLA-deadline',
            ],
            'columns' => [
                'subject'     => 'Onderwerp',
                'contact'     => 'Contact',
                'priority'    => 'Prioriteit',
                'status'      => 'Status',
                'assigned_to' => 'Toegewezen aan',
                'created'     => 'Aangemaakt',
            ],
            'actions' => [
                'resolve' => 'Oplossen',
            ],
        ],
        'canned_responses' => [
            'label'  => 'Standaardantwoord',
            'plural' => 'Standaardantwoorden',
            'sections' => [
                'details' => 'Standaardantwoord',
            ],
            'fields' => [
                'title'     => 'Titel',
                'body'      => 'Inhoud',
                'is_shared' => 'Gedeeld met alle agenten',
            ],
            'columns' => [
                'title'     => 'Titel',
                'body'      => 'Inhoud',
                'shared'    => 'Gedeeld',
            ],
        ],
        'ticket_sla_rules' => [
            'label'  => 'SLA-regel',
            'plural' => 'SLA-regels',
            'sections' => [
                'details' => 'SLA-regeldetails',
            ],
            'fields' => [
                'name'                  => 'Naam',
                'priority'              => 'Prioriteit',
                'first_response_hours'  => 'Eerste responstijd (uren)',
                'resolution_hours'      => 'Oplostijd (uren)',
                'is_active'             => 'Actief',
            ],
            'columns' => [
                'name'                  => 'Naam',
                'priority'              => 'Prioriteit',
                'first_response_hours'  => 'Eerste respons (u)',
                'resolution_hours'      => 'Oplostijd (u)',
                'is_active'             => 'Actief',
            ],
        ],
        'chatbot_rules' => [
            'label'  => 'Chatbotregel',
            'plural' => 'Chatbotregels',
            'sections' => [
                'details' => 'Chatbotregel',
            ],
            'fields' => [
                'name'             => 'Naam',
                'trigger_keywords' => 'Activeringssleutelwoorden',
                'response_body'    => 'Antwoord',
                'sort_order'       => 'Sorteervolgorde',
                'is_active'        => 'Actief',
            ],
            'columns' => [
                'name'     => 'Naam',
                'keywords' => 'Sleutelwoorden',
                'is_active' => 'Actief',
                'order'    => 'Volgorde',
            ],
        ],
    ],
];
