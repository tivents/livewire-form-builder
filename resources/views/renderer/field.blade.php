{{-- resources/views/renderer/field.blade.php --}}

@php
    $inputClass = 'block w-full rounded-lg border ' . ($error ? 'border-red-400 bg-red-50 focus:ring-red-400' : 'border-gray-300 bg-white focus:ring-indigo-400') . ' px-3 py-2 text-sm text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:border-transparent transition';
@endphp

{{-- ── Layout fields ── --}}
@if ($type === 'heading')
    @php $tag = $field['level'] ?? 'h2'; $sizes = ['h1'=>'text-3xl','h2'=>'text-2xl font-bold','h3'=>'text-xl font-semibold','h4'=>'text-lg font-semibold']; @endphp
    <{{ $tag }} class="{{ $sizes[$tag] ?? 'text-xl' }} text-gray-800 mt-2">{{ $field['text'] ?? $field['label'] ?? '' }}</{{ $tag }}>

@elseif ($type === 'hint')
    @php $palette = ['info'=>['bg-blue-50 text-blue-800 border-blue-200','ℹ️'],'warning'=>['bg-yellow-50 text-yellow-800 border-yellow-200','⚠️'],'success'=>['bg-green-50 text-green-800 border-green-200','✅'],'error'=>['bg-red-50 text-red-800 border-red-200','🚫']]; $p = $palette[$field['style'] ?? 'info']; @endphp
    <div class="rounded-lg border px-4 py-3 text-sm flex gap-2 {{ $p[0] }}">
        <span class="flex-none">{{ $p[1] }}</span>
        <span>{{ $field['text'] }}</span>
    </div>

@elseif ($type === 'html')
    <div class="prose prose-sm max-w-none">{!! $field['content'] !!}</div>

@elseif ($type === 'divider')
    <hr class="border-t border-gray-200 my-2" />

