<?php

return [
    'navigation' => [
        'groups' => [
            'tasks'         => 'Taken',
            'time_tracking' => 'Tijdregistratie',
            'documents'     => 'Documenten',
        ],
    ],
    'resources' => [
        'tasks' => [
            'label'  => 'Taak',
            'plural' => 'Taken',
            'sections' => [
                'details'    => 'Taakgegevens',
                'assignment' => 'Toewijzing',
                'labels'     => 'Labels',
            ],
            'fields' => [
                'title'           => 'Titel',
                'description'     => 'Beschrijving',
                'status'          => 'Status',
                'priority'        => 'Prioriteit',
                'assignee'        => 'Toegewezen aan',
                'due_date'        => 'Vervaldatum',
                'start_date'      => 'Startdatum',
                'estimated_hours' => 'Geschatte uren',
                'labels'          => 'Labels',
            ],
            'columns' => [
                'title'           => 'Titel',
                'status'          => 'Status',
                'priority'        => 'Prioriteit',
                'assignee'        => 'Toegewezen aan',
                'due_date'        => 'Vervaldatum',
                'estimated_hours' => 'Gesch. uren',
            ],
        ],
        'task_labels' => [
            'label'  => 'Taaklabel',
            'plural' => 'Taaklabels',
            'sections' => [
                'details' => 'Labelgegevens',
            ],
            'fields' => [
                'name'  => 'Naam',
                'color' => 'Kleur (hex)',
            ],
            'columns' => [
                'name'        => 'Naam',
                'color'       => 'Kleur',
                'tasks_count' => 'Taken',
            ],
        ],
        'time_entries' => [
            'label'  => 'Tijdregistratie',
            'plural' => 'Tijdregistraties',
            'sections' => [
                'details' => 'Tijdregistratiegegevens',
            ],
            'fields' => [
                'task'        => 'Taak',
                'entry_date'  => 'Datum',
                'description' => 'Beschrijving',
                'minutes'     => 'Tijd (minuten)',
                'is_billable' => 'Factureerbaar',
            ],
            'columns' => [
                'entry_date'  => 'Datum',
                'task'        => 'Taak',
                'description' => 'Beschrijving',
                'duration'    => 'Duur',
                'is_billable' => 'Factureerbaar',
                'is_approved' => 'Goedgekeurd',
            ],
            'filters' => [
                'is_billable'        => 'Factureerbaar',
                'billable_only'      => 'Alleen factureerbaar',
                'non_billable_only'  => 'Alleen niet-factureerbaar',
                'all_entries'        => 'Alle registraties',
                'is_approved'        => 'Goedkeuringsstatus',
                'approved_only'      => 'Alleen goedgekeurd',
                'pending_only'       => 'Alleen in behandeling',
            ],
        ],
        'timesheets' => [
            'label'  => 'Urenstaat',
            'plural' => 'Urenstaten',
            'sections' => [
                'details' => 'Urenstatengegevens',
            ],
            'fields' => [
                'week_start_date' => 'Week beginnend op',
                'status'          => 'Status',
                'submitted_at'    => 'Ingediend',
            ],
            'columns' => [
                'week_start_date' => 'Week beginnend op',
                'status'          => 'Status',
                'submitted_at'    => 'Ingediend',
            ],
        ],
        'document_folders' => [
            'label'  => 'Documentmap',
            'plural' => 'Documentmappen',
            'sections' => [
                'details' => 'Mapgegevens',
            ],
            'fields' => [
                'name'             => 'Naam',
                'parent_folder_id' => 'Bovenliggende map',
            ],
            'columns' => [
                'name'   => 'Naam',
                'parent' => 'Bovenliggende map',
            ],
        ],
        'documents' => [
            'label'  => 'Document',
            'plural' => 'Documenten',
            'sections' => [
                'details' => 'Documentgegevens',
            ],
            'fields' => [
                'title'     => 'Titel',
                'folder_id' => 'Map',
            ],
            'columns' => [
                'title'  => 'Titel',
                'folder' => 'Map',
            ],
        ],
    ],
];
