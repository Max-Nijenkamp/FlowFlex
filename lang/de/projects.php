<?php

return [
    'navigation' => [
        'groups' => [
            'tasks'         => 'Aufgaben',
            'time_tracking' => 'Zeiterfassung',
            'documents'     => 'Dokumente',
        ],
    ],
    'resources' => [
        'tasks' => [
            'label'  => 'Aufgabe',
            'plural' => 'Aufgaben',
            'sections' => [
                'details'    => 'Aufgabendetails',
                'assignment' => 'Zuweisung',
                'labels'     => 'Etiketten',
            ],
            'fields' => [
                'title'           => 'Titel',
                'description'     => 'Beschreibung',
                'status'          => 'Status',
                'priority'        => 'Priorität',
                'assignee'        => 'Zugewiesen an',
                'due_date'        => 'Fälligkeitsdatum',
                'start_date'      => 'Startdatum',
                'estimated_hours' => 'Geschätzte Stunden',
                'labels'          => 'Etiketten',
            ],
            'columns' => [
                'title'           => 'Titel',
                'status'          => 'Status',
                'priority'        => 'Priorität',
                'assignee'        => 'Zugewiesen an',
                'due_date'        => 'Fälligkeitsdatum',
                'estimated_hours' => 'Gesch. Stunden',
            ],
        ],
        'task_labels' => [
            'label'  => 'Aufgabenetikett',
            'plural' => 'Aufgabenetiketten',
            'sections' => [
                'details' => 'Etikettendetails',
            ],
            'fields' => [
                'name'  => 'Name',
                'color' => 'Farbe (Hex)',
            ],
            'columns' => [
                'name'        => 'Name',
                'color'       => 'Farbe',
                'tasks_count' => 'Aufgaben',
            ],
        ],
        'time_entries' => [
            'label'  => 'Zeiteintrag',
            'plural' => 'Zeiteinträge',
            'sections' => [
                'details' => 'Zeiteintragdetails',
            ],
            'fields' => [
                'task'        => 'Aufgabe',
                'entry_date'  => 'Datum',
                'description' => 'Beschreibung',
                'minutes'     => 'Zeit (Minuten)',
                'is_billable' => 'Abrechenbar',
            ],
            'columns' => [
                'entry_date'  => 'Datum',
                'task'        => 'Aufgabe',
                'description' => 'Beschreibung',
                'duration'    => 'Dauer',
                'is_billable' => 'Abrechenbar',
                'is_approved' => 'Genehmigt',
            ],
            'filters' => [
                'is_billable'        => 'Abrechenbar',
                'billable_only'      => 'Nur abrechenbar',
                'non_billable_only'  => 'Nur nicht abrechenbar',
                'all_entries'        => 'Alle Einträge',
                'is_approved'        => 'Genehmigungsstatus',
                'approved_only'      => 'Nur genehmigt',
                'pending_only'       => 'Nur ausstehend',
            ],
        ],
        'timesheets' => [
            'label'  => 'Stundenzettel',
            'plural' => 'Stundenzettel',
            'sections' => [
                'details' => 'Stundenzetteldetails',
            ],
            'fields' => [
                'week_start_date' => 'Wochenbeginn',
                'status'          => 'Status',
                'submitted_at'    => 'Eingereicht am',
            ],
            'columns' => [
                'week_start_date' => 'Wochenbeginn',
                'status'          => 'Status',
                'submitted_at'    => 'Eingereicht am',
            ],
        ],
        'document_folders' => [
            'label'  => 'Dokumentordner',
            'plural' => 'Dokumentordner',
            'sections' => [
                'details' => 'Ordnerdetails',
            ],
            'fields' => [
                'name'             => 'Name',
                'parent_folder_id' => 'Übergeordneter Ordner',
            ],
            'columns' => [
                'name'   => 'Name',
                'parent' => 'Übergeordneter Ordner',
            ],
        ],
        'documents' => [
            'label'  => 'Dokument',
            'plural' => 'Dokumente',
            'sections' => [
                'details' => 'Dokumentdetails',
            ],
            'fields' => [
                'title'     => 'Titel',
                'folder_id' => 'Ordner',
            ],
            'columns' => [
                'title'  => 'Titel',
                'folder' => 'Ordner',
            ],
        ],
    ],
];
