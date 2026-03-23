{{-- resources/views/renderer/field.blade.php --}}

{{-- ── Layout fields ── --}}
@if ($type === 'heading')
    @php $tag = $field['level'] ?? 'h2'; $sizes = ['h1' => 'text-3xl', 'h2' => 'text-2xl font-bold', 'h3' => 'text-xl font-semibold', 'h4' => 'text-lg font-semibold']; @endphp
    <{{ $tag }} class="{{ $sizes[$tag] ?? 'text-xl' }} text-zinc-800 mt-2">{{ $field['text'] ?? $field['label'] ?? '' }}</{{ $tag }}>

@elseif ($type === 'hint')
    @php
        $palette = [
            'info'    => ['bg-blue-50 text-blue-800 border-blue-200',     'ℹ️'],
            'warning' => ['bg-yellow-50 text-yellow-800 border-yellow-200', '⚠️'],
            'success' => ['bg-green-50 text-green-800 border-green-200',   '✅'],
            'error'   => ['bg-red-50 text-red-800 border-red-200',         '🚫'],
        ];
        $p = $palette[$field['style'] ?? 'info'];
    @endphp
    <div class="rounded-lg border px-4 py-3 text-sm flex gap-2 {{ $p[0] }}">
        <span class="shrink-0">{{ $p[1] }}</span>
        <span>{{ $field['text'] }}</span>
    </div>

@elseif ($type === 'html')
    <div class="prose prose-sm max-w-none">{!! $field['content'] !!}</div>

@elseif ($type === 'divider')
    <flux:separator />

