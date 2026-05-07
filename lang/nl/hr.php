<?php

return [
    'navigation' => [
        'groups' => [
            'people'      => 'Medewerkers',
            'onboarding'  => 'Onboarding',
            'leave'       => 'Verlof',
            'payroll'     => 'Verloning',
        ],
    ],
    'resources' => [
        'departments' => [
            'label'  => 'Afdeling',
            'plural' => 'Afdelingen',
            'sections' => [
                'details' => 'Afdelingsgegevens',
            ],
            'fields' => [
                'parent_department' => 'Bovenliggende afdeling',
                'manager'           => 'Manager',
            ],
            'columns' => [
                'parent'  => 'Bovenliggende',
                'manager' => 'Manager',
            ],
        ],
        'employees' => [
            'label'  => 'Medewerker',
            'plural' => 'Medewerkers',
            'sections' => [
                'personal_details'   => 'Persoonlijke gegevens',
                'employment'         => 'Dienstverband',
                'emergency_contact'  => 'Noodcontact',
            ],
            'fields' => [
                'department'                   => 'Afdeling',
                'manager'                      => 'Manager',
                'contracted_hours_per_week'    => 'Contracturen / week',
                'emergency_contact_name'       => 'Naam',
                'emergency_contact_phone'      => 'Telefoon',
                'emergency_contact_relationship' => 'Relatie',
            ],
            'columns' => [
                'name'       => 'Naam',
                'department' => 'Afdeling',
                'type'       => 'Type',
                'status'     => 'Status',
                'start_date' => 'Startdatum',
            ],
        ],
        'leave_types' => [
            'label'  => 'Verlofsoort',
            'plural' => 'Verlofsoorten',
            'sections' => [
                'details' => 'Details verlofsoort',
            ],
            'fields' => [
                'paid_leave'        => 'Betaald verlof',
                'requires_approval' => 'Goedkeuring vereist',
                'allow_half_day'    => 'Halve dag toestaan',
                'min_notice_days'   => 'Minimale opzegtermijn (dagen)',
                'is_active'         => 'Actief',
            ],
            'columns' => [
                'paid'              => 'Betaald',
                'approval_required' => 'Goedkeuring vereist',
                'is_active'         => 'Actief',
            ],
        ],
        'leave_requests' => [
            'label'  => 'Verlofaanvraag',
            'plural' => 'Verlofaanvragen',
            'sections' => [
                'details' => 'Verlofaanvraaggegevens',
            ],
            'fields' => [
                'employee'         => 'Medewerker',
                'leave_type'       => 'Verlofsoort',
                'half_day'         => 'Halve dag',
                'rejection_reason' => 'Reden afwijzing',
            ],
            'columns' => [
                'employee'   => 'Medewerker',
                'leave_type' => 'Verlofsoort',
                'start'      => 'Begindatum',
                'end'        => 'Einddatum',
                'days'       => 'Dagen',
            ],
            'actions' => [
                'approve' => 'Goedkeuren',
                'reject'  => 'Afwijzen',
            ],
        ],
        'onboarding_templates' => [
            'label'  => 'Onboardingsjabloon',
            'plural' => 'Onboardingsjablonen',
            'sections' => [
                'details' => 'Sjabloongegevens',
            ],
            'fields' => [
                'is_active' => 'Actief',
            ],
            'columns' => [
                'is_active' => 'Actief',
            ],
            'relation_managers' => [
                'tasks' => [
                    'title' => 'Taken',
                    'sections' => [
                        'details' => 'Taakgegevens',
                    ],
                    'fields' => [
                        'task_type'       => 'Taaktype',
                        'due_day_offset'  => 'Vervaldatum (dagen na start)',
                    ],
                    'columns' => [
                        'order'          => '#',
                        'type'           => 'Type',
                        'due_day_offset' => 'Vervaldatum (dagen na start)',
                    ],
                ],
            ],
        ],
        'onboarding_flows' => [
            'label'  => 'Onboardingproces',
            'plural' => 'Onboardingprocessen',
            'sections' => [
                'details' => 'Procesgegevens',
            ],
            'fields' => [
                'employee' => 'Medewerker',
                'template' => 'Sjabloon',
            ],
            'columns' => [
                'employee' => 'Medewerker',
                'started'  => 'Gestart op',
            ],
        ],
        'payroll_entities' => [
            'label'  => 'Salarisadministratie-entiteit',
            'plural' => 'Salarisadministratie-entiteiten',
            'sections' => [
                'details' => 'Entiteitsgegevens',
            ],
            'fields' => [
                'display_name'   => 'Weergavenaam',
                'legal_name'     => 'Officiële naam',
                'country_code'   => 'Landcode',
                'tax_reference'  => 'Belastingreferentie',
                'default_entity' => 'Standaard entiteit',
            ],
            'columns' => [
                'legal_name'    => 'Officiële naam',
                'country'       => 'Land',
                'tax_reference' => 'Belastingreferentie',
                'is_default'    => 'Standaard',
            ],
        ],
        'pay_elements' => [
            'label'  => 'Loonelement',
            'plural' => 'Loonelementen',
            'sections' => [
                'details' => 'Loonaelementgegevens',
            ],
            'fields' => [
                'element_type'  => 'Elementtype',
                'is_taxable'    => 'Belastbaar',
                'is_pensionable' => 'Pensioengevend',
                'is_active'     => 'Actief',
            ],
            'columns' => [
                'type'      => 'Type',
                'is_taxable' => 'Belastbaar',
                'is_active' => 'Actief',
            ],
        ],
        'pay_runs' => [
            'label'  => 'Loonronde',
            'plural' => 'Loonronden',
            'sections' => [
                'details' => 'Loonrondegegevens',
            ],
            'fields' => [
                'payroll_entity' => 'Salarisadministratie-entiteit',
                'period_start'   => 'Periode begin',
                'period_end'     => 'Periode einde',
                'payment_date'   => 'Betaaldatum',
            ],
            'columns' => [
                'period_start'  => 'Periode begin',
                'period_end'    => 'Periode einde',
                'payment_date'  => 'Betaaldatum',
                'frequency'     => 'Frequentie',
                'gross'         => 'Bruto',
                'net'           => 'Netto',
            ],
            'filters' => [
                'frequency' => 'Frequentie',
            ],
        ],
        'payslips' => [
            'label'  => 'Loonstrook',
            'plural' => 'Loonstroken',
            'columns' => [
                'employee'    => 'Medewerker',
                'pay_run'     => 'Loonronde',
                'period_start' => 'Periode begin',
                'period_end'   => 'Periode einde',
                'generated'   => 'Gegenereerd',
                'sent'        => 'Verzonden',
            ],
        ],
        'salary_records' => [
            'label'  => 'Salarisrecord',
            'plural' => 'Salarisrecords',
            'sections' => [
                'details' => 'Salarisgegevens',
            ],
            'fields' => [
                'employee'       => 'Medewerker',
                'effective_from' => 'Geldig vanaf',
                'effective_to'   => 'Geldig tot',
                'annual_salary'  => 'Jaarsalaris',
                'currency'       => 'Valuta',
            ],
            'columns' => [
                'employee'       => 'Medewerker',
                'effective_from' => 'Geldig vanaf',
                'frequency'      => 'Frequentie',
                'annual_salary'  => 'Jaarsalaris',
                'currency'       => 'Valuta',
            ],
        ],
        'contractor_payments' => [
            'label'  => 'Aannemersbetaling',
            'plural' => 'Aannemersbetalingen',
            'sections' => [
                'details' => 'Betalingsgegevens',
            ],
            'fields' => [
                'contractor_employee' => 'Aannemer / Medewerker',
                'pay_run'             => 'Loonronde',
            ],
            'columns' => [
                'contractor'  => 'Aannemer',
                'pay_run'     => 'Loonronde',
                'processed'   => 'Verwerkt op',
            ],
        ],
        'deductions' => [
            'label'  => 'Inhouding',
            'plural' => 'Inhoudingen',
            'sections' => [
                'details' => 'Inhoudingsgegevens',
            ],
            'fields' => [
                'employee'        => 'Medewerker',
                'pay_element'     => 'Loonelement',
                'deduction_type'  => 'Type inhouding',
                'is_percentage'   => 'Percentage?',
                'is_recurring'    => 'Terugkerend',
                'effective_from'  => 'Geldig vanaf',
                'effective_to'    => 'Geldig tot',
            ],
            'columns' => [
                'employee'       => 'Medewerker',
                'type'           => 'Type',
                'is_percentage'  => '% Gebaseerd',
                'is_recurring'   => 'Terugkerend',
                'from'           => 'Vanaf',
                'to'             => 'Tot',
            ],
        ],
        'public_holidays' => [
            'label'  => 'Officiële feestdag',
            'plural' => 'Officiële feestdagen',
            'sections' => [
                'details' => 'Feestdaggegevens',
            ],
            'fields' => [
                'country_code'  => 'Landcode',
                'is_recurring'  => 'Jaarlijks terugkerend',
            ],
            'columns' => [
                'country'      => 'Land',
                'is_recurring' => 'Terugkerend',
            ],
            'filters' => [
                'country' => 'Land',
            ],
        ],
        'employee_documents' => [
            'title' => 'Documenten',
            'sections' => [
                'details' => 'Documentgegevens',
            ],
        ],
    ],
];
