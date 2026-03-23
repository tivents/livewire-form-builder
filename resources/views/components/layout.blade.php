<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title }} – Form Architect</title>

    {{-- Tailwind CDN (replace with your own build in production) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @livewireStyles
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

    {{ $slot }}

    @livewireScripts
    @stack('scripts')
</body>
</html>
