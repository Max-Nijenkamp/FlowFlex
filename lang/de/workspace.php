<?php

return [
    'navigation' => [
        'groups' => [
            'settings' => 'Einstellungen',
        ],
    ],
    'pages' => [
        'company' => [
            'title'     => 'Unternehmenseinstellungen',
            'nav_label' => 'Unternehmenseinstellungen',
            'sections' => [
                'company_details' => 'Unternehmensdetails',
                'localisation'    => 'Lokalisierung',
                'branding'        => 'Branding',
            ],
            'fields' => [
                'company_name' => 'Unternehmensname',
                'email'        => 'E-Mail-Adresse',
                'phone'        => 'Telefonnummer',
                'website'      => 'Website',
                'timezone'     => 'Zeitzone',
                'language'     => 'Sprache',
                'currency'     => 'Währung',
                'logo'         => 'Unternehmenslogo',
            ],
            'actions' => [
                'save' => 'Änderungen speichern',
            ],
            'notifications' => [
                'saved' => 'Unternehmenseinstellungen gespeichert',
            ],
        ],
        'team' => [
            'title'     => 'Team',
            'nav_label' => 'Team',
            'sections' => [
                'personal_details' => 'Persönliche Daten',
            ],
            'fields' => [
                'name'       => 'Name',
                'first_name' => 'Vorname',
                'last_name'  => 'Nachname',
                'email'      => 'E-Mail-Adresse',
                'password'   => 'Passwort',
                'roles'      => 'Rollen',
                'active'     => 'Aktiv',
            ],
            'columns' => [
                'name'   => 'Name',
                'email'  => 'E-Mail',
                'roles'  => 'Rollen',
                'active' => 'Aktiv',
                'joined' => 'Beigetreten',
            ],
            'actions' => [
                'add_member' => 'Mitglied hinzufügen',
                'disable'    => 'Deaktivieren',
                'enable'     => 'Aktivieren',
            ],
            'notifications' => [
                'member_updated'      => 'Teammitglied aktualisiert',
                'member_added'        => 'Teammitglied hinzugefügt',
                'cannot_disable_self' => 'Sie können Ihr eigenes Konto nicht deaktivieren',
                'member_enabled'      => 'Mitglied aktiviert',
                'member_disabled'     => 'Mitglied deaktiviert',
            ],
        ],
        'api_keys' => [
            'title'     => 'API-Schlüssel',
            'nav_label' => 'API-Schlüssel',
            'fields' => [
                'name'       => 'Schlüsselname',
                'key_prefix' => 'Schlüsselpräfix',
                'scopes'     => 'Erlaubte Module (Bereiche)',
                'last_used'  => 'Zuletzt verwendet',
                'expires'    => 'Läuft ab',
                'created'    => 'Erstellt',
                'expires_at' => 'Ablaufdatum',
            ],
            'actions' => [
                'create_key' => 'API-Schlüssel erstellen',
                'revoke'     => 'Widerrufen',
            ],
            'modals' => [
                'revoke_heading'     => 'API-Schlüssel widerrufen',
                'revoke_description' => 'Dadurch wird der Schlüssel sofort ungültig. Dies kann nicht rückgängig gemacht werden.',
            ],
            'notifications' => [
                'created'      => 'API-Schlüssel erstellt — jetzt kopieren',
                'created_body' => 'Dies ist das einzige Mal, dass der Schlüssel angezeigt wird. Bewahren Sie ihn sicher auf.',
                'revoked'      => 'API-Schlüssel widerrufen',
            ],
        ],
        'notifications' => [
            'title'     => 'Benachrichtigungseinstellungen',
            'nav_label' => 'Benachrichtigungen',
            'fields' => [
                'enabled' => 'Aktiviert',
                'email'   => 'E-Mail',
            ],
            'actions' => [
                'save' => 'Einstellungen speichern',
            ],
            'notifications' => [
                'saved' => 'Benachrichtigungseinstellungen gespeichert',
            ],
        ],
    ],
];
