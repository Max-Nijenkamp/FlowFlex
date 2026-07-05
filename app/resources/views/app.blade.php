<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="theme-color" content="#FBFAF8" />

        <title inertia>{{ config('app.name', 'FlowFlex') }}</title>
        <meta name="description" content="One platform. Every tool. Always flexible. HR, finance, CRM and 70 more modules on one database — pay per user, per module." inertia />

        <link rel="icon" href="/images/logo/flowflex-icon.svg" type="image/svg+xml" />

        @vite(['resources/css/app.css', 'resources/js/app.ts'])
        @inertiaHead
    </head>
    <body class="antialiased">
        @inertia
    </body>
</html>
