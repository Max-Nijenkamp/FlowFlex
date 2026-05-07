<?php

return [
    'navigation' => [
        'groups' => [
            'settings' => 'Settings',
        ],
    ],
    'pages' => [
        'company' => [
            'title'     => 'Company Settings',
            'nav_label' => 'Company Settings',
            'sections' => [
                'company_details' => 'Company Details',
                'localisation'    => 'Localisation',
                'branding'        => 'Branding',
            ],
            'fields' => [
                'company_name' => 'Company name',
                'email'        => 'Email address',
                'phone'        => 'Phone number',
                'website'      => 'Website',
                'timezone'     => 'Timezone',
                'language'     => 'Language',
                'currency'     => 'Currency',
                'logo'         => 'Company logo',
            ],
            'actions' => [
                'save' => 'Save changes',
            ],
            'notifications' => [
                'saved' => 'Company settings saved',
            ],
        ],
        'team' => [
            'title'     => 'Team',
            'nav_label' => 'Team',
            'sections' => [
                'personal_details' => 'Personal details',
            ],
            'fields' => [
                'name'       => 'Name',
                'first_name' => 'First name',
                'last_name'  => 'Last name',
                'email'      => 'Email address',
                'password'   => 'Password',
                'roles'      => 'Roles',
                'active'     => 'Active',
            ],
            'columns' => [
                'name'   => 'Name',
                'email'  => 'Email',
                'roles'  => 'Roles',
                'active' => 'Active',
                'joined' => 'Joined',
            ],
            'actions' => [
                'add_member' => 'Add member',
                'disable'    => 'Disable',
                'enable'     => 'Enable',
            ],
            'notifications' => [
                'member_updated'      => 'Team member updated',
                'member_added'        => 'Team member added',
                'cannot_disable_self' => 'You cannot disable your own account',
                'member_enabled'      => 'Member enabled',
                'member_disabled'     => 'Member disabled',
            ],
        ],
        'api_keys' => [
            'title'     => 'API Keys',
            'nav_label' => 'API Keys',
            'fields' => [
                'name'       => 'Key name',
                'key_prefix' => 'Key prefix',
                'scopes'     => 'Allowed modules (scopes)',
                'last_used'  => 'Last used',
                'expires'    => 'Expires',
                'created'    => 'Created',
                'expires_at' => 'Expiry date',
            ],
            'actions' => [
                'create_key' => 'Create API key',
                'revoke'     => 'Revoke',
            ],
            'modals' => [
                'revoke_heading'     => 'Revoke API key',
                'revoke_description' => 'This will immediately invalidate the key. This cannot be undone.',
            ],
            'notifications' => [
                'created'      => 'API key created — copy it now',
                'created_body' => 'This is the only time the key will be shown. Store it somewhere safe.',
                'revoked'      => 'API key revoked',
            ],
        ],
        'notifications' => [
            'title'     => 'Notification Preferences',
            'nav_label' => 'Notifications',
            'fields' => [
                'enabled' => 'Enabled',
                'email'   => 'Email',
            ],
            'actions' => [
                'save' => 'Save preferences',
            ],
            'notifications' => [
                'saved' => 'Notification preferences saved',
            ],
        ],
    ],
];
