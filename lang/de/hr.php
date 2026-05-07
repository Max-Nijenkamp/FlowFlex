<?php

return [
    'navigation' => [
        'groups' => [
            'people'      => 'Mitarbeiter',
            'onboarding'  => 'Onboarding',
            'leave'       => 'Urlaub',
            'payroll'     => 'Lohnabrechnung',
        ],
    ],
    'resources' => [
        'departments' => [
            'label'  => 'Abteilung',
            'plural' => 'Abteilungen',
            'sections' => [
                'details' => 'Abteilungsdetails',
            ],
            'fields' => [
                'parent_department' => 'Übergeordnete Abteilung',
                'manager'           => 'Manager',
            ],
            'columns' => [
                'parent'  => 'Übergeordnete',
                'manager' => 'Manager',
            ],
        ],
        'employees' => [
            'label'  => 'Mitarbeiter',
            'plural' => 'Mitarbeiter',
            'sections' => [
                'personal_details'   => 'Persönliche Angaben',
                'employment'         => 'Beschäftigung',
                'emergency_contact'  => 'Notfallkontakt',
            ],
            'fields' => [
                'department'                     => 'Abteilung',
                'manager'                        => 'Manager',
                'contracted_hours_per_week'      => 'Vertragsstunden / Woche',
                'emergency_contact_name'         => 'Name',
                'emergency_contact_phone'        => 'Telefon',
                'emergency_contact_relationship' => 'Beziehung',
            ],
            'columns' => [
                'name'       => 'Name',
                'department' => 'Abteilung',
                'type'       => 'Typ',
                'status'     => 'Status',
                'start_date' => 'Startdatum',
            ],
        ],
        'leave_types' => [
            'label'  => 'Urlaubsart',
            'plural' => 'Urlaubsarten',
            'sections' => [
                'details' => 'Details zur Urlaubsart',
            ],
            'fields' => [
                'paid_leave'        => 'Bezahlter Urlaub',
                'requires_approval' => 'Genehmigung erforderlich',
                'allow_half_day'    => 'Halbtag erlauben',
                'min_notice_days'   => 'Mindestankündigungsfrist (Tage)',
                'is_active'         => 'Aktiv',
            ],
            'columns' => [
                'paid'              => 'Bezahlt',
                'approval_required' => 'Genehmigung erforderlich',
                'is_active'         => 'Aktiv',
            ],
        ],
        'leave_requests' => [
            'label'  => 'Urlaubsantrag',
            'plural' => 'Urlaubsanträge',
            'sections' => [
                'details' => 'Urlaubsantragdetails',
            ],
            'fields' => [
                'employee'         => 'Mitarbeiter',
                'leave_type'       => 'Urlaubsart',
                'half_day'         => 'Halbtag',
                'rejection_reason' => 'Ablehnungsgrund',
            ],
            'columns' => [
                'employee'   => 'Mitarbeiter',
                'leave_type' => 'Urlaubsart',
                'start'      => 'Startdatum',
                'end'        => 'Enddatum',
                'days'       => 'Tage',
            ],
            'actions' => [
                'approve' => 'Genehmigen',
                'reject'  => 'Ablehnen',
            ],
        ],
        'onboarding_templates' => [
            'label'  => 'Onboarding-Vorlage',
            'plural' => 'Onboarding-Vorlagen',
            'sections' => [
                'details' => 'Vorlagendetails',
            ],
            'fields' => [
                'is_active' => 'Aktiv',
            ],
            'columns' => [
                'is_active' => 'Aktiv',
            ],
            'relation_managers' => [
                'tasks' => [
                    'title' => 'Aufgaben',
                    'sections' => [
                        'details' => 'Aufgabendetails',
                    ],
                    'fields' => [
                        'task_type'      => 'Aufgabentyp',
                        'due_day_offset' => 'Fällig (Tage nach Start)',
                    ],
                    'columns' => [
                        'order'          => '#',
                        'type'           => 'Typ',
                        'due_day_offset' => 'Fällig (Tage nach Start)',
                    ],
                ],
            ],
        ],
        'onboarding_flows' => [
            'label'  => 'Onboarding-Prozess',
            'plural' => 'Onboarding-Prozesse',
            'sections' => [
                'details' => 'Prozessdetails',
            ],
            'fields' => [
                'employee' => 'Mitarbeiter',
                'template' => 'Vorlage',
            ],
            'columns' => [
                'employee' => 'Mitarbeiter',
                'started'  => 'Gestartet am',
            ],
        ],
        'payroll_entities' => [
            'label'  => 'Lohnabrechnungsentität',
            'plural' => 'Lohnabrechnungsentitäten',
            'sections' => [
                'details' => 'Entitätsdetails',
            ],
            'fields' => [
                'display_name'   => 'Anzeigename',
                'legal_name'     => 'Offizieller Name',
                'country_code'   => 'Ländercode',
                'tax_reference'  => 'Steuerreferenz',
                'default_entity' => 'Standardentität',
            ],
            'columns' => [
                'legal_name'    => 'Offizieller Name',
                'country'       => 'Land',
                'tax_reference' => 'Steuerreferenz',
                'is_default'    => 'Standard',
            ],
        ],
        'pay_elements' => [
            'label'  => 'Lohnelement',
            'plural' => 'Lohnelemente',
            'sections' => [
                'details' => 'Lohnelementdetails',
            ],
            'fields' => [
                'element_type'   => 'Elementtyp',
                'is_taxable'     => 'Steuerpflichtig',
                'is_pensionable' => 'Rentenpflichtig',
                'is_active'      => 'Aktiv',
            ],
            'columns' => [
                'type'       => 'Typ',
                'is_taxable' => 'Steuerpflichtig',
                'is_active'  => 'Aktiv',
            ],
        ],
        'pay_runs' => [
            'label'  => 'Lohnlauf',
            'plural' => 'Lohnläufe',
            'sections' => [
                'details' => 'Lohnlaufdetails',
            ],
            'fields' => [
                'payroll_entity' => 'Lohnabrechnungsentität',
                'period_start'   => 'Periodenbeginn',
                'period_end'     => 'Periodenende',
                'payment_date'   => 'Zahlungsdatum',
            ],
            'columns' => [
                'period_start'  => 'Periodenbeginn',
                'period_end'    => 'Periodenende',
                'payment_date'  => 'Zahlungsdatum',
                'frequency'     => 'Häufigkeit',
                'gross'         => 'Brutto',
                'net'           => 'Netto',
            ],
            'filters' => [
                'frequency' => 'Häufigkeit',
            ],
        ],
        'payslips' => [
            'label'  => 'Gehaltsabrechnung',
            'plural' => 'Gehaltsabrechnungen',
            'columns' => [
                'employee'     => 'Mitarbeiter',
                'pay_run'      => 'Lohnlauf',
                'period_start' => 'Periodenbeginn',
                'period_end'   => 'Periodenende',
                'generated'    => 'Erstellt',
                'sent'         => 'Versendet',
            ],
        ],
        'salary_records' => [
            'label'  => 'Gehaltsaufzeichnung',
            'plural' => 'Gehaltsaufzeichnungen',
            'sections' => [
                'details' => 'Gehaltsdetails',
            ],
            'fields' => [
                'employee'       => 'Mitarbeiter',
                'effective_from' => 'Gültig ab',
                'effective_to'   => 'Gültig bis',
                'annual_salary'  => 'Jahresgehalt',
                'currency'       => 'Währung',
            ],
            'columns' => [
                'employee'       => 'Mitarbeiter',
                'effective_from' => 'Gültig ab',
                'frequency'      => 'Häufigkeit',
                'annual_salary'  => 'Jahresgehalt',
                'currency'       => 'Währung',
            ],
        ],
        'contractor_payments' => [
            'label'  => 'Auftragnehmer-Zahlung',
            'plural' => 'Auftragnehmer-Zahlungen',
            'sections' => [
                'details' => 'Zahlungsdetails',
            ],
            'fields' => [
                'contractor_employee' => 'Auftragnehmer / Mitarbeiter',
                'pay_run'             => 'Lohnlauf',
            ],
            'columns' => [
                'contractor' => 'Auftragnehmer',
                'pay_run'    => 'Lohnlauf',
                'processed'  => 'Verarbeitet am',
            ],
        ],
        'deductions' => [
            'label'  => 'Abzug',
            'plural' => 'Abzüge',
            'sections' => [
                'details' => 'Abzugsdetails',
            ],
            'fields' => [
                'employee'       => 'Mitarbeiter',
                'pay_element'    => 'Lohnelement',
                'deduction_type' => 'Abzugstyp',
                'is_percentage'  => 'Prozentbasiert?',
                'is_recurring'   => 'Wiederkehrend',
                'effective_from' => 'Gültig ab',
                'effective_to'   => 'Gültig bis',
            ],
            'columns' => [
                'employee'      => 'Mitarbeiter',
                'type'          => 'Typ',
                'is_percentage' => '% Basiert',
                'is_recurring'  => 'Wiederkehrend',
                'from'          => 'Ab',
                'to'            => 'Bis',
            ],
        ],
        'public_holidays' => [
            'label'  => 'Öffentlicher Feiertag',
            'plural' => 'Öffentliche Feiertage',
            'sections' => [
                'details' => 'Feiertagsdetails',
            ],
            'fields' => [
                'country_code' => 'Ländercode',
                'is_recurring' => 'Jährlich wiederkehrend',
            ],
            'columns' => [
                'country'      => 'Land',
                'is_recurring' => 'Wiederkehrend',
            ],
            'filters' => [
                'country' => 'Land',
            ],
        ],
        'employee_documents' => [
            'title' => 'Dokumente',
            'sections' => [
                'details' => 'Dokumentdetails',
            ],
        ],
    ],
];
