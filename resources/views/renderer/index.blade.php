{{-- resources/views/renderer/index.blade.php --}}
<div class="fa-renderer">
@once
<style>
    @media (max-width: 639px) {
        .fa-renderer .fa-grid-item {
            grid-column: span 12 / span 12 !important;
        }
    }
</style>
@endonce
    @if ($submitted)
        <div class="rounded-xl bg-green-50 border border-green-200 px-6 py-8 text-center">
            <svg class="w-10 h-10 mx-auto mb-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-green-800 font-semibold text-base">{{ $successMessage }}</p>
        </div>
    @else
        <form wire:submit="submit" novalidate>
            <div style="display:grid;grid-template-columns:repeat(12,minmax(0,1fr));column-gap:1rem;row-gap:1.25rem;">
                @foreach ($schema as $field)
                    @php
                        $key        = $field['key']  ?? null;
                        $type       = $field['type'] ?? 'text';
                        $widthStyle = \Tivents\LivewireFormBuilder\Support\AbstractFieldType::widthStyle($field['width'] ?? 'full');
                    @endphp

                    @if ($type === 'row')
                        {{-- Row: use its own width property; full-width on mobile --}}
                        <div class="fa-grid-item" style="{{ $widthStyle }}">
                            <div style="display:grid;grid-template-columns:repeat(12,minmax(0,1fr));column-gap:1rem;row-gap:1.25rem;">
                                @foreach (($field['children'] ?? []) as $child)
                                    @php
                                        $cKey       = $child['key']  ?? null;
                                        $cType      = $child['type'] ?? 'text';
                                        $cVisible   = $visibilityMap[$cKey] ?? true;
                                        $cWidthStyle= \Tivents\LivewireFormBuilder\Support\AbstractFieldType::widthStyle($child['width'] ?? 'full');
                                        $cError     = $validationErrors[$cKey][0] ?? null;
                                        $cIsLayout  = in_array($cType, ['heading', 'hint', 'html']);
                                    @endphp
                                    @if ($cVisible)
                                    <div class="fa-grid-item" style="{{ $cWidthStyle }}">
                                        @include('livewire-form-builder::renderer.field', [
                                            'field'    => $child,
                                            'type'     => $cType,
                                            'key'      => $cKey,
                                            'error'    => $cError,
                                            'isLayout' => $cIsLayout,
                                        ])
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @else
                        @php
                            $visible  = $visibilityMap[$key] ?? true;
                            $error    = $validationErrors[$key][0] ?? null;
                            $isLayout = in_array($type, ['heading', 'hint', 'html']);
                        @endphp
                        @if ($visible)
                        <div class="fa-grid-item" style="{{ $widthStyle }}">
                            @include('livewire-form-builder::renderer.field', [
                                'field'    => $field,
                                'type'     => $type,
                                'key'      => $key,
                                'error'    => $error,
                                'isLayout' => $isLayout,
                            ])
                        </div>
                        @endif
                    @endif
                @endforeach
            </div>

            @php
                $btnColors = [
                    'green'  => 'bg-green-600 hover:bg-green-700',
                    'blue'   => 'bg-blue-600 hover:bg-blue-700',
                    'indigo' => 'bg-indigo-600 hover:bg-indigo-700',
                    'red'    => 'bg-red-600 hover:bg-red-700',
                    'orange' => 'bg-orange-600 hover:bg-orange-700',
                    'purple' => 'bg-purple-600 hover:bg-purple-700',
                    'gray'   => 'bg-gray-600 hover:bg-gray-700',
                    'black'  => 'bg-gray-900 hover:bg-black',
                ];
                $btnColor = $btnColors[$settings['button_color'] ?? 'green'] ?? $btnColors['green'];
            @endphp
            @php
                $alignClass = match($settings['button_align'] ?? 'left') {
                    'center' => 'justify-center',
                    'right'  => 'justify-end',
                    default  => 'justify-start',
                };
            @endphp
            <div class="mt-8 flex {{ $alignClass }}">
                <button type="submit"
                    class="inline-flex items-center gap-2 {{ $btnColor }} text-white font-semibold text-sm px-6 py-2.5 transition-colors shadow-sm"
                    style="border-radius: 0.5rem;"
                    wire:loading.attr="disabled">
                    <svg wire:loading wire:target="submit" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="submit">{{ $settings['button_label'] ?? 'Submit' }}</span>
                    <span wire:loading wire:target="submit">{{ $settings['button_loading_label'] ?? $settings['button_label'] ?? 'Submit' }}...</span>
                </button>
            </div>
        </form>
    @endif
</div>
