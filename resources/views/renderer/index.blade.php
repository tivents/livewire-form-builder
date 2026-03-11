{{-- resources/views/renderer/index.blade.php --}}
<div class="fa-renderer">
    @if ($submitted)
        <div class="rounded-xl bg-green-50 border border-green-200 px-6 py-8 text-center">
            <svg class="w-10 h-10 mx-auto mb-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-green-800 font-semibold text-base">{{ $successMessage }}</p>
        </div>
    @else
        <form wire:submit.prevent="submit" novalidate>
            <div class="grid grid-cols-12 gap-x-4 gap-y-5">
                @foreach ($schema as $field)
                    @php
                        $key     = $field['key']   ?? null;
                        $type    = $field['type']  ?? 'text';
                        $visible = $visibilityMap[$key] ?? true;
                        $widthClass = \Tivents\LivewireFormBuilder\Support\AbstractFieldType::widthClass($field['width'] ?? 'full');
                        $error   = $validationErrors[$key][0] ?? null;
                        $isLayout= in_array($type, ['heading', 'hint', 'html']);
                    @endphp

                    @if ($visible)
                    <div class="{{ $widthClass }}">
                        @include('livewire-form-builder::renderer.field', [
                            'field'  => $field,
                            'type'   => $type,
                            'key'    => $key,
                            'error'  => $error,
                            'isLayout' => $isLayout,
                        ])
                    </div>
                    @endif
                @endforeach
            </div>

            <div class="mt-8">
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-6 py-2.5 rounded-xl transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Submit
                </button>
            </div>
        </form>
    @endif
</div>
