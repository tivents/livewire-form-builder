{{-- resources/views/partials/icon.blade.php --}}
{{-- Minimal inline SVG icons mapped from heroicon names --}}
@php
    $icons = [
        'heroicon-o-pencil'             => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>',
        'heroicon-o-document-text'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
        'heroicon-o-chevron-up-down'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/>',
        'heroicon-o-check-circle'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'heroicon-o-radio'              => '<circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 1v2m0 18v2M4.22 4.22l1.42 1.42m12.72 12.72l1.42 1.42M1 12h2m18 0h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>',
        'heroicon-o-calendar-days'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
        'heroicon-o-paper-clip'         => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>',
        'heroicon-o-rectangle-stack'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6h12M6 10h12M6 14h12M6 18h12"/>',
        'heroicon-o-h1'                 => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>',
        'heroicon-o-information-circle' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'heroicon-o-code-bracket'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>',
    ];
    $path = $icons[$name] ?? '<circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>';
@endphp
<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">{!! $path !!}</svg>
