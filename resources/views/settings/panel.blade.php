{{-- resources/views/settings/panel.blade.php --}}
{{-- Shared field settings, rendered for the selected field in the builder --}}

@php
    $type       = $field['type'] ?? 'text';
    $isLayout   = in_array($type, ['heading', 'hint', 'html']);
    $hasOptions = in_array($type, ['select', 'checkbox', 'radio']);
@endphp

<div class="divide-y divide-zinc-100">

    {{-- ── Basic ── --}}
    <div class="p-4 space-y-3">
        <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Basic</p>

        @if (!$isLayout)
        <flux:field>
            <flux:label>Label</flux:label>
            <flux:input size="sm" wire:model.live.debounce.300ms="schema.{{ $index }}.label" placeholder="Field label" />
        </flux:field>
        <flux:field>
            <flux:label>Field Key <span class="text-zinc-400 font-normal">(unique)</span></flux:label>
            <flux:input size="sm" wire:model.live.debounce.300ms="schema.{{ $index }}.key" placeholder="field_key" class="font-mono" />
        </flux:field>
        <flux:field>
            <flux:label>Placeholder</flux:label>
            <flux:input size="sm" wire:model.live.debounce.300ms="schema.{{ $index }}.placeholder" />
        </flux:field>
        <flux:field>
            <flux:label>Hint text</flux:label>
            <flux:input size="sm" wire:model.live.debounce.300ms="schema.{{ $index }}.hint" />
        </flux:field>
        <flux:checkbox wire:model.live="schema.{{ $index }}.required" label="Required" />
        @endif

        {{-- Layout-specific: heading text / level --}}
        @if ($type === 'heading')
        <flux:field>
            <flux:label>Text</flux:label>
            <flux:input size="sm" wire:model.live.debounce.300ms="schema.{{ $index }}.text" />
        </flux:field>
        <flux:field>
            <flux:label>Level</flux:label>
            <flux:select wire:model.live="schema.{{ $index }}.level" size="sm">
                @foreach (['h1', 'h2', 'h3', 'h4'] as $h)
                    <flux:option value="{{ $h }}">{{ strtoupper($h) }}</flux:option>
                @endforeach
            </flux:select>
        </flux:field>
        @endif

        @if ($type === 'hint')
        <flux:field>
            <flux:label>Text</flux:label>
            <flux:textarea wire:model.live.debounce.300ms="schema.{{ $index }}.text" rows="3" />
        </flux:field>
        <flux:field>
            <flux:label>Style</flux:label>
            <flux:select wire:model.live="schema.{{ $index }}.style" size="sm">
                @foreach (['info', 'warning', 'success', 'error'] as $s)
                    <flux:option value="{{ $s }}">{{ ucfirst($s) }}</flux:option>
                @endforeach
            </flux:select>
        </flux:field>
        @endif

        @if ($type === 'html')
        <flux:field>
            <flux:label>HTML Content</flux:label>
            <flux:textarea wire:model.live.debounce.300ms="schema.{{ $index }}.content" rows="5" class="font-mono text-xs" />
        </flux:field>
        @endif
    </div>

    {{-- ── Layout / Width ── --}}
    <div class="p-4 space-y-3">
        <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Layout</p>
        <flux:field>
            <flux:label>Column Width</flux:label>
            <flux:select wire:model.live="schema.{{ $index }}.width" size="sm">
                <flux:option value="full">Full width</flux:option>
                <flux:option value="1/2">Half (1/2)</flux:option>
                <flux:option value="1/3">One Third (1/3)</flux:option>
                <flux:option value="2/3">Two Thirds (2/3)</flux:option>
                <flux:option value="1/4">One Quarter (1/4)</flux:option>
                <flux:option value="3/4">Three Quarters (3/4)</flux:option>
            </flux:select>
        </flux:field>
    </div>

    {{-- ── Type-specific ── --}}
    @if ($type === 'number')
    <div class="p-4 space-y-3">
        <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Number</p>
        <div class="grid grid-cols-3 gap-2">
            <flux:field>
                <flux:label>Min</flux:label>
                <flux:input size="sm" type="number" wire:model.live="schema.{{ $index }}.min" />
            </flux:field>
            <flux:field>
                <flux:label>Max</flux:label>
                <flux:input size="sm" type="number" wire:model.live="schema.{{ $index }}.max" />
            </flux:field>
            <flux:field>
                <flux:label>Step</flux:label>
                <flux:input size="sm" type="number" wire:model.live="schema.{{ $index }}.step" />
            </flux:field>
        </div>
    </div>
    @endif

    @if ($type === 'toggle')
    <div class="p-4 space-y-3">
        <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Toggle Labels</p>
        <div class="grid grid-cols-2 gap-2">
            <flux:field>
                <flux:label>On label</flux:label>
                <flux:input size="sm" wire:model.live.debounce.300ms="schema.{{ $index }}.on_label" />
            </flux:field>
            <flux:field>
                <flux:label>Off label</flux:label>
                <flux:input size="sm" wire:model.live.debounce.300ms="schema.{{ $index }}.off_label" />
            </flux:field>
        </div>
    </div>
    @endif

    @if ($type === 'hidden')
    <div class="p-4 space-y-3">
        <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Hidden Field</p>
        <flux:field>
            <flux:label>Default value</flux:label>
            <flux:input size="sm" wire:model.live.debounce.300ms="schema.{{ $index }}.default" />
        </flux:field>
        <flux:text class="text-xs text-zinc-400">Hidden fields are not shown to users but their value is submitted.</flux:text>
    </div>
    @endif

    @if (in_array($type, ['text', 'email']))
    <div class="p-4 space-y-3">
        <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Input Type</p>
        <flux:select wire:model.live="schema.{{ $index }}.input_type" size="sm">
            @foreach (['text', 'email', 'tel', 'url', 'number', 'password'] as $it)
                <flux:option value="{{ $it }}">{{ ucfirst($it) }}</flux:option>
            @endforeach
        </flux:select>
        <div class="grid grid-cols-2 gap-2">
            <flux:field>
                <flux:label>Min length</flux:label>
                <flux:input size="sm" type="number" wire:model.live="schema.{{ $index }}.min_length" />
            </flux:field>
            <flux:field>
                <flux:label>Max length</flux:label>
                <flux:input size="sm" type="number" wire:model.live="schema.{{ $index }}.max_length" />
            </flux:field>
        </div>
    </div>
    @endif

    @if ($type === 'textarea')
    <div class="p-4 space-y-3">
        <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Textarea</p>
        <flux:field>
            <flux:label>Rows</flux:label>
            <flux:input size="sm" type="number" wire:model.live="schema.{{ $index }}.rows" min="1" max="20" />
        </flux:field>
    </div>
    @endif

    @if ($type === 'datetime')
    <div class="p-4 space-y-3">
        <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Date / Time</p>
        <flux:select wire:model.live="schema.{{ $index }}.mode" size="sm">
            <flux:option value="date">Date only</flux:option>
            <flux:option value="time">Time only</flux:option>
            <flux:option value="datetime">Date &amp; Time</flux:option>
        </flux:select>
        <div class="grid grid-cols-2 gap-2">
            <flux:field>
                <flux:label>Min date</flux:label>
                <flux:input size="sm" type="date" wire:model.live="schema.{{ $index }}.min_date" />
            </flux:field>
            <flux:field>
                <flux:label>Max date</flux:label>
                <flux:input size="sm" type="date" wire:model.live="schema.{{ $index }}.max_date" />
            </flux:field>
        </div>
    </div>
    @endif

    @if ($type === 'file')
    <div class="p-4 space-y-3">
        <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">File Upload</p>
        <flux:checkbox wire:model.live="schema.{{ $index }}.multiple" label="Allow multiple" />
        <flux:field>
            <flux:label>Max size (KB)</flux:label>
            <flux:input size="sm" type="number" wire:model.live="schema.{{ $index }}.max_size_kb" />
        </flux:field>
        <flux:field>
            <flux:label>Max files</flux:label>
            <flux:input size="sm" type="number" wire:model.live="schema.{{ $index }}.max_files" min="1" />
        </flux:field>
    </div>
    @endif

    {{-- ── Options (select / checkbox / radio) ── --}}
    @if ($hasOptions)
    <div class="p-4 space-y-3">
        <div class="flex items-center justify-between">
            <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Options</p>
            <flux:button wire:click="addFieldOption({{ $index }})" variant="ghost" size="xs" icon="plus">
                Add
            </flux:button>
        </div>
        @foreach (($field['options'] ?? []) as $oi => $option)
        <div class="flex items-center gap-2">
            <flux:input
                size="sm"
                wire:model.live.debounce.300ms="schema.{{ $index }}.options.{{ $oi }}.label"
                placeholder="Label"
                class="flex-1"
            />
            <flux:input
                size="sm"
                wire:model.live.debounce.300ms="schema.{{ $index }}.options.{{ $oi }}.value"
                placeholder="value"
                class="w-24 font-mono"
            />
            <flux:button
                wire:click="removeFieldOption({{ $index }}, {{ $oi }})"
                variant="ghost"
                size="xs"
                icon="x-mark"
            />
        </div>
        @endforeach

        @if ($type === 'select')
        <div class="space-y-2 pt-1">
            <flux:checkbox wire:model.live="schema.{{ $index }}.multiple" label="Multi-select" />
            <flux:checkbox wire:model.live="schema.{{ $index }}.searchable" label="Searchable" />
        </div>
        @endif

        @if (in_array($type, ['checkbox', 'radio']))
        <div class="pt-1">
            <flux:checkbox wire:model.live="schema.{{ $index }}.inline" label="Inline layout" />
        </div>
        @endif
    </div>
    @endif

    {{-- ── Repeater children ── --}}
    @if ($type === 'repeater')
    <div class="p-4 space-y-3">
        <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Repeater</p>
        <div class="grid grid-cols-2 gap-2">
            <flux:field>
                <flux:label>Min rows</flux:label>
                <flux:input size="sm" type="number" wire:model.live="schema.{{ $index }}.min_rows" min="0" />
            </flux:field>
            <flux:field>
                <flux:label>Max rows</flux:label>
                <flux:input size="sm" type="number" wire:model.live="schema.{{ $index }}.max_rows" min="1" />
            </flux:field>
        </div>
        <flux:field>
            <flux:label>Add button label</flux:label>
            <flux:input size="sm" wire:model.live.debounce.300ms="schema.{{ $index }}.add_label" />
        </flux:field>

        <div class="space-y-2">
            <p class="text-xs text-zinc-500 font-medium">Child fields</p>
            @foreach (($field['children'] ?? []) as $ci => $child)
                <div class="flex items-center gap-2 bg-zinc-50 rounded-lg px-3 py-2">
                    <span class="flex-1 text-xs text-zinc-700 font-medium">{{ $child['label'] }}</span>
                    <span class="text-[10px] text-zinc-400 font-mono">{{ $child['type'] }}</span>
                    <flux:button
                        wire:click="deleteChildField({{ $index }}, {{ $ci }})"
                        variant="ghost"
                        size="xs"
                        icon="x-mark"
                    />
                </div>
            @endforeach
            <div class="flex flex-wrap gap-1 pt-1">
                @foreach (['text', 'select', 'checkbox', 'datetime'] as $ct)
                    <flux:button
                        wire:click="addChildField({{ $index }}, '{{ $ct }}')"
                        variant="ghost"
                        size="xs"
                        icon="plus"
                    >
                        {{ ucfirst($ct) }}
                    </flux:button>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ── Conditional Logic ── --}}
    @if (!$isLayout)
    <div class="p-4 space-y-3">
        <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Conditional Logic</p>

        <flux:field>
            <flux:label>Action</flux:label>
            <flux:select wire:model.live="schema.{{ $index }}.conditions.action" size="sm">
                <flux:option value="show">Show this field</flux:option>
                <flux:option value="hide">Hide this field</flux:option>
            </flux:select>
        </flux:field>
        <flux:field>
            <flux:label>Logic</flux:label>
            <flux:select wire:model.live="schema.{{ $index }}.conditions.logic" size="sm">
                <flux:option value="and">All rules match (AND)</flux:option>
                <flux:option value="or">Any rule matches (OR)</flux:option>
            </flux:select>
        </flux:field>

        @foreach (($field['conditions']['rules'] ?? []) as $ri => $rule)
        <div class="space-y-1.5 bg-zinc-50 rounded-lg p-2.5">
            <flux:select wire:model.live="schema.{{ $index }}.conditions.rules.{{ $ri }}.field" size="sm">
                <flux:option value="">— pick field —</flux:option>
                @foreach ($fieldKeys as $fk => $fl)
                    <flux:option value="{{ $fk }}">{{ $fl }}</flux:option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="schema.{{ $index }}.conditions.rules.{{ $ri }}.operator" size="sm">
                @foreach (['==' => 'equals', '!=' => 'not equals', 'contains' => 'contains', 'empty' => 'is empty', 'not_empty' => 'is not empty', '>' => '>', '<' => '<'] as $op => $ol)
                    <flux:option value="{{ $op }}">{{ $ol }}</flux:option>
                @endforeach
            </flux:select>
            @if (!in_array($rule['operator'] ?? '', ['empty', 'not_empty']))
            <flux:input
                size="sm"
                wire:model.live.debounce.300ms="schema.{{ $index }}.conditions.rules.{{ $ri }}.value"
                placeholder="Value…"
            />
            @endif
            <flux:button
                wire:click="removeCondition({{ $index }}, {{ $ri }})"
                variant="ghost"
                size="xs"
                icon="trash"
            >
                Remove rule
            </flux:button>
        </div>
        @endforeach

        <flux:button
            wire:click="addCondition({{ $index }})"
            variant="ghost"
            size="xs"
            icon="plus"
        >
            Add condition
        </flux:button>
    </div>
    @endif

</div>