{{-- ── Input fields ── --}}
@else
    <div class="space-y-1">
        {{-- Label --}}
        @if (!empty($field['label']))
        <label for="fa_{{ $key }}" class="block text-sm font-medium text-gray-700">
            {{ $field['label'] }}
            @if (!empty($field['required'])) <span class="text-red-500 ml-0.5">*</span> @endif
        </label>
        @endif

        {{-- Hint --}}
        @if (!empty($field['hint']))
        <p class="text-xs text-gray-500">{{ $field['hint'] }}</p>
        @endif

        {{-- Field input --}}
        @if ($type === 'text')
            <input
                type="{{ $field['input_type'] ?? 'text' }}"
                id="fa_{{ $key }}"
                wire:model.live.debounce.300ms="formData.{{ $key }}"
                placeholder="{{ $field['placeholder'] ?? '' }}"
                @if(!empty($field['min_length'])) minlength="{{ $field['min_length'] }}" @endif
                @if(!empty($field['max_length'])) maxlength="{{ $field['max_length'] }}" @endif
                @if(!empty($field['disabled'])) disabled @endif
                class="{{ $inputClass }}"
            />

        @elseif ($type === 'textarea')
            <textarea
                id="fa_{{ $key }}"
                wire:model.live.debounce.300ms="formData.{{ $key }}"
                rows="{{ $field['rows'] ?? 4 }}"
                placeholder="{{ $field['placeholder'] ?? '' }}"
                @if(!empty($field['disabled'])) disabled @endif
                class="{{ $inputClass }}"
            ></textarea>

        @elseif ($type === 'select')
            <select
                id="fa_{{ $key }}"
                wire:model.live="formData.{{ $key }}"
                @if(!empty($field['multiple'])) multiple @endif
                @if(!empty($field['disabled'])) disabled @endif
                class="{{ $inputClass }}"
            >
                @if (empty($field['multiple']))
                    <option value="">{{ $field['placeholder'] ?? '— Select —' }}</option>
                @endif
                @foreach (($field['options'] ?? []) as $opt)
                    <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                @endforeach
            </select>

        @elseif ($type === 'checkbox')
            <div class="{{ !empty($field['inline']) ? 'flex flex-wrap gap-4' : 'space-y-2' }}">
                @foreach (($field['options'] ?? []) as $opt)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            wire:model.live="formData.{{ $key }}"
                            value="{{ $opt['value'] }}"
                            @if(!empty($field['disabled'])) disabled @endif
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        />
                        <span class="text-sm text-gray-700">{{ $opt['label'] }}</span>
                    </label>
                @endforeach
            </div>

        @elseif ($type === 'radio')
            <div class="{{ !empty($field['inline']) ? 'flex flex-wrap gap-4' : 'space-y-2' }}">
                @foreach (($field['options'] ?? []) as $opt)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="radio"
                            wire:model.live="formData.{{ $key }}"
                            value="{{ $opt['value'] }}"
                            @if(!empty($field['disabled'])) disabled @endif
                            class="border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        />
                        <span class="text-sm text-gray-700">{{ $opt['label'] }}</span>
                    </label>
                @endforeach
            </div>

        @elseif ($type === 'datetime')
            @php $inputType = match($field['mode'] ?? 'date') { 'time' => 'time', 'datetime' => 'datetime-local', default => 'date' }; @endphp
            <input
                type="{{ $inputType }}"
                id="fa_{{ $key }}"
                wire:model.live="formData.{{ $key }}"
                @if(!empty($field['min_date'])) min="{{ $field['min_date'] }}" @endif
                @if(!empty($field['max_date'])) max="{{ $field['max_date'] }}" @endif
                @if(!empty($field['disabled'])) disabled @endif
                class="{{ $inputClass }}"
            />

        @elseif ($type === 'number')
            <input
                type="number"
                id="fa_{{ $key }}"
                wire:model.live.debounce.300ms="formData.{{ $key }}"
                placeholder="{{ $field['placeholder'] ?? '' }}"
                @if(!is_null($field['min'] ?? null)) min="{{ $field['min'] }}" @endif
                @if(!is_null($field['max'] ?? null)) max="{{ $field['max'] }}" @endif
                @if(!is_null($field['step'] ?? null)) step="{{ $field['step'] }}" @endif
                @if(!empty($field['disabled'])) disabled @endif
                class="{{ $inputClass }}"
            />

        @elseif ($type === 'toggle')
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    wire:click="$set('formData.{{ $key }}', !formData['{{ $key }}'])"
                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 {{ !empty($formData[$key]) ? 'bg-indigo-600' : 'bg-gray-200' }}"
                    role="switch"
                    aria-checked="{{ !empty($formData[$key]) ? 'true' : 'false' }}"
                >
                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ !empty($formData[$key]) ? 'translate-x-5' : 'translate-x-0' }}"></span>
                </button>
                <span class="text-sm text-gray-600">
                    {{ !empty($formData[$key]) ? ($field['on_label'] ?? 'Yes') : ($field['off_label'] ?? 'No') }}
                </span>
                <input type="hidden" wire:model="formData.{{ $key }}" />
            </div>

        @elseif ($type === 'hidden')
            <input type="hidden" wire:model="formData.{{ $key }}" value="{{ $field['default'] ?? '' }}" />
            <div class="relative">
                <input
                    type="file"
                    id="fa_{{ $key }}"
                    wire:model="fileUploads.{{ $key }}"
                    @if(!empty($field['multiple'])) multiple @endif
                    @if(!empty($field['allowed_types'])) accept="{{ implode(',', $field['allowed_types']) }}" @endif
                    @if(!empty($field['disabled'])) disabled @endif
                    class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-gray-300 rounded-lg"
                />
            </div>

        @elseif ($type === 'repeater')
            <div class="space-y-3">
                @php $rows = $formData[$key] ?? []; @endphp

                @foreach ($rows as $ri => $rowData)
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 relative">
                        <div class="grid grid-cols-12 gap-3">
                            @foreach (($field['children'] ?? []) as $child)
                                @php $ck = $child['key'] ?? null; $cw = \Tivents\LivewireFormBuilder\Support\AbstractFieldType::widthClass($child['width'] ?? 'full'); @endphp
                                @if ($ck)
                                <div class="{{ $cw }}">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ $child['label'] ?? $ck }}</label>
                                    <input
                                        type="{{ $child['input_type'] ?? 'text' }}"
                                        wire:model.live="formData.{{ $key }}.{{ $ri }}.{{ $ck }}"
                                        placeholder="{{ $child['placeholder'] ?? '' }}"
                                        class="{{ $inputClass }} text-xs"
                                    />
                                </div>
                                @endif
                            @endforeach
                        </div>
                        <button
                            type="button"
                            wire:click="$set('formData.{{ $key }}', array_values(array_filter(formData['{{ $key }}'] ?? [], fn($k) => $k !== {{ $ri }}, ARRAY_FILTER_USE_KEY)))"
                            class="absolute top-2 right-2 text-gray-300 hover:text-red-500 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endforeach

                @php $maxRows = $field['max_rows'] ?? null; $canAdd = $maxRows === null || count($rows) < $maxRows; @endphp
                @if ($canAdd)
                    <button
                        type="button"
                        wire:click="$set('formData.{{ $key }}', array_merge(formData['{{ $key }}'] ?? [], [[]]))"
                        class="flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        {{ $field['add_label'] ?? 'Add item' }}
                    </button>
                @endif
            </div>
        @endif

        {{-- Error --}}
        @if ($error)
        <p class="text-xs text-red-600 flex items-center gap-1">
            <svg class="w-3.5 h-3.5 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $error }}
        </p>
        @endif
    </div>
@endif
