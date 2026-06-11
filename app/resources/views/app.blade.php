<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="FlowFlex — one platform, every tool, always flexible. HR, finance, CRM and more for teams of 50–500. Per user, per module pricing.">
    <link rel="icon" type="image/svg+xml" href="/images/logo/flowflex-icon.svg">
    <title inertia>{{ config('app.name', 'FlowFlex') }}</title>
    @routes
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    @inertiaHead
</head>
<body class="font-sans antialiased bg-paper text-ink">
    @inertia
</body>
</html>
