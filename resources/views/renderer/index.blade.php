{{-- resources/views/renderer/index.blade.php --}}
<div class="fa-renderer">
    @if ($submitted)
        <div class="rounded-xl bg-green-50 border border-green-200 px-6 py-8 text-center">
            <flux:icon.check-circle class="size-10 mx-auto mb-3 text-green-500" />
            <p class="text-green-800 font-semibold text-base">{{ $successMessage }}</p>
        </div>
    @else
        <form wire:submit.prevent="submit" novalidate>
            <div class="grid grid-cols-12 gap-x-4 gap-y-5">
                @foreach ($schema as $field)
                    @php
                        $key        = $field['key']   ?? null;
                        $type       = $field['type']  ?? 'text';
                        $visible    = $visibilityMap[$key] ?? true;
                        $widthClass = \Tivents\LivewireFormBuilder\Support\AbstractFieldType::widthClass($field['width'] ?? 'full');
                        $error      = $validationErrors[$key][0] ?? null;
                        $isLayout   = in_array($type, ['heading', 'hint', 'html']);
                    @endphp

                    @if ($visible)
                    <div class="{{ $widthClass }}">
                        @include('livewire-form-builder::renderer.field', [
                            'field'    => $field,
                            'type'     => $type,
                            'key'      => $key,
                            'error'    => $error,
                            'isLayout' => $isLayout,
                        ])
                    </div>
                    @endif
                @endforeach
            </div>

            <div class="mt-8">
                <flux:button type="submit" variant="primary" icon="paper-airplane">
                    Submit
                </flux:button>
            </div>
        </form>
    @endif
</div>
