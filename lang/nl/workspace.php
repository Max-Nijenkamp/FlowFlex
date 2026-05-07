<?php

return [
    'navigation' => [
        'groups' => [
            'settings' => 'Instellingen',
        ],
    ],
    'pages' => [
        'company' => [
            'title'     => 'Bedrijfsinstellingen',
            'nav_label' => 'Bedrijfsinstellingen',
            'sections' => [
                'company_details' => 'Bedrijfsgegevens',
                'localisation'    => 'Lokalisatie',
                'branding'        => 'Branding',
            ],
            'fields' => [
                'company_name' => 'Bedrijfsnaam',
                'email'        => 'E-mailadres',
                'phone'        => 'Telefoonnummer',
                'website'      => 'Website',
                'timezone'     => 'Tijdzone',
                'language'     => 'Taal',
                'currency'     => 'Valuta',
                'logo'         => 'Bedrijfslogo',
            ],
            'actions' => [
                'save' => 'Wijzigingen opslaan',
            ],
            'notifications' => [
                'saved' => 'Bedrijfsinstellingen opgeslagen',
            ],
        ],
        'team' => [
            'title'     => 'Team',
            'nav_label' => 'Team',
            'sections' => [
                'personal_details' => 'Persoonlijke gegevens',
            ],
            'fields' => [
                'name'       => 'Naam',
                'first_name' => 'Voornaam',
                'last_name'  => 'Achternaam',
                'email'      => 'E-mailadres',
                'password'   => 'Wachtwoord',
                'roles'      => 'Rollen',
                'active'     => 'Actief',
            ],
            'columns' => [
                'name'   => 'Naam',
                'email'  => 'E-mail',
                'roles'  => 'Rollen',
                'active' => 'Actief',
                'joined' => 'Lid geworden',
            ],
            'actions' => [
                'add_member' => 'Lid toevoegen',
                'disable'    => 'Uitschakelen',
                'enable'     => 'Inschakelen',
            ],
            'notifications' => [
                'member_updated'        => 'Teamlid bijgewerkt',
                'member_added'          => 'Teamlid toegevoegd',
                'cannot_disable_self'   => 'U kunt uw eigen account niet uitschakelen',
                'member_enabled'        => 'Lid ingeschakeld',
                'member_disabled'       => 'Lid uitgeschakeld',
            ],
        ],
        'api_keys' => [
            'title'     => 'API-sleutels',
            'nav_label' => 'API-sleutels',
            'fields' => [
                'name'       => 'Sleutelnaam',
                'key_prefix' => 'Sleutelprefix',
                'scopes'     => 'Toegestane modules (bereiken)',
                'last_used'  => 'Laatste gebruik',
                'expires'    => 'Vervalt',
                'created'    => 'Aangemaakt',
                'expires_at' => 'Vervaldatum',
            ],
            'actions' => [
                'create_key' => 'API-sleutel aanmaken',
                'revoke'     => 'Intrekken',
            ],
            'modals' => [
                'revoke_heading'     => 'API-sleutel intrekken',
                'revoke_description' => 'Hierdoor wordt de sleutel onmiddellijk ongeldig. Dit kan niet ongedaan worden gemaakt.',
            ],
            'notifications' => [
                'created'       => 'API-sleutel aangemaakt — kopieer hem nu',
                'created_body'  => 'Dit is de enige keer dat de sleutel wordt getoond. Bewaar hem op een veilige plek.',
                'revoked'       => 'API-sleutel ingetrokken',
            ],
        ],
        'notifications' => [
            'title'     => 'Meldingsvoorkeuren',
            'nav_label' => 'Meldingen',
            'fields' => [
                'enabled' => 'Ingeschakeld',
                'email'   => 'E-mail',
            ],
            'actions' => [
                'save' => 'Voorkeuren opslaan',
            ],
            'notifications' => [
                'saved' => 'Meldingsvoorkeuren opgeslagen',
            ],
        ],
    ],
];
