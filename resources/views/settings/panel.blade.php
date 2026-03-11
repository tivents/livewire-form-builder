{{-- resources/views/settings/panel.blade.php --}}
{{-- Shared field settings, rendered for the selected field in the builder --}}

@php
    $type      = $field['type'] ?? 'text';
    $isLayout  = in_array($type, ['heading', 'hint', 'html']);
    $hasOptions= in_array($type, ['select', 'checkbox', 'radio']);
@endphp

<div class="divide-y divide-gray-100">

    {{-- ── Basic ── --}}
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Basic</h3>

        @if (!$isLayout)
        <div>
            <label class="fa-label">Label</label>
            <input type="text"
                wire:model.live.debounce.300ms="schema.{{ $index }}.label"
                class="fa-input" placeholder="Field label" />
        </div>
        <div>
            <label class="fa-label">Field Key <span class="text-gray-400 font-normal">(unique)</span></label>
            <input type="text"
                wire:model.live.debounce.300ms="schema.{{ $index }}.key"
                class="fa-input font-mono text-xs" placeholder="field_key" />
        </div>
        <div>
            <label class="fa-label">Placeholder</label>
            <input type="text"
                wire:model.live.debounce.300ms="schema.{{ $index }}.placeholder"
                class="fa-input" />
        </div>
        <div>
            <label class="fa-label">Hint text</label>
            <input type="text"
                wire:model.live.debounce.300ms="schema.{{ $index }}.hint"
                class="fa-input" />
        </div>
        <div class="flex items-center justify-between">
            <label class="fa-label mb-0">Required</label>
            <input type="checkbox"
                wire:model.live="schema.{{ $index }}.required"
                class="rounded border-gray-300 text-indigo-600" />
        </div>
        @endif

        {{-- Layout-specific: heading text / level --}}
        @if ($type === 'heading')
        <div>
            <label class="fa-label">Text</label>
            <input type="text" wire:model.live.debounce.300ms="schema.{{ $index }}.text" class="fa-input" />
        </div>
        <div>
            <label class="fa-label">Level</label>
            <select wire:model.live="schema.{{ $index }}.level" class="fa-input">
                @foreach (['h1','h2','h3','h4'] as $h)
                    <option value="{{ $h }}">{{ strtoupper($h) }}</option>
                @endforeach
            </select>
        </div>
        @endif

        @if ($type === 'hint')
        <div>
            <label class="fa-label">Text</label>
            <textarea wire:model.live.debounce.300ms="schema.{{ $index }}.text" rows="3" class="fa-input"></textarea>
        </div>
        <div>
            <label class="fa-label">Style</label>
            <select wire:model.live="schema.{{ $index }}.style" class="fa-input">
                @foreach (['info','warning','success','error'] as $s)
                    <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        @endif

        @if ($type === 'html')
        <div>
            <label class="fa-label">HTML Content</label>
            <textarea wire:model.live.debounce.300ms="schema.{{ $index }}.content" rows="5"
                class="fa-input font-mono text-xs"></textarea>
        </div>
        @endif
    </div>

    {{-- ── Layout / Width ── --}}
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Layout</h3>
        <div>
            <label class="fa-label">Column Width</label>
            <select wire:model.live="schema.{{ $index }}.width" class="fa-input">
                <option value="full">Full width</option>
                <option value="1/2">Half (1/2)</option>
                <option value="1/3">One Third (1/3)</option>
                <option value="2/3">Two Thirds (2/3)</option>
                <option value="1/4">One Quarter (1/4)</option>
                <option value="3/4">Three Quarters (3/4)</option>
            </select>
        </div>
    </div>

    {{-- ── Type-specific ── --}}
    @if ($type === 'number')
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Number</h3>
        <div class="grid grid-cols-3 gap-2">
            <div>
                <label class="fa-label">Min</label>
                <input type="number" wire:model.live="schema.{{ $index }}.min" class="fa-input" />
            </div>
            <div>
                <label class="fa-label">Max</label>
                <input type="number" wire:model.live="schema.{{ $index }}.max" class="fa-input" />
            </div>
            <div>
                <label class="fa-label">Step</label>
                <input type="number" wire:model.live="schema.{{ $index }}.step" class="fa-input" />
            </div>
        </div>
    </div>
    @endif

    @if ($type === 'toggle')
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Toggle Labels</h3>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="fa-label">On label</label>
                <input type="text" wire:model.live.debounce.300ms="schema.{{ $index }}.on_label" class="fa-input" />
            </div>
            <div>
                <label class="fa-label">Off label</label>
                <input type="text" wire:model.live.debounce.300ms="schema.{{ $index }}.off_label" class="fa-input" />
            </div>
        </div>
    </div>
    @endif

    @if ($type === 'hidden')
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Hidden Field</h3>
        <div>
            <label class="fa-label">Default value</label>
            <input type="text" wire:model.live.debounce.300ms="schema.{{ $index }}.default" class="fa-input" />
        </div>
        <p class="text-xs text-gray-400">Hidden fields are not shown to users but their value is submitted.</p>
    </div>
    @endif

    @if (in_array($type, ['text', 'email']))
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Input Type</h3>
        <select wire:model.live="schema.{{ $index }}.input_type" class="fa-input">
            @foreach (['text','email','tel','url','number','password'] as $it)
                <option value="{{ $it }}">{{ ucfirst($it) }}</option>
            @endforeach
        </select>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="fa-label">Min length</label>
                <input type="number" wire:model.live="schema.{{ $index }}.min_length" class="fa-input" />
            </div>
            <div>
                <label class="fa-label">Max length</label>
                <input type="number" wire:model.live="schema.{{ $index }}.max_length" class="fa-input" />
            </div>
        </div>
    </div>
    @endif

    @if ($type === 'textarea')
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Textarea</h3>
        <div>
            <label class="fa-label">Rows</label>
            <input type="number" wire:model.live="schema.{{ $index }}.rows" class="fa-input" min="1" max="20" />
        </div>
    </div>
    @endif

    @if ($type === 'datetime')
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Date / Time</h3>
        <select wire:model.live="schema.{{ $index }}.mode" class="fa-input">
            <option value="date">Date only</option>
            <option value="time">Time only</option>
            <option value="datetime">Date & Time</option>
        </select>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="fa-label">Min date</label>
                <input type="date" wire:model.live="schema.{{ $index }}.min_date" class="fa-input" />
            </div>
            <div>
                <label class="fa-label">Max date</label>
                <input type="date" wire:model.live="schema.{{ $index }}.max_date" class="fa-input" />
            </div>
        </div>
    </div>
    @endif

    @if ($type === 'file')
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">File Upload</h3>
        <div class="flex items-center justify-between">
            <label class="fa-label mb-0">Allow multiple</label>
            <input type="checkbox" wire:model.live="schema.{{ $index }}.multiple" class="rounded border-gray-300 text-indigo-600" />
        </div>
        <div>
            <label class="fa-label">Max size (KB)</label>
            <input type="number" wire:model.live="schema.{{ $index }}.max_size_kb" class="fa-input" />
        </div>
        <div>
            <label class="fa-label">Max files</label>
            <input type="number" wire:model.live="schema.{{ $index }}.max_files" class="fa-input" min="1" />
        </div>
    </div>
    @endif

    {{-- ── Options (select / checkbox / radio) ── --}}
    @if ($hasOptions)
    <div class="p-4 space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Options</h3>
            <button wire:click="addFieldOption({{ $index }})" type="button"
                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">+ Add</button>
        </div>
        @foreach (($field['options'] ?? []) as $oi => $option)
        <div class="flex items-center gap-2">
            <input type="text"
                wire:model.live.debounce.300ms="schema.{{ $index }}.options.{{ $oi }}.label"
                class="fa-input flex-1" placeholder="Label" />
            <input type="text"
                wire:model.live.debounce.300ms="schema.{{ $index }}.options.{{ $oi }}.value"
                class="fa-input w-24 font-mono text-xs" placeholder="value" />
            <button wire:click="removeFieldOption({{ $index }}, {{ $oi }})" type="button"
                class="text-gray-300 hover:text-red-500 transition-colors flex-none">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @endforeach

        @if ($type === 'select')
        <div class="flex items-center justify-between pt-1">
            <label class="fa-label mb-0">Multi-select</label>
            <input type="checkbox" wire:model.live="schema.{{ $index }}.multiple" class="rounded border-gray-300 text-indigo-600" />
        </div>
        <div class="flex items-center justify-between">
            <label class="fa-label mb-0">Searchable</label>
            <input type="checkbox" wire:model.live="schema.{{ $index }}.searchable" class="rounded border-gray-300 text-indigo-600" />
        </div>
        @endif

        @if (in_array($type, ['checkbox', 'radio']))
        <div class="flex items-center justify-between pt-1">
            <label class="fa-label mb-0">Inline layout</label>
            <input type="checkbox" wire:model.live="schema.{{ $index }}.inline" class="rounded border-gray-300 text-indigo-600" />
        </div>
        @endif
    </div>
    @endif

    {{-- ── Repeater children ── --}}
    @if ($type === 'repeater')
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Repeater</h3>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="fa-label">Min rows</label>
                <input type="number" wire:model.live="schema.{{ $index }}.min_rows" class="fa-input" min="0" />
            </div>
            <div>
                <label class="fa-label">Max rows</label>
                <input type="number" wire:model.live="schema.{{ $index }}.max_rows" class="fa-input" min="1" />
            </div>
        </div>
        <div>
            <label class="fa-label">Add button label</label>
            <input type="text" wire:model.live.debounce.300ms="schema.{{ $index }}.add_label" class="fa-input" />
        </div>

        <div class="space-y-2">
            <p class="text-xs text-gray-500 font-medium">Child fields</p>
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
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Conditional Logic</h3>

        <div class="flex items-center gap-2">
            <label class="fa-label mb-0 flex-none">Action</label>
            <select wire:model.live="schema.{{ $index }}.conditions.action" class="fa-input">
                <option value="show">Show this field</option>
                <option value="hide">Hide this field</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <label class="fa-label mb-0 flex-none">Logic</label>
            <select wire:model.live="schema.{{ $index }}.conditions.logic" class="fa-input">
                <option value="and">All rules match (AND)</option>
                <option value="or">Any rule matches (OR)</option>
            </select>
        </div>

        @foreach (($field['conditions']['rules'] ?? []) as $ri => $rule)
        <div class="space-y-1.5 bg-gray-50 rounded-lg p-2.5">
            <select wire:model.live="schema.{{ $index }}.conditions.rules.{{ $ri }}.field" class="fa-input text-xs">
                <option value="">— pick field —</option>
                @foreach ($fieldKeys as $fk => $fl)
                    <option value="{{ $fk }}">{{ $fl }}</option>
                @endforeach
            </select>
            <select wire:model.live="schema.{{ $index }}.conditions.rules.{{ $ri }}.operator" class="fa-input text-xs">
                @foreach (['==' => 'equals', '!=' => 'not equals', 'contains' => 'contains', 'empty' => 'is empty', 'not_empty' => 'is not empty', '>' => '>', '<' => '<'] as $op => $ol)
                    <option value="{{ $op }}">{{ $ol }}</option>
                @endforeach
            </select>
            @if (!in_array($rule['operator'] ?? '', ['empty', 'not_empty']))
            <input type="text"
                wire:model.live.debounce.300ms="schema.{{ $index }}.conditions.rules.{{ $ri }}.value"
                class="fa-input text-xs" placeholder="Value…" />
            @endif
            <button wire:click="removeCondition({{ $index }}, {{ $ri }})" type="button"
                class="text-xs text-red-400 hover:text-red-600">Remove rule</button>
        </div>
        @endforeach

        <button wire:click="addCondition({{ $index }})" type="button"
            class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">+ Add condition</button>
    </div>
    @endif

</div>

@push('styles')
<style>
.fa-label { @apply block text-xs font-medium text-gray-600 mb-1; }
.fa-input { @apply w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs text-gray-800 focus:outline-none focus:ring-1 focus:ring-indigo-400 focus:border-indigo-400 transition; }
</style>
@endpush
