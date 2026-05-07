<?php

return [
    'navigation' => [
        'groups' => [
            'invoices' => 'Facturen',
            'expenses' => 'Onkosten',
            'reports'  => 'Rapporten',
        ],
    ],
    'resources' => [
        'invoices' => [
            'label'  => 'Factuur',
            'plural' => 'Facturen',
            'sections' => [
                'details' => 'Factuurgegevens',
                'pricing' => 'Prijsstelling',
            ],
            'fields' => [
                'number'         => 'Nummer',
                'contact_id'     => 'Contact-ID',
                'issue_date'     => 'Uitgiftedatum',
                'due_date'       => 'Vervaldatum',
                'currency'       => 'Valuta',
                'status'         => 'Status',
                'notes'          => 'Notities',
                'discount_type'  => 'Kortingstype',
                'discount_value' => 'Kortingswaarde',
                'tax_rate'       => 'BTW-tarief',
            ],
            'columns' => [
                'number'     => 'Nummer',
                'contact'    => 'Contact',
                'issue_date' => 'Uitgiftedatum',
                'due_date'   => 'Vervaldatum',
                'total'      => 'Totaal',
                'status'     => 'Status',
            ],
        ],
        'credit_notes' => [
            'label'  => 'Creditnota',
            'plural' => 'Creditnota\'s',
            'sections' => [
                'details' => 'Creditnotagegevens',
            ],
            'fields' => [
                'invoice_id' => 'Factuur',
                'number'     => 'Nummer',
                'amount'     => 'Bedrag',
                'reason'     => 'Reden',
                'issued_at'  => 'Uitgiftedatum',
            ],
            'columns' => [
                'number'    => 'Nummer',
                'invoice'   => 'Factuur',
                'amount'    => 'Bedrag',
                'issued_at' => 'Uitgiftedatum',
            ],
        ],
        'recurring_invoices' => [
            'label'  => 'Terugkerende factuur',
            'plural' => 'Terugkerende facturen',
            'sections' => [
                'details' => 'Terugkerende factuurgegevens',
            ],
            'fields' => [
                'frequency'   => 'Frequentie',
                'next_run_at' => 'Volgende uitvoering',
                'last_run_at' => 'Laatste uitvoering',
                'is_active'   => 'Actief',
            ],
            'columns' => [
                'frequency'   => 'Frequentie',
                'next_run_at' => 'Volgende uitvoering',
                'last_run_at' => 'Laatste uitvoering',
                'is_active'   => 'Actief',
            ],
        ],
        'expenses' => [
            'label'  => 'Onkosten',
            'plural' => 'Onkosten',
            'sections' => [
                'details' => 'Onkostengegevens',
            ],
            'fields' => [
                'tenant_id'           => 'Medewerker',
                'expense_category_id' => 'Categorie',
                'description'         => 'Beschrijving',
                'amount'              => 'Bedrag',
                'currency'            => 'Valuta',
                'expense_date'        => 'Datum',
                'status'              => 'Status',
                'vendor'              => 'Leverancier',
                'mileage_km'          => 'Kilometers',
                'rejection_reason'    => 'Reden afwijzing',
            ],
            'columns' => [
                'description'   => 'Beschrijving',
                'employee'      => 'Medewerker',
                'amount'        => 'Bedrag',
                'expense_date'  => 'Datum',
                'status'        => 'Status',
                'category'      => 'Categorie',
            ],
            'actions' => [
                'approve'          => 'Goedkeuren',
                'reject'           => 'Afwijzen',
                'rejection_reason' => 'Reden afwijzing',
            ],
        ],
        'expense_categories' => [
            'label'  => 'Onkostencategorie',
            'plural' => 'Onkostencategorieën',
            'sections' => [
                'details' => 'Onkostencategorie',
            ],
            'fields' => [
                'name'          => 'Naam',
                'description'   => 'Beschrijving',
                'monthly_limit' => 'Maandlimiet',
                'is_active'     => 'Actief',
            ],
            'columns' => [
                'name'          => 'Naam',
                'description'   => 'Beschrijving',
                'monthly_limit' => 'Maandlimiet',
                'is_active'     => 'Actief',
            ],
        ],
        'expense_reports' => [
            'label'  => 'Onkostenrapportage',
            'plural' => 'Onkostenrapportages',
            'sections' => [
                'details' => 'Onkostenrapportagegegevens',
            ],
            'fields' => [
                'title'        => 'Titel',
                'tenant_id'    => 'Medewerker',
                'status'       => 'Status',
                'submitted_at' => 'Ingediend op',
            ],
            'columns' => [
                'title'        => 'Titel',
                'employee'     => 'Medewerker',
                'status'       => 'Status',
                'submitted_at' => 'Ingediend op',
            ],
        ],
        'mileage_rates' => [
            'label'  => 'Kilometervergoeding',
            'plural' => 'Kilometervergoedingen',
            'sections' => [
                'details' => 'Kilometervergoeding',
            ],
            'fields' => [
                'name'           => 'Naam',
                'rate_per_km'    => 'Tarief per km',
                'currency'       => 'Valuta',
                'effective_from' => 'Geldig vanaf',
                'effective_to'   => 'Geldig tot',
                'is_active'      => 'Actief',
            ],
            'columns' => [
                'name'           => 'Naam',
                'rate_per_km'    => 'Tarief per km',
                'currency'       => 'Valuta',
                'effective_from' => 'Geldig vanaf',
                'effective_to'   => 'Geldig tot',
                'is_active'      => 'Actief',
            ],
        ],
        'financial_reporting' => [
            'label'       => 'Financiële rapporten',
            'nav_label'   => 'Financiële rapporten',
            'page_title'  => 'Financiële rapporten',
        ],
    ],
];
