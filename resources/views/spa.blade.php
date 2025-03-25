<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>AI Scrape Demo 2</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="font-sans antialiased" id="app">
        @if (!file_exists(public_path('build/manifest.json')) && !file_exists(public_path('hot')))
            <div>No manifest.json or hot file found.</div>
        @endif
        <app />
    </body>
</html>
