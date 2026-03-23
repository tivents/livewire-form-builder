<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title }} – Form Architect</title>

    @fluxStyles
    @livewireStyles
    @stack('styles')
</head>
<body class="bg-zinc-50 text-zinc-900 antialiased">

    {{ $slot }}

    @fluxScripts
    @livewireScripts
    @stack('scripts')
</body>
</html>
