{{-- resources/views/settings/panel.blade.php --}}
{{-- Shared field settings, rendered for the selected field in the builder --}}

@php
    $type      = $field['type'] ?? 'text';
    $isLayout  = in_array($type, ['heading', 'hint', 'html', 'divider', 'row']);
    $hasOptions= in_array($type, ['select', 'checkbox', 'radio']);
    $ci        = $childIndex ?? null;
    $sp        = $ci !== null ? "{$index}.children.{$ci}" : "{$index}";
@endphp

<div class="divide-y divide-gray-100">

    {{-- ── Basic ── --}}
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('livewire-form-builder::messages.settings.basic') }}</h3>

        @if (!$isLayout)
        <div>
            <label class="fa-label">{{ __('livewire-form-builder::messages.settings.label') }}</label>
            <input type="text"
                wire:model.live.debounce.300ms="schema.{{ $sp }}.label"
                class="fa-input" placeholder="{{ __('livewire-form-builder::messages.settings.field_key_placeholder') }}" />
        </div>
        @php $isDuplicateKey = in_array($field['key'] ?? '', $duplicateKeys ?? []); @endphp
        <div>
            <label class="fa-label">{{ __('livewire-form-builder::messages.settings.field_key') }} <span class="text-gray-400 font-normal">{{ __('livewire-form-builder::messages.settings.field_key_unique') }}</span></label>
            <input type="text"
                wire:model.live.debounce.300ms="schema.{{ $sp }}.key"
                class="fa-input font-mono text-xs {{ $isDuplicateKey ? 'border-red-400 ring-1 ring-red-300' : '' }}"
                placeholder="{{ __('livewire-form-builder::messages.settings.field_key_input_placeholder') }}" />
            @if ($isDuplicateKey)
                <p class="mt-1 text-xs text-red-500">{{ __('livewire-form-builder::messages.settings.field_key_duplicate') }}</p>
            @endif
        </div>
        <div>
            <label class="fa-label">{{ __('livewire-form-builder::messages.settings.placeholder') }}</label>
            <input type="text"
                wire:model.live.debounce.300ms="schema.{{ $sp }}.placeholder"
                class="fa-input" />
        </div>
        <div>
            <label class="fa-label">{{ __('livewire-form-builder::messages.settings.hint_text') }}</label>
            <input type="text"
                wire:model.live.debounce.300ms="schema.{{ $sp }}.hint"
                class="fa-input" />
        </div>
        <div class="flex items-center justify-between">
            <label class="fa-label mb-0">{{ __('livewire-form-builder::messages.settings.required') }}</label>
            <input type="checkbox"
                wire:model.live="schema.{{ $sp }}.required"
                class="rounded border-gray-300 text-indigo-600" />
        </div>
        <div class="flex items-center justify-between">
            <label class="fa-label mb-0">{{ __('livewire-form-builder::messages.settings.hidden') }} <span class="text-gray-400 font-normal">{{ __('livewire-form-builder::messages.settings.hidden_note') }}</span></label>
            <input type="checkbox"
                wire:model.live="schema.{{ $sp }}.hidden"
                class="rounded border-gray-300 text-indigo-600" />
        </div>
        @endif

        {{-- Layout-specific: heading text / level --}}
        @if ($type === 'heading')
        <div>
            <label class="fa-label">{{ __('livewire-form-builder::messages.settings.text') }}</label>
            <input type="text" wire:model.live.debounce.300ms="schema.{{ $sp }}.text" class="fa-input" />
        </div>
        <div>
            <label class="fa-label">{{ __('livewire-form-builder::messages.settings.level') }}</label>
            <select wire:model.live="schema.{{ $sp }}.level" class="fa-input">
                @foreach (['h1','h2','h3','h4'] as $h)
                    <option value="{{ $h }}">{{ strtoupper($h) }}</option>
                @endforeach
            </select>
        </div>
        @endif

        @if ($type === 'hint')
        <div>
            <label class="fa-label">{{ __('livewire-form-builder::messages.settings.text') }}</label>
            <textarea wire:model.live.debounce.300ms="schema.{{ $sp }}.text" rows="3" class="fa-input"></textarea>
        </div>
        <div>
            <label class="fa-label">{{ __('livewire-form-builder::messages.settings.style') }}</label>
            <select wire:model.live="schema.{{ $sp }}.style" class="fa-input">
                @foreach (['info','warning','success','error'] as $s)
                    <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        @endif

        @if ($type === 'html')
        <div>
            <label class="fa-label">{{ __('livewire-form-builder::messages.settings.html_content') }}</label>
            <textarea wire:model.live.debounce.300ms="schema.{{ $sp }}.content" rows="5"
                class="fa-input font-mono text-xs"></textarea>
        </div>
        @endif
    </div>

    {{-- ── Layout / Width ── --}}
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('livewire-form-builder::messages.settings.layout') }}</h3>
        <div>
            <label class="fa-label">{{ __('livewire-form-builder::messages.settings.column_width') }}</label>
            <select wire:model.live="schema.{{ $sp }}.width" class="fa-input">
                @foreach ([
                    'full' => __('livewire-form-builder::messages.settings.width.full'),
                    '1/2'  => __('livewire-form-builder::messages.settings.width.half'),
                    '1/3'  => __('livewire-form-builder::messages.settings.width.one_third'),
                    '2/3'  => __('livewire-form-builder::messages.settings.width.two_thirds'),
                    '1/4'  => __('livewire-form-builder::messages.settings.width.one_quarter'),
                    '3/4'  => __('livewire-form-builder::messages.settings.width.three_quarters'),
                ] as $val => $lbl)
                    <option value="{{ $val }}">{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- ── Type-specific ── --}}
    @if ($type === 'number')
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('livewire-form-builder::messages.settings.number') }}</h3>
        <div class="grid grid-cols-3 gap-2">
            <div>
                <label class="fa-label">{{ __('livewire-form-builder::messages.settings.min') }}</label>
                <input type="number" wire:model.live="schema.{{ $sp }}.min" class="fa-input" />
            </div>
            <div>
                <label class="fa-label">{{ __('livewire-form-builder::messages.settings.max') }}</label>
                <input type="number" wire:model.live="schema.{{ $sp }}.max" class="fa-input" />
            </div>
            <div>
                <label class="fa-label">{{ __('livewire-form-builder::messages.settings.step') }}</label>
                <input type="number" wire:model.live="schema.{{ $sp }}.step" class="fa-input" />
            </div>
        </div>
    </div>
    @endif

    @if ($type === 'toggle')
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('livewire-form-builder::messages.settings.toggle_labels') }}</h3>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="fa-label">{{ __('livewire-form-builder::messages.settings.on_label') }}</label>
                <input type="text" wire:model.live.debounce.300ms="schema.{{ $sp }}.on_label" class="fa-input" />
            </div>
            <div>
                <label class="fa-label">{{ __('livewire-form-builder::messages.settings.off_label') }}</label>
                <input type="text" wire:model.live.debounce.300ms="schema.{{ $sp }}.off_label" class="fa-input" />
            </div>
        </div>
    </div>
    @endif

    @if ($type === 'hidden')
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('livewire-form-builder::messages.settings.hidden_field') }}</h3>
        <div>
            <label class="fa-label">{{ __('livewire-form-builder::messages.settings.default_value') }}</label>
            <input type="text" wire:model.live.debounce.300ms="schema.{{ $sp }}.default" class="fa-input" />
        </div>
        <p class="text-xs text-gray-400">{{ __('livewire-form-builder::messages.settings.hidden_field_note') }}</p>
    </div>
    @endif

    @if (in_array($type, ['text', 'email']))
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('livewire-form-builder::messages.settings.input_type') }}</h3>
        <select wire:model.live="schema.{{ $sp }}.input_type" class="fa-input">
            @foreach (['text','email','tel','url','number','password'] as $it)
                <option value="{{ $it }}">{{ ucfirst($it) }}</option>
            @endforeach
        </select>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="fa-label">{{ __('livewire-form-builder::messages.settings.min_length') }}</label>
                <input type="number" wire:model.live="schema.{{ $sp }}.min_length" class="fa-input" />
            </div>
            <div>
                <label class="fa-label">{{ __('livewire-form-builder::messages.settings.max_length') }}</label>
                <input type="number" wire:model.live="schema.{{ $sp }}.max_length" class="fa-input" />
            </div>
        </div>
    </div>
    @endif

    @if ($type === 'textarea')
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('livewire-form-builder::messages.settings.textarea') }}</h3>
        <div>
            <label class="fa-label">{{ __('livewire-form-builder::messages.settings.rows') }}</label>
            <input type="number" wire:model.live="schema.{{ $sp }}.rows" class="fa-input" min="1" max="20" />
        </div>
    </div>
    @endif

    @if ($type === 'datetime')
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('livewire-form-builder::messages.settings.datetime') }}</h3>
        <select wire:model.live="schema.{{ $sp }}.mode" class="fa-input">
            <option value="date">{{ __('livewire-form-builder::messages.settings.datetime.date_only') }}</option>
            <option value="time">{{ __('livewire-form-builder::messages.settings.datetime.time_only') }}</option>
            <option value="datetime">{{ __('livewire-form-builder::messages.settings.datetime.datetime') }}</option>
        </select>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="fa-label">{{ __('livewire-form-builder::messages.settings.min_date') }}</label>
                <input type="date" wire:model.live="schema.{{ $sp }}.min_date" class="fa-input" />
            </div>
            <div>
                <label class="fa-label">{{ __('livewire-form-builder::messages.settings.max_date') }}</label>
                <input type="date" wire:model.live="schema.{{ $sp }}.max_date" class="fa-input" />
            </div>
        </div>
    </div>
    @endif

    @if ($type === 'file')
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('livewire-form-builder::messages.settings.file_upload') }}</h3>
        <div class="flex items-center justify-between">
            <label class="fa-label mb-0">{{ __('livewire-form-builder::messages.settings.allow_multiple') }}</label>
            <input type="checkbox" wire:model.live="schema.{{ $sp }}.multiple" class="rounded border-gray-300 text-indigo-600" />
        </div>
        <div>
            <label class="fa-label">{{ __('livewire-form-builder::messages.settings.max_size_kb') }}</label>
            <input type="number" wire:model.live="schema.{{ $sp }}.max_size_kb" class="fa-input" />
        </div>
        <div>
            <label class="fa-label">{{ __('livewire-form-builder::messages.settings.max_files') }}</label>
            <input type="number" wire:model.live="schema.{{ $sp }}.max_files" class="fa-input" min="1" />
        </div>
    </div>
    @endif

    {{-- ── Options (select / checkbox / radio) ── --}}
    @if ($hasOptions)
    <div class="p-4 space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('livewire-form-builder::messages.settings.options') }}</h3>
            <button wire:click="addFieldOption({{ $index }}, {{ $ci !== null ? $ci : 'null' }})" type="button"
                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">{{ __('livewire-form-builder::messages.settings.options.add') }}</button>
        </div>
        @if (count($field['options'] ?? []) > 0)
        <div class="flex items-center gap-2 px-0.5">
            <span class="flex-1 text-[10px] font-medium text-gray-400 uppercase tracking-wider">{{ __('livewire-form-builder::messages.settings.options.label_column') }}</span>
            <span class="w-24 text-[10px] font-medium text-gray-400 uppercase tracking-wider">{{ __('livewire-form-builder::messages.settings.options.value_column') }}</span>
            <span class="w-3.5 flex-none"></span>
        </div>
        @endif
        @foreach (($field['options'] ?? []) as $oi => $option)
        <div class="flex items-center gap-2">
            <div class="flex-1 min-w-0">
                <input type="text"
                    wire:model.live.debounce.300ms="schema.{{ $sp }}.options.{{ $oi }}.label"
                    class="fa-input" placeholder="{{ __('livewire-form-builder::messages.settings.options.label_placeholder') }}" />
            </div>
            <div class="flex-none" style="width:6rem">
                <input type="text"
                    wire:model.live.debounce.300ms="schema.{{ $sp }}.options.{{ $oi }}.value"
                    class="fa-input font-mono text-xs" placeholder="{{ __('livewire-form-builder::messages.settings.options.value_placeholder') }}" />
            </div>
            <button wire:click="removeFieldOption({{ $index }}, {{ $oi }}, {{ $ci !== null ? $ci : 'null' }})" type="button"
                class="text-gray-300 hover:text-red-500 transition-colors flex-none">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @endforeach

        @if ($type === 'select')
        <div class="flex items-center justify-between pt-1">
            <label class="fa-label mb-0">{{ __('livewire-form-builder::messages.settings.multi_select') }}</label>
            <input type="checkbox" wire:model.live="schema.{{ $sp }}.multiple" class="rounded border-gray-300 text-indigo-600" />
        </div>
        <div class="flex items-center justify-between">
            <label class="fa-label mb-0">{{ __('livewire-form-builder::messages.settings.searchable') }}</label>
            <input type="checkbox" wire:model.live="schema.{{ $sp }}.searchable" class="rounded border-gray-300 text-indigo-600" />
        </div>
        @endif

        @if (in_array($type, ['checkbox', 'radio']))
        <div class="flex items-center justify-between pt-1">
            <label class="fa-label mb-0">{{ __('livewire-form-builder::messages.settings.inline_layout') }}</label>
            <input type="checkbox" wire:model.live="schema.{{ $sp }}.inline" class="rounded border-gray-300 text-indigo-600" />
        </div>
        @endif
    </div>
    @endif

    {{-- ── Row children ── --}}
    @if ($type === 'row')
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('livewire-form-builder::messages.settings.fields_in_row') }}</h3>
        <div class="space-y-1.5">
            @php $childCount = count($field['children'] ?? []); @endphp
            @forelse (($field['children'] ?? []) as $ci2 => $child)
                <div class="flex items-center gap-1 bg-gray-50 rounded-lg px-2 py-2">
                    {{-- Move up/down --}}
                    <div class="flex flex-col gap-0.5 flex-none">
                        <button wire:click="moveChildInRow({{ $index }}, {{ $ci2 }}, {{ $ci2 - 1 }})" type="button"
                            title="{{ __('livewire-form-builder::messages.builder.action.move_left') }}" {{ $ci2 === 0 ? 'disabled' : '' }}
                            class="p-0.5 rounded text-gray-300 hover:text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                        </button>
                        <button wire:click="moveChildInRow({{ $index }}, {{ $ci2 }}, {{ $ci2 + 1 }})" type="button"
                            title="{{ __('livewire-form-builder::messages.builder.action.move_right') }}" {{ $ci2 === $childCount - 1 ? 'disabled' : '' }}
                            class="p-0.5 rounded text-gray-300 hover:text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                    <span class="flex-1 text-xs text-gray-700 font-medium truncate">{{ $child['label'] ?? $child['key'] }}</span>
                    <span class="text-[10px] text-gray-400 font-mono flex-none">{{ $child['type'] }}</span>
                    <button wire:click="selectField({{ $index }}, {{ $ci2 }})" type="button" title="{{ __('livewire-form-builder::messages.builder.action.edit') }}"
                        class="p-0.5 text-gray-400 hover:text-indigo-500 transition-colors flex-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button wire:click="removeFieldFromRow({{ $index }}, {{ $ci2 }})" type="button" title="{{ __('livewire-form-builder::messages.builder.action.remove') }}"
                        class="p-0.5 text-gray-300 hover:text-red-500 transition-colors flex-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @empty
                <p class="text-xs text-gray-400 italic">{{ __('livewire-form-builder::messages.settings.row.no_fields') }}</p>
            @endforelse
        </div>
        <div class="flex flex-wrap gap-1 pt-1">
            @foreach (['text','select','checkbox','radio','textarea','number','datetime','toggle','file'] as $ct)
                <button wire:click="addFieldToRow({{ $index }}, '{{ $ct }}')" type="button"
                    class="text-xs px-2 py-1 bg-indigo-50 text-indigo-700 rounded hover:bg-indigo-100 transition-colors">
                    + {{ ucfirst($ct) }}
                </button>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Repeater children ── --}}
    @if ($type === 'repeater')
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('livewire-form-builder::messages.settings.repeater') }}</h3>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="fa-label">{{ __('livewire-form-builder::messages.settings.min_rows') }}</label>
                <input type="number" wire:model.live="schema.{{ $sp }}.min_rows" class="fa-input" min="0" />
            </div>
            <div>
                <label class="fa-label">{{ __('livewire-form-builder::messages.settings.max_rows') }}</label>
                <input type="number" wire:model.live="schema.{{ $sp }}.max_rows" class="fa-input" min="1" />
            </div>
        </div>
        <div>
            <label class="fa-label">{{ __('livewire-form-builder::messages.settings.add_button_label') }}</label>
            <input type="text" wire:model.live.debounce.300ms="schema.{{ $sp }}.add_label" class="fa-input" />
        </div>

        <div class="space-y-2">
            <p class="text-xs text-gray-500 font-medium">{{ __('livewire-form-builder::messages.settings.child_fields') }}</p>
            @foreach (($field['children'] ?? []) as $ci => $child)
                <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
                    <span class="flex-1 text-xs text-gray-700 font-medium">{{ $child['label'] }}</span>
                    <span class="text-[10px] text-gray-400 font-mono">{{ $child['type'] }}</span>
                    <button wire:click="deleteChildField({{ $index }}, {{ $ci }})" type="button" class="text-gray-300 hover:text-red-500">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endforeach
            <div class="flex flex-wrap gap-1 pt-1">
                @foreach (['text','select','checkbox','datetime'] as $ct)
                    <button wire:click="addChildField({{ $index }}, '{{ $ct }}')" type="button"
                        class="text-xs px-2 py-1 bg-indigo-50 text-indigo-700 rounded hover:bg-indigo-100 transition-colors">
                        + {{ ucfirst($ct) }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ── Conditional Logic ── --}}
    @if (!$isLayout)
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('livewire-form-builder::messages.settings.conditions') }}</h3>

        <div class="flex items-center gap-2">
            <label class="fa-label mb-0 flex-none">{{ __('livewire-form-builder::messages.settings.conditions.action') }}</label>
            <select wire:model.live="schema.{{ $sp }}.conditions.action" class="fa-input">
                <option value="show">{{ __('livewire-form-builder::messages.settings.conditions.show') }}</option>
                <option value="hide">{{ __('livewire-form-builder::messages.settings.conditions.hide') }}</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <label class="fa-label mb-0 flex-none">{{ __('livewire-form-builder::messages.settings.conditions.logic') }}</label>
            <select wire:model.live="schema.{{ $sp }}.conditions.logic" class="fa-input">
                <option value="and">{{ __('livewire-form-builder::messages.settings.conditions.and') }}</option>
                <option value="or">{{ __('livewire-form-builder::messages.settings.conditions.or') }}</option>
            </select>
        </div>

        @foreach (($field['conditions']['rules'] ?? []) as $ri => $rule)
        <div class="space-y-1.5 bg-gray-50 rounded-lg p-2.5">
            <select wire:model.live="schema.{{ $sp }}.conditions.rules.{{ $ri }}.field" class="fa-input text-xs">
                <option value="">{{ __('livewire-form-builder::messages.settings.conditions.pick_field') }}</option>
                @foreach ($fieldKeys as $fk => $fl)
                    <option value="{{ $fk }}">{{ $fl }}</option>
                @endforeach
            </select>
            <select wire:model.live="schema.{{ $sp }}.conditions.rules.{{ $ri }}.operator" class="fa-input text-xs">
                @foreach ([
                    '=='        => __('livewire-form-builder::messages.settings.conditions.equals'),
                    '!='        => __('livewire-form-builder::messages.settings.conditions.not_equals'),
                    'contains'  => __('livewire-form-builder::messages.settings.conditions.contains'),
                    'empty'     => __('livewire-form-builder::messages.settings.conditions.is_empty'),
                    'not_empty' => __('livewire-form-builder::messages.settings.conditions.not_empty'),
                    '>'         => '>',
                    '<'         => '<',
                ] as $op => $ol)
                    <option value="{{ $op }}">{{ $ol }}</option>
                @endforeach
            </select>
            @if (!in_array($rule['operator'] ?? '', ['empty', 'not_empty']))
            <input type="text"
                wire:model.live.debounce.300ms="schema.{{ $sp }}.conditions.rules.{{ $ri }}.value"
                class="fa-input text-xs" placeholder="{{ __('livewire-form-builder::messages.settings.conditions.value_placeholder') }}" />
            @endif
            <button wire:click="removeCondition({{ $index }}, {{ $ri }}, {{ $ci !== null ? $ci : 'null' }})" type="button"
                class="text-xs text-red-400 hover:text-red-600">{{ __('livewire-form-builder::messages.settings.conditions.remove_rule') }}</button>
        </div>
        @endforeach

        <button wire:click="addCondition({{ $index }}, {{ $ci !== null ? $ci : 'null' }})" type="button"
            class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">{{ __('livewire-form-builder::messages.settings.conditions.add') }}</button>
    </div>
    @endif

</div>

@push('styles')
<style>
.fa-label { @apply block text-xs font-medium text-gray-600 mb-1; }
.fa-input { @apply w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-indigo-400 focus:border-indigo-400 transition; }
</style>
@endpush