{{-- ── Input fields ── --}}
@else
    <flux:field>
        {{-- Label --}}
        @if (!empty($field['label']))
            <flux:label for="fa_{{ $key }}">
                {{ $field['label'] }}
                @if (!empty($field['required'])) <span class="text-red-500 ml-0.5">*</span> @endif
            </flux:label>
        @endif

        {{-- Hint --}}
        @if (!empty($field['hint']))
            <flux:description>{{ $field['hint'] }}</flux:description>
        @endif

        {{-- Field input --}}
        @if ($type === 'text')
            <flux:input
                type="{{ $field['input_type'] ?? 'text' }}"
                id="fa_{{ $key }}"
                wire:model.live.debounce.300ms="formData.{{ $key }}"
                placeholder="{{ $field['placeholder'] ?? '' }}"
                @if(!empty($field['min_length'])) minlength="{{ $field['min_length'] }}" @endif
                @if(!empty($field['max_length'])) maxlength="{{ $field['max_length'] }}" @endif
                @if(!empty($field['disabled'])) disabled @endif
                :invalid="!empty('{{ $error }}')"
            />

        @elseif ($type === 'textarea')
            <flux:textarea
                id="fa_{{ $key }}"
                wire:model.live.debounce.300ms="formData.{{ $key }}"
                rows="{{ $field['rows'] ?? 4 }}"
                placeholder="{{ $field['placeholder'] ?? '' }}"
                @if(!empty($field['disabled'])) disabled @endif
                :invalid="!empty('{{ $error }}')"
            />

        @elseif ($type === 'select')
            <flux:select
                id="fa_{{ $key }}"
                wire:model.live="formData.{{ $key }}"
                @if(!empty($field['multiple'])) multiple @endif
                @if(!empty($field['disabled'])) disabled @endif
            >
                @if (empty($field['multiple']))
                    <flux:option value="">{{ $field['placeholder'] ?? '— Select —' }}</flux:option>
                @endif
                @foreach (($field['options'] ?? []) as $opt)
                    <flux:option value="{{ $opt['value'] }}">{{ $opt['label'] }}</flux:option>
                @endforeach
            </flux:select>

        @elseif ($type === 'checkbox')
            <div class="{{ !empty($field['inline']) ? 'flex flex-wrap gap-4' : 'space-y-2' }}">
                @foreach (($field['options'] ?? []) as $opt)
                    <flux:checkbox
                        wire:model.live="formData.{{ $key }}"
                        value="{{ $opt['value'] }}"
                        label="{{ $opt['label'] }}"
                        @if(!empty($field['disabled'])) disabled @endif
                    />
                @endforeach
            </div>

        @elseif ($type === 'radio')
            <flux:radio.group
                wire:model.live="formData.{{ $key }}"
                class="{{ !empty($field['inline']) ? 'flex flex-wrap gap-4' : '' }}"
            >
                @foreach (($field['options'] ?? []) as $opt)
                    <flux:radio
                        value="{{ $opt['value'] }}"
                        label="{{ $opt['label'] }}"
                        @if(!empty($field['disabled'])) disabled @endif
                    />
                @endforeach
            </flux:radio.group>

        @elseif ($type === 'datetime')
            @php $inputType = match($field['mode'] ?? 'date') { 'time' => 'time', 'datetime' => 'datetime-local', default => 'date' }; @endphp
            <flux:input
                type="{{ $inputType }}"
                id="fa_{{ $key }}"
                wire:model.live="formData.{{ $key }}"
                @if(!empty($field['min_date'])) min="{{ $field['min_date'] }}" @endif
                @if(!empty($field['max_date'])) max="{{ $field['max_date'] }}" @endif
                @if(!empty($field['disabled'])) disabled @endif
            />

        @elseif ($type === 'number')
            <flux:input
                type="number"
                id="fa_{{ $key }}"
                wire:model.live.debounce.300ms="formData.{{ $key }}"
                placeholder="{{ $field['placeholder'] ?? '' }}"
                @if(!is_null($field['min'] ?? null)) min="{{ $field['min'] }}" @endif
                @if(!is_null($field['max'] ?? null)) max="{{ $field['max'] }}" @endif
                @if(!is_null($field['step'] ?? null)) step="{{ $field['step'] }}" @endif
                @if(!empty($field['disabled'])) disabled @endif
            />

        @elseif ($type === 'toggle')
            <div class="flex items-center gap-3 py-1">
                <flux:switch
                    wire:model.live="formData.{{ $key }}"
                    @if(!empty($field['disabled'])) disabled @endif
                />
                <span class="text-sm text-zinc-600">
                    {{ !empty($formData[$key]) ? ($field['on_label'] ?? 'Yes') : ($field['off_label'] ?? 'No') }}
                </span>
            </div>

        @elseif ($type === 'hidden')
            <input type="hidden" wire:model="formData.{{ $key }}" value="{{ $field['default'] ?? '' }}" />

        @elseif ($type === 'file')
            <input
                type="file"
                id="fa_{{ $key }}"
                wire:model="fileUploads.{{ $key }}"
                @if(!empty($field['multiple'])) multiple @endif
                @if(!empty($field['allowed_types'])) accept="{{ implode(',', $field['allowed_types']) }}" @endif
                @if(!empty($field['disabled'])) disabled @endif
                class="block w-full text-sm text-zinc-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 cursor-pointer border border-zinc-300 rounded-lg"
            />

        @elseif ($type === 'repeater')
            @php $rows = $formData[$key] ?? []; @endphp
            <div class="space-y-3">
                @foreach ($rows as $ri => $rowData)
                    <flux:card class="relative p-4">
                        <div class="grid grid-cols-12 gap-3">
                            @foreach (($field['children'] ?? []) as $child)
                                @php
                                    $ck = $child['key'] ?? null;
                                    $cw = \Tivents\LivewireFormBuilder\Support\AbstractFieldType::widthClass($child['width'] ?? 'full');
                                @endphp
                                @if ($ck)
                                <div class="{{ $cw }}">
                                    <flux:field>
                                        <flux:label>{{ $child['label'] ?? $ck }}</flux:label>
                                        <flux:input
                                            type="{{ $child['input_type'] ?? 'text' }}"
                                            wire:model.live="formData.{{ $key }}.{{ $ri }}.{{ $ck }}"
                                            placeholder="{{ $child['placeholder'] ?? '' }}"
                                            size="sm"
                                        />
                                    </flux:field>
                                </div>
                                @endif
                            @endforeach
                        </div>
                        <flux:button
                            type="button"
                            wire:click="$set('formData.{{ $key }}', array_values(array_filter(formData['{{ $key }}'] ?? [], fn($k) => $k !== {{ $ri }}, ARRAY_FILTER_USE_KEY)))"
                            variant="ghost"
                            size="xs"
                            icon="x-mark"
                            class="absolute top-2 right-2"
                        />
                    </flux:card>
                @endforeach

                @php $maxRows = $field['max_rows'] ?? null; $canAdd = $maxRows === null || count($rows) < $maxRows; @endphp
                @if ($canAdd)
                    <flux:button
                        type="button"
                        wire:click="$set('formData.{{ $key }}', array_merge(formData['{{ $key }}'] ?? [], [[]]))"
                        variant="ghost"
                        icon="plus"
                        size="sm"
                    >
                        {{ $field['add_label'] ?? 'Add item' }}
                    </flux:button>
                @endif
            </div>
        @endif

        {{-- Error --}}
        @if ($error)
            <p class="text-sm text-red-500 flex items-center gap-1 mt-1">
                <flux:icon.exclamation-circle class="size-3.5 shrink-0" />
                {{ $error }}
            </p>
        @endif
    </flux:field>
@endif
