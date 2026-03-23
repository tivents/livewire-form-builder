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
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Basic</h3>

        @if (!$isLayout)
        <div>
            <label class="fa-label">Label</label>
            <input type="text"
                wire:model.live.debounce.300ms="schema.{{ $sp }}.label"
                class="fa-input" placeholder="Field label" />
        </div>
        <div>
            <label class="fa-label">Field Key <span class="text-gray-400 font-normal">(unique)</span></label>
            <input type="text"
                wire:model.live.debounce.300ms="schema.{{ $sp }}.key"
                class="fa-input font-mono text-xs" placeholder="field_key" />
        </div>
        <div>
            <label class="fa-label">Placeholder</label>
            <input type="text"
                wire:model.live.debounce.300ms="schema.{{ $sp }}.placeholder"
                class="fa-input" />
        </div>
        <div>
            <label class="fa-label">Hint text</label>
            <input type="text"
                wire:model.live.debounce.300ms="schema.{{ $sp }}.hint"
                class="fa-input" />
        </div>
        <div class="flex items-center justify-between">
            <label class="fa-label mb-0">Required</label>
            <input type="checkbox"
                wire:model.live="schema.{{ $sp }}.required"
                class="rounded border-gray-300 text-indigo-600" />
        </div>
        @endif

        {{-- Layout-specific: heading text / level --}}
        @if ($type === 'heading')
        <div>
            <label class="fa-label">Text</label>
            <input type="text" wire:model.live.debounce.300ms="schema.{{ $sp }}.text" class="fa-input" />
        </div>
        <div>
            <label class="fa-label">Level</label>
            <select wire:model.live="schema.{{ $sp }}.level" class="fa-input">
                @foreach (['h1','h2','h3','h4'] as $h)
                    <option value="{{ $h }}">{{ strtoupper($h) }}</option>
                @endforeach
            </select>
        </div>
        @endif

        @if ($type === 'hint')
        <div>
            <label class="fa-label">Text</label>
            <textarea wire:model.live.debounce.300ms="schema.{{ $sp }}.text" rows="3" class="fa-input"></textarea>
        </div>
        <div>
            <label class="fa-label">Style</label>
            <select wire:model.live="schema.{{ $sp }}.style" class="fa-input">
                @foreach (['info','warning','success','error'] as $s)
                    <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        @endif

        @if ($type === 'html')
        <div>
            <label class="fa-label">HTML Content</label>
            <textarea wire:model.live.debounce.300ms="schema.{{ $sp }}.content" rows="5"
                class="fa-input font-mono text-xs"></textarea>
        </div>
        @endif
    </div>

    {{-- ── Layout / Width ── --}}
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Layout</h3>
        <div>
            <label class="fa-label">Column Width</label>
            <select wire:model.live="schema.{{ $sp }}.width" class="fa-input">
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
                <input type="number" wire:model.live="schema.{{ $sp }}.min" class="fa-input" />
            </div>
            <div>
                <label class="fa-label">Max</label>
                <input type="number" wire:model.live="schema.{{ $sp }}.max" class="fa-input" />
            </div>
            <div>
                <label class="fa-label">Step</label>
                <input type="number" wire:model.live="schema.{{ $sp }}.step" class="fa-input" />
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
                <input type="text" wire:model.live.debounce.300ms="schema.{{ $sp }}.on_label" class="fa-input" />
            </div>
            <div>
                <label class="fa-label">Off label</label>
                <input type="text" wire:model.live.debounce.300ms="schema.{{ $sp }}.off_label" class="fa-input" />
            </div>
        </div>
    </div>
    @endif

    @if ($type === 'hidden')
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Hidden Field</h3>
        <div>
            <label class="fa-label">Default value</label>
            <input type="text" wire:model.live.debounce.300ms="schema.{{ $sp }}.default" class="fa-input" />
        </div>
        <p class="text-xs text-gray-400">Hidden fields are not shown to users but their value is submitted.</p>
    </div>
    @endif

    @if (in_array($type, ['text', 'email']))
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Input Type</h3>
        <select wire:model.live="schema.{{ $sp }}.input_type" class="fa-input">
            @foreach (['text','email','tel','url','number','password'] as $it)
                <option value="{{ $it }}">{{ ucfirst($it) }}</option>
            @endforeach
        </select>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="fa-label">Min length</label>
                <input type="number" wire:model.live="schema.{{ $sp }}.min_length" class="fa-input" />
            </div>
            <div>
                <label class="fa-label">Max length</label>
                <input type="number" wire:model.live="schema.{{ $sp }}.max_length" class="fa-input" />
            </div>
        </div>
    </div>
    @endif

    @if ($type === 'textarea')
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Textarea</h3>
        <div>
            <label class="fa-label">Rows</label>
            <input type="number" wire:model.live="schema.{{ $sp }}.rows" class="fa-input" min="1" max="20" />
        </div>
    </div>
    @endif

    @if ($type === 'datetime')
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Date / Time</h3>
        <select wire:model.live="schema.{{ $sp }}.mode" class="fa-input">
            <option value="date">Date only</option>
            <option value="time">Time only</option>
            <option value="datetime">Date & Time</option>
        </select>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="fa-label">Min date</label>
                <input type="date" wire:model.live="schema.{{ $sp }}.min_date" class="fa-input" />
            </div>
            <div>
                <label class="fa-label">Max date</label>
                <input type="date" wire:model.live="schema.{{ $sp }}.max_date" class="fa-input" />
            </div>
        </div>
    </div>
    @endif

    @if ($type === 'file')
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">File Upload</h3>
        <div class="flex items-center justify-between">
            <label class="fa-label mb-0">Allow multiple</label>
            <input type="checkbox" wire:model.live="schema.{{ $sp }}.multiple" class="rounded border-gray-300 text-indigo-600" />
        </div>
        <div>
            <label class="fa-label">Max size (KB)</label>
            <input type="number" wire:model.live="schema.{{ $sp }}.max_size_kb" class="fa-input" />
        </div>
        <div>
            <label class="fa-label">Max files</label>
            <input type="number" wire:model.live="schema.{{ $sp }}.max_files" class="fa-input" min="1" />
        </div>
    </div>
    @endif

    {{-- ── Options (select / checkbox / radio) ── --}}
    @if ($hasOptions)
    <div class="p-4 space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Options</h3>
            <button wire:click="addFieldOption({{ $index }}, {{ $ci !== null ? $ci : 'null' }})" type="button"
                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">+ Add</button>
        </div>
        @foreach (($field['options'] ?? []) as $oi => $option)
        <div class="flex items-center gap-2">
            <input type="text"
                wire:model.live.debounce.300ms="schema.{{ $sp }}.options.{{ $oi }}.label"
                class="fa-input flex-1" placeholder="Label" />
            <input type="text"
                wire:model.live.debounce.300ms="schema.{{ $sp }}.options.{{ $oi }}.value"
                class="fa-input w-24 font-mono text-xs" placeholder="value" />
            <button wire:click="removeFieldOption({{ $index }}, {{ $oi }}, {{ $ci !== null ? $ci : 'null' }})" type="button"
                class="text-gray-300 hover:text-red-500 transition-colors flex-none">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @endforeach

        @if ($type === 'select')
        <div class="flex items-center justify-between pt-1">
            <label class="fa-label mb-0">Multi-select</label>
            <input type="checkbox" wire:model.live="schema.{{ $sp }}.multiple" class="rounded border-gray-300 text-indigo-600" />
        </div>
        <div class="flex items-center justify-between">
            <label class="fa-label mb-0">Searchable</label>
            <input type="checkbox" wire:model.live="schema.{{ $sp }}.searchable" class="rounded border-gray-300 text-indigo-600" />
        </div>
        @endif

        @if (in_array($type, ['checkbox', 'radio']))
        <div class="flex items-center justify-between pt-1">
            <label class="fa-label mb-0">Inline layout</label>
            <input type="checkbox" wire:model.live="schema.{{ $sp }}.inline" class="rounded border-gray-300 text-indigo-600" />
        </div>
        @endif
    </div>
    @endif

    {{-- ── Row children ── --}}
    @if ($type === 'row')
    <div class="p-4 space-y-3">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Fields in Row</h3>
        <div class="space-y-1.5">
            @forelse (($field['children'] ?? []) as $ci2 => $child)
                <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
                    <span class="flex-1 text-xs text-gray-700 font-medium truncate">{{ $child['label'] ?? $child['key'] }}</span>
                    <span class="text-[10px] text-gray-400 font-mono flex-none">{{ $child['type'] }}</span>
                    <button wire:click="selectField({{ $index }}, {{ $ci2 }})" type="button" title="Edit"
                        class="p-0.5 text-gray-400 hover:text-indigo-500 transition-colors flex-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button wire:click="removeFieldFromRow({{ $index }}, {{ $ci2 }})" type="button" title="Remove"
                        class="p-0.5 text-gray-300 hover:text-red-500 transition-colors flex-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @empty
                <p class="text-xs text-gray-400 italic">No fields yet — drag from the palette or use buttons below.</p>
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
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Repeater</h3>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="fa-label">Min rows</label>
                <input type="number" wire:model.live="schema.{{ $sp }}.min_rows" class="fa-input" min="0" />
            </div>
            <div>
                <label class="fa-label">Max rows</label>
                <input type="number" wire:model.live="schema.{{ $sp }}.max_rows" class="fa-input" min="1" />
            </div>
        </div>
        <div>
            <label class="fa-label">Add button label</label>
            <input type="text" wire:model.live.debounce.300ms="schema.{{ $sp }}.add_label" class="fa-input" />
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
            <select wire:model.live="schema.{{ $sp }}.conditions.action" class="fa-input">
                <option value="show">Show this field</option>
                <option value="hide">Hide this field</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <label class="fa-label mb-0 flex-none">Logic</label>
            <select wire:model.live="schema.{{ $sp }}.conditions.logic" class="fa-input">
                <option value="and">All rules match (AND)</option>
                <option value="or">Any rule matches (OR)</option>
            </select>
        </div>

        @foreach (($field['conditions']['rules'] ?? []) as $ri => $rule)
        <div class="space-y-1.5 bg-gray-50 rounded-lg p-2.5">
            <select wire:model.live="schema.{{ $sp }}.conditions.rules.{{ $ri }}.field" class="fa-input text-xs">
                <option value="">— pick field —</option>
                @foreach ($fieldKeys as $fk => $fl)
                    <option value="{{ $fk }}">{{ $fl }}</option>
                @endforeach
            </select>
            <select wire:model.live="schema.{{ $sp }}.conditions.rules.{{ $ri }}.operator" class="fa-input text-xs">
                @foreach (['==' => 'equals', '!=' => 'not equals', 'contains' => 'contains', 'empty' => 'is empty', 'not_empty' => 'is not empty', '>' => '>', '<' => '<'] as $op => $ol)
                    <option value="{{ $op }}">{{ $ol }}</option>
                @endforeach
            </select>
            @if (!in_array($rule['operator'] ?? '', ['empty', 'not_empty']))
            <input type="text"
                wire:model.live.debounce.300ms="schema.{{ $sp }}.conditions.rules.{{ $ri }}.value"
                class="fa-input text-xs" placeholder="Value…" />
            @endif
            <button wire:click="removeCondition({{ $index }}, {{ $ri }}, {{ $ci !== null ? $ci : 'null' }})" type="button"
                class="text-xs text-red-400 hover:text-red-600">Remove rule</button>
        </div>
        @endforeach

        <button wire:click="addCondition({{ $index }}, {{ $ci !== null ? $ci : 'null' }})" type="button"
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
