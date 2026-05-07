<?php

return [
    'navigation' => [
        'groups' => [
            'invoices' => 'Rechnungen',
            'expenses' => 'Ausgaben',
            'reports'  => 'Berichte',
        ],
    ],
    'resources' => [
        'invoices' => [
            'label'  => 'Rechnung',
            'plural' => 'Rechnungen',
            'sections' => [
                'details' => 'Rechnungsdetails',
                'pricing' => 'Preisgestaltung',
            ],
            'fields' => [
                'number'         => 'Nummer',
                'contact_id'     => 'Kontakt-ID',
                'issue_date'     => 'Ausstellungsdatum',
                'due_date'       => 'Fälligkeitsdatum',
                'currency'       => 'Währung',
                'status'         => 'Status',
                'notes'          => 'Notizen',
                'discount_type'  => 'Rabatttyp',
                'discount_value' => 'Rabattwert',
                'tax_rate'       => 'Steuersatz',
            ],
            'columns' => [
                'number'     => 'Nummer',
                'contact'    => 'Kontakt',
                'issue_date' => 'Ausstellungsdatum',
                'due_date'   => 'Fälligkeitsdatum',
                'total'      => 'Gesamt',
                'status'     => 'Status',
            ],
        ],
        'credit_notes' => [
            'label'  => 'Gutschrift',
            'plural' => 'Gutschriften',
            'sections' => [
                'details' => 'Gutschriftdetails',
            ],
            'fields' => [
                'invoice_id' => 'Rechnung',
                'number'     => 'Nummer',
                'amount'     => 'Betrag',
                'reason'     => 'Grund',
                'issued_at'  => 'Ausstellungsdatum',
            ],
            'columns' => [
                'number'    => 'Nummer',
                'invoice'   => 'Rechnung',
                'amount'    => 'Betrag',
                'issued_at' => 'Ausstellungsdatum',
            ],
        ],
        'recurring_invoices' => [
            'label'  => 'Wiederkehrende Rechnung',
            'plural' => 'Wiederkehrende Rechnungen',
            'sections' => [
                'details' => 'Details zur wiederkehrenden Rechnung',
            ],
            'fields' => [
                'frequency'   => 'Häufigkeit',
                'next_run_at' => 'Nächste Ausführung',
                'last_run_at' => 'Letzte Ausführung',
                'is_active'   => 'Aktiv',
            ],
            'columns' => [
                'frequency'   => 'Häufigkeit',
                'next_run_at' => 'Nächste Ausführung',
                'last_run_at' => 'Letzte Ausführung',
                'is_active'   => 'Aktiv',
            ],
        ],
        'expenses' => [
            'label'  => 'Ausgabe',
            'plural' => 'Ausgaben',
            'sections' => [
                'details' => 'Ausgabendetails',
            ],
            'fields' => [
                'tenant_id'           => 'Mitarbeiter',
                'expense_category_id' => 'Kategorie',
                'description'         => 'Beschreibung',
                'amount'              => 'Betrag',
                'currency'            => 'Währung',
                'expense_date'        => 'Datum',
                'status'              => 'Status',
                'vendor'              => 'Lieferant',
                'mileage_km'          => 'Kilometer',
                'rejection_reason'    => 'Ablehnungsgrund',
            ],
            'columns' => [
                'description'  => 'Beschreibung',
                'employee'     => 'Mitarbeiter',
                'amount'       => 'Betrag',
                'expense_date' => 'Datum',
                'status'       => 'Status',
                'category'     => 'Kategorie',
            ],
            'actions' => [
                'approve'          => 'Genehmigen',
                'reject'           => 'Ablehnen',
                'rejection_reason' => 'Ablehnungsgrund',
            ],
        ],
        'expense_categories' => [
            'label'  => 'Ausgabenkategorie',
            'plural' => 'Ausgabenkategorien',
            'sections' => [
                'details' => 'Ausgabenkategorie',
            ],
            'fields' => [
                'name'          => 'Name',
                'description'   => 'Beschreibung',
                'monthly_limit' => 'Monatliches Limit',
                'is_active'     => 'Aktiv',
            ],
            'columns' => [
                'name'          => 'Name',
                'description'   => 'Beschreibung',
                'monthly_limit' => 'Monatliches Limit',
                'is_active'     => 'Aktiv',
            ],
        ],
        'expense_reports' => [
            'label'  => 'Ausgabenbericht',
            'plural' => 'Ausgabenberichte',
            'sections' => [
                'details' => 'Ausgabenberichtdetails',
            ],
            'fields' => [
                'title'        => 'Titel',
                'tenant_id'    => 'Mitarbeiter',
                'status'       => 'Status',
                'submitted_at' => 'Eingereicht am',
            ],
            'columns' => [
                'title'        => 'Titel',
                'employee'     => 'Mitarbeiter',
                'status'       => 'Status',
                'submitted_at' => 'Eingereicht am',
            ],
        ],
        'mileage_rates' => [
            'label'  => 'Kilometerrate',
            'plural' => 'Kilometerraten',
            'sections' => [
                'details' => 'Kilometerrate',
            ],
            'fields' => [
                'name'           => 'Name',
                'rate_per_km'    => 'Rate pro km',
                'currency'       => 'Währung',
                'effective_from' => 'Gültig ab',
                'effective_to'   => 'Gültig bis',
                'is_active'      => 'Aktiv',
            ],
            'columns' => [
                'name'           => 'Name',
                'rate_per_km'    => 'Rate pro km',
                'currency'       => 'Währung',
                'effective_from' => 'Gültig ab',
                'effective_to'   => 'Gültig bis',
                'is_active'      => 'Aktiv',
            ],
        ],
        'financial_reporting' => [
            'label'      => 'Finanzberichte',
            'nav_label'  => 'Finanzberichte',
            'page_title' => 'Finanzberichte',
        ],
    ],
];
