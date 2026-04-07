{{-- resources/views/builder/index.blade.php --}}
@push('styles')
<style>
    /* ── Scoped reset: prevent host-app CSS bleeding into the builder ── */
    .fa-builder, .fa-builder * {
        box-sizing: border-box;
    }
    .fa-builder {
        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 0.875rem;
        line-height: 1.25rem;
        color: #111827;
    }
    /* Force flex behaviour regardless of Bootstrap/Flowbite resets */
    .fa-builder header,
    .fa-builder .fa-builder-header {
        display: flex !important;
        align-items: center !important;
        flex-wrap: nowrap !important;
    }
    /* Neutralise Bootstrap heading styles inside builder */
    .fa-builder h1, .fa-builder h2, .fa-builder h3,
    .fa-builder h4, .fa-builder h5, .fa-builder h6 {
        font-size: inherit;
        font-weight: inherit;
        line-height: inherit;
        margin: 0;
        padding: 0;
        border: none;
    }
    /* Neutralise Bootstrap form-control / input-group overrides */
    .fa-builder input:not([type=checkbox]):not([type=radio]),
    .fa-builder select,
    .fa-builder textarea {
        display: block;
        width: 100%;
        padding: inherit;
        font-size: inherit;
        line-height: inherit;
        background-image: none;
        border-radius: inherit;
    }
    .fa-builder button {
        display: inline-flex;
        align-items: center;
    }
    .fa-builder p { margin: 0; }
    .fa-builder ul, .fa-builder ol { margin: 0; padding: 0; list-style: none; }
    /* Force CSS grid so col-span-* classes work regardless of Bootstrap */
    .fa-builder .grid { display: grid !important; }
    .fa-builder .col-span-3  { grid-column: span 3  / span 3  !important; }
    .fa-builder .col-span-4  { grid-column: span 4  / span 4  !important; }
    .fa-builder .col-span-6  { grid-column: span 6  / span 6  !important; }
    .fa-builder .col-span-8  { grid-column: span 8  / span 8  !important; }
    .fa-builder .col-span-9  { grid-column: span 9  / span 9  !important; }
    .fa-builder .col-span-12 { grid-column: span 12 / span 12 !important; }
    .fa-builder .grid-cols-12 { grid-template-columns: repeat(12, minmax(0, 1fr)) !important; }
</style>
@endpush

<div
    x-data="formArchitectBuilder()"
    x-init="init()"
    class="fa-builder flex bg-gray-50 font-sans text-sm"
    style="height: clamp(600px, calc(100vh - 8rem), 100vh);"
>
    {{-- ═══════════════════ LEFT: Field Palette ═══════════════════ --}}
    <aside class="fa-palette w-64 flex-none bg-white border-r border-gray-200 flex flex-col overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200">
            <h2 class="font-semibold text-gray-700 text-xs uppercase tracking-wider">Field Types</h2>
        </div>
        <div class="flex-1 overflow-y-auto p-3 space-y-4">
            {{-- Standard presets --}}
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-1">Standard</p>
                <div class="space-y-1">
                    @foreach ($presets as $presetKey => $preset)
                        <button
                            type="button"
                            wire:click="addPreset('{{ $presetKey }}')"
                            class="group w-full flex items-center gap-2 px-3 py-2 rounded-md text-gray-600 hover:bg-indigo-50 hover:text-indigo-700 transition-colors cursor-grab active:cursor-grabbing"
                            draggable="true"
                            @dragstart="onPaletteDragStart($event, 'preset:{{ $presetKey }}')"
                        >
                            <span class="flex-none w-4 h-4 text-gray-400 group-hover:text-indigo-500">
                                @include('livewire-form-builder::partials.icon', ['name' => 'heroicon-o-pencil'])
                            </span>
                            <span class="text-xs font-medium">{{ $preset['label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
            @php
                $groupLabels = ['inputs' => 'Input Fields', 'layout' => 'Layout', 'advanced' => 'Advanced'];
            @endphp
            @foreach ($palette as $group => $fields)
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-1">
                        {{ $groupLabels[$group] ?? $group }}
                    </p>
                    <div class="space-y-1">
                        @foreach ($fields as $field)
                            <button
                                type="button"
                                wire:click="addField('{{ $field['type'] }}')"
                                class="group w-full flex items-center gap-2 px-3 py-2 rounded-md text-gray-600 hover:bg-indigo-50 hover:text-indigo-700 transition-colors cursor-grab active:cursor-grabbing"
                                draggable="true"
                                @dragstart="onPaletteDragStart($event, '{{ $field['type'] }}')"
                            >
                                <span class="flex-none w-4 h-4 text-gray-400 group-hover:text-indigo-500">
                                    @include('livewire-form-builder::partials.icon', ['name' => $field['icon']])
                                </span>
                                <span class="text-xs font-medium">{{ $field['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </aside>

    {{-- ═══════════════════ CENTRE: Canvas + Tabs ═══════════════════ --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Top bar --}}
        <header class="fa-builder-header flex items-center justify-between px-5 py-3 bg-white border-b border-gray-200 gap-4" style="display:flex!important;flex-wrap:nowrap!important;">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <input
                    wire:model.live.debounce.300ms="name"
                    type="text"
                    placeholder="Form name…"
                    class="text-base font-semibold text-gray-800 bg-transparent border-b border-transparent hover:border-gray-300 focus:border-indigo-500 focus:outline-none px-0 py-0.5 w-full max-w-sm"
                />
            </div>

            {{-- Tab switcher --}}
            <nav class="flex gap-1 bg-gray-100 rounded-lg p-1">
                @foreach (['builder' => 'Builder', 'preview' => 'Preview', 'json' => 'JSON'] as $tab => $label)
                    <button
                        wire:click="$set('activeTab', '{{ $tab }}')"
                        type="button"
                        class="px-3 py-1.5 text-xs font-medium rounded-md transition-colors {{ $activeTab === $tab ? 'bg-white shadow text-gray-800' : 'text-gray-500 hover:text-gray-700' }}"
                    >{{ $label }}</button>
                @endforeach
            </nav>

            <button
                wire:click="save"
                type="button"
                class="flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Save
            </button>
        </header>

        {{-- Flash message --}}
        @if ($flashMessage)
            <div
                wire:click="dismissFlash"
                class="mx-5 mt-3 flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm cursor-pointer {{ $flashType === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200' }}"
            >
                <span>{{ $flashMessage }}</span>
                <svg class="w-3.5 h-3.5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
        @endif

        {{-- ─── BUILDER TAB ─── --}}
        @if ($activeTab === 'builder')
        <div
            class="flex-1 overflow-y-auto p-6"
            @dragover.prevent="onCanvasDragOver($event)"
            @drop.prevent="onCanvasDrop($event)"
        >
            @if (count($schema) === 0)
                <div class="flex flex-col items-center justify-center h-full text-gray-400 select-none">
                    <svg class="w-12 h-12 mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <p class="text-sm font-medium">Drag fields here or click a type in the palette</p>
                </div>
            @else
                {{-- 12-column grid canvas --}}
                <div id="fa-canvas" class="max-w-3xl mx-auto" style="display:grid;grid-template-columns:repeat(12,minmax(0,1fr));gap:1rem;">
                    @foreach ($schema as $index => $field)
                        @php
                            $widthStyle = \Tivents\LivewireFormBuilder\Support\AbstractFieldType::widthStyle($field['width'] ?? 'full');
                            $isSelected = $selectedFieldIndex === $index && $selectedChildIndex === null;
                        @endphp

                        @if (($field['type'] ?? '') === 'row')
                        {{-- ── Row container ── --}}
                        <div
                            wire:key="field-{{ $field['key'] }}"
                            style="{{ $widthStyle }}"
                            class="relative"
                            data-field-key="{{ $field['key'] }}"
                            data-index="{{ $index }}"
                            draggable="true"
                            @dragstart="onFieldDragStart($event, {{ $index }})"
                            @dragover.prevent="onFieldDragOver($event, {{ $index }})"
                            @dragleave="onFieldDragLeave($event)"
                            @drop.prevent="onFieldDrop($event, {{ $index }})"
                        >
                            <div class="rounded-xl border-2 border-dashed {{ $isSelected ? 'border-indigo-400 bg-indigo-50/30' : 'border-gray-300 bg-gray-50/40' }} transition-all">
                                {{-- Row header --}}
                                <div class="flex items-center justify-between px-3 py-2 border-b border-dashed {{ $isSelected ? 'border-indigo-300' : 'border-gray-200' }}">
                                    <button wire:click.stop="selectField({{ $index }})" type="button"
                                        class="flex items-center gap-1.5 text-xs font-semibold {{ $isSelected ? 'text-indigo-500' : 'text-gray-400 hover:text-indigo-500' }} transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5h7v14H4V5zm9 0h7v14h-7V5z"/></svg>
                                        Row
                                    </button>
                                    <div class="flex items-center gap-1">
                                        <button wire:click.stop="moveUp({{ $index }})" type="button" title="Move up"
                                            class="p-1 rounded text-gray-400 hover:text-gray-700 hover:bg-white transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                        </button>
                                        <button wire:click.stop="moveDown({{ $index }})" type="button" title="Move down"
                                            class="p-1 rounded text-gray-400 hover:text-gray-700 hover:bg-white transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                        <button wire:click.stop="deleteField({{ $index }})" type="button" title="Delete row"
                                            class="p-1 rounded text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Row children grid --}}
                                <div class="p-3">
                                    <div style="display:grid;grid-template-columns:repeat(12,minmax(0,1fr));gap:0.75rem;"
                                         @dragover.prevent.stop="onRowDragOver($event)"
                                         @dragleave="onRowDragLeave($event)"
                                         @drop.prevent.stop="onRowDrop($event, {{ $index }})">

                                        @forelse (($field['children'] ?? []) as $ci => $child)
                                            @php
                                                $childWidthStyle = \Tivents\LivewireFormBuilder\Support\AbstractFieldType::widthStyle($child['width'] ?? 'full');
                                                $isChildSelected = $selectedFieldIndex === $index && $selectedChildIndex === $ci;
                                            @endphp
                                            <div style="{{ $childWidthStyle }}" class="relative"
                                                 wire:key="row-{{ $field['key'] }}-child-{{ $child['key'] }}"
                                                 data-row="{{ $index }}" data-child="{{ $ci }}"
                                                 draggable="true"
                                                 @dragstart.stop="onChildDragStart($event, {{ $index }}, {{ $ci }})">
                                                <div
                                                    wire:click.stop="selectField({{ $index }}, {{ $ci }})"
                                                    class="group relative border rounded-lg p-3 bg-white cursor-pointer transition-all select-none
                                                        {{ $isChildSelected
                                                            ? 'border-indigo-500 shadow-md shadow-indigo-100 ring-1 ring-indigo-500'
                                                            : 'border-gray-200 hover:border-gray-300 hover:shadow-sm' }}"
                                                >
                                                    {{-- Drag handle --}}
                                                    <div class="absolute left-1.5 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 cursor-grab text-gray-300">
                                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 5a1 1 0 100-2 1 1 0 000 2zm0 7a1 1 0 100-2 1 1 0 000 2zm0 7a1 1 0 100-2 1 1 0 000 2zm6-14a1 1 0 100-2 1 1 0 000 2zm0 7a1 1 0 100-2 1 1 0 000 2zm0 7a1 1 0 100-2 1 1 0 000 2z"/></svg>
                                                    </div>
                                                    <div class="pl-3 pr-6">
                                                        @include('livewire-form-builder::partials.field-preview', ['field' => $child])
                                                    </div>
                                                    {{-- Move left / right + Remove --}}
                                                    @php $rowChildCount = count($field['children'] ?? []); @endphp
                                                    <div class="absolute top-1.5 right-1.5 opacity-0 group-hover:opacity-100 {{ $isChildSelected ? 'opacity-100' : '' }} transition-opacity flex items-center gap-0.5">
                                                        @if ($ci > 0)
                                                        <button wire:click.stop="moveChildInRow({{ $index }}, {{ $ci }}, {{ $ci - 1 }})" type="button" title="Move left"
                                                            class="p-0.5 rounded text-gray-300 hover:text-gray-600 hover:bg-gray-100">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                                        </button>
                                                        @endif
                                                        @if ($ci < $rowChildCount - 1)
                                                        <button wire:click.stop="moveChildInRow({{ $index }}, {{ $ci }}, {{ $ci + 1 }})" type="button" title="Move right"
                                                            class="p-0.5 rounded text-gray-300 hover:text-gray-600 hover:bg-gray-100">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                        </button>
                                                        @endif
                                                        <button wire:click.stop="removeFieldFromRow({{ $index }}, {{ $ci }})" type="button" title="Remove from row"
                                                            class="p-0.5 rounded text-gray-300 hover:text-red-500 hover:bg-red-50">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </div>
                                                    <span class="absolute bottom-1 right-2 text-[9px] font-mono text-gray-300">{{ $child['type'] }}</span>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-span-12 py-5 text-center text-xs text-gray-400 border-2 border-dashed border-gray-200 rounded-lg">
                                                Drop fields here or click the Row to add fields via settings
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        @else
                        {{-- ── Regular field card ── --}}
                        <div
                            wire:key="field-{{ $field['key'] }}"
                            style="{{ $widthStyle }}"
                            class="relative"
                            data-field-key="{{ $field['key'] }}"
                            data-index="{{ $index }}"
                            draggable="true"
                            @dragstart="onFieldDragStart($event, {{ $index }})"
                            @dragover.prevent="onFieldDragOver($event, {{ $index }})"
                            @dragleave="onFieldDragLeave($event)"
                            @drop.prevent="onFieldDrop($event, {{ $index }})"
                        >
                            <div
                                wire:click="selectField({{ $index }})"
                                class="group relative border rounded-xl p-4 bg-white cursor-pointer transition-all select-none
                                    {{ $isSelected
                                        ? 'border-indigo-500 shadow-md shadow-indigo-100 ring-1 ring-indigo-500'
                                        : 'border-gray-200 hover:border-gray-300 hover:shadow-sm' }}"
                            >
                                <div class="absolute left-2 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity cursor-grab text-gray-300">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 5a1 1 0 100-2 1 1 0 000 2zm0 7a1 1 0 100-2 1 1 0 000 2zm0 7a1 1 0 100-2 1 1 0 000 2zm6-14a1 1 0 100-2 1 1 0 000 2zm0 7a1 1 0 100-2 1 1 0 000 2zm0 7a1 1 0 100-2 1 1 0 000 2z"/></svg>
                                </div>
                                <div class="pl-4">
                                    @include('livewire-form-builder::partials.field-preview', ['field' => $field])
                                </div>
                                <div class="absolute top-2 right-2 flex items-center gap-1 opacity-0 group-hover:opacity-100 {{ $isSelected ? 'opacity-100' : '' }} transition-opacity">
                                    <button wire:click.stop="moveUp({{ $index }})" type="button" title="Move up"
                                        class="p-1 rounded text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    </button>
                                    <button wire:click.stop="moveDown({{ $index }})" type="button" title="Move down"
                                        class="p-1 rounded text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <button wire:click.stop="duplicateField({{ $index }})" type="button" title="Duplicate"
                                        class="p-1 rounded text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    </button>
                                    <button wire:click.stop="deleteField({{ $index }})" type="button" title="Delete"
                                        class="p-1 rounded text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                                <span class="absolute bottom-2 right-2 text-[10px] font-mono text-gray-300">{{ $field['type'] }}</span>
                            </div>
                        </div>
                        @endif

                    @endforeach
                </div>
            @endif
        </div>

        {{-- ─── PREVIEW TAB ─── --}}
        @elseif ($activeTab === 'preview')
        <div class="flex-1 overflow-y-auto p-6">
            <div class="max-w-2xl mx-auto bg-white rounded-xl border border-gray-200 shadow-sm p-8">
                <h1 class="text-xl font-bold text-gray-800 mb-6">{{ $name ?: 'Untitled Form' }}</h1>
                <livewire:livewire-form-builder::renderer :schema="$schema" key="preview-{{ now()->timestamp }}" />
            </div>
        </div>

        {{-- ─── JSON TAB ─── --}}
        @elseif ($activeTab === 'json')
        <div class="flex-1 overflow-hidden flex flex-col p-5 gap-4">
            <div class="flex gap-3">
                <button
                    x-data
                    @click="
                        const json = @js($this->exportJson());
                        navigator.clipboard.writeText(json);
                    "
                    type="button"
                    class="text-xs bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg"
                >Copy JSON</button>
                <label class="text-xs bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 px-3 py-1.5 rounded-lg cursor-pointer">
                    Import JSON
                    <input type="file" accept=".json" class="hidden" x-data @change="
                        const file = $event.target.files[0];
                        if (!file) return;
                        const reader = new FileReader();
                        reader.onload = e => $wire.importJson(e.target.result);
                        reader.readAsText(file);
                    " />
                </label>
            </div>
            <pre class="flex-1 overflow-auto bg-gray-900 text-green-400 text-xs rounded-xl p-5 font-mono leading-relaxed">{{ $this->exportJson() }}</pre>
        </div>
        @endif
    </div>

    {{-- ═══════════════════ RIGHT: Settings Panel ═══════════════════ --}}
    @if ($activeTab === 'builder')
    <aside class="w-72 flex-none bg-white border-l border-gray-200 flex flex-col overflow-hidden">
        @if ($selectedField !== null)
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
                <h2 class="font-semibold text-gray-700 text-xs uppercase tracking-wider">Field Settings</h2>
                <button wire:click="selectField({{ $selectedFieldIndex }})" type="button" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto"
                 wire:key="settings-panel-{{ $selectedFieldIndex }}-{{ $selectedChildIndex ?? 'x' }}">
                @include('livewire-form-builder::settings.panel', [
                    'field'      => $selectedField,
                    'index'      => $selectedFieldIndex,
                    'childIndex' => $selectedChildIndex,
                    'fieldKeys'  => $fieldKeys,
                ])
            </div>
        @else
            <div class="px-4 py-3 border-b border-gray-200">
                <h2 class="font-semibold text-gray-700 text-xs uppercase tracking-wider">Form Settings</h2>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                <div>
                    <label class="fa-label">Description</label>
                    <textarea wire:model.live.debounce.300ms="description" rows="2" class="fa-input" placeholder="Optional description…"></textarea>
                </div>
                <div class="flex items-center justify-between">
                    <label class="fa-label mb-0">Active</label>
                    <input type="checkbox" wire:model.live="isActive" class="rounded border-gray-300 text-indigo-600" />
                </div>
                <div>
                    <label class="fa-label">Submit button label</label>
                    <input type="text"
                        wire:model.live.debounce.300ms="settings.button_label"
                        class="fa-input"
                        placeholder="Submit" />
                </div>
                <div>
                    <label class="fa-label">Submit button position</label>
                    <div class="flex gap-2 mt-1">
                        @foreach (['left' => 'Left', 'center' => 'Center', 'right' => 'Right'] as $align => $label)
                            <button
                                type="button"
                                wire:click="$set('settings.button_align', '{{ $align }}')"
                                class="flex-1 py-1.5 text-xs rounded-lg border transition {{ ($settings['button_align'] ?? 'left') === $align ? 'bg-indigo-50 border-indigo-400 text-indigo-700 font-semibold' : 'border-gray-200 text-gray-500 hover:border-gray-300' }}"
                            >{{ $label }}</button>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="fa-label">Submit button color</label>
                    <div class="grid grid-cols-4 gap-2 mt-1">
                        @foreach (['green' => 'bg-green-500', 'blue' => 'bg-blue-500', 'indigo' => 'bg-indigo-500', 'red' => 'bg-red-500', 'orange' => 'bg-orange-500', 'purple' => 'bg-purple-500', 'gray' => 'bg-gray-500', 'black' => 'bg-gray-900'] as $color => $bg)
                            <button
                                type="button"
                                wire:click="$set('settings.button_color', '{{ $color }}')"
                                class="h-7 rounded-lg {{ $bg }} ring-offset-1 transition {{ ($settings['button_color'] ?? 'green') === $color ? 'ring-2 ring-gray-400' : '' }}"
                                title="{{ ucfirst($color) }}"
                            ></button>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </aside>
    @endif
</div>

@push('scripts')
<script>
function formArchitectBuilder() {
    return {
        dragSourceIndex: null,
        dragSourceType: null,   // 'palette' | 'field' | 'child'
        dragSourceRowIndex: null,
        dragSourceChildIndex: null,

        init() {
            // SortableJS or native DnD – using native here for zero dependencies
        },

        onPaletteDragStart(event, type) {
            this.dragSourceType  = 'palette';
            this.dragSourceIndex = null;
            event.dataTransfer.effectAllowed = 'copy';
            event.dataTransfer.setData('text/plain', type);
        },

        onFieldDragStart(event, index) {
            this.dragSourceType  = 'field';
            this.dragSourceIndex = index;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', String(index));
            event.currentTarget.classList.add('opacity-50');
        },

        onFieldDragOver(event, index) {
            event.currentTarget.classList.add('ring-2', 'ring-indigo-400', 'ring-offset-1');
        },

        onFieldDragLeave(event) {
            event.currentTarget.classList.remove('ring-2', 'ring-indigo-400', 'ring-offset-1');
        },

        onFieldDrop(event, targetIndex) {
            event.currentTarget.classList.remove('ring-2', 'ring-indigo-400', 'ring-offset-1');

            if (this.dragSourceType === 'palette') {
                const data = event.dataTransfer.getData('text/plain');
                if (data.startsWith('preset:')) {
                    @this.addPreset(data.slice(7), targetIndex);
                } else {
                    @this.addField(data, targetIndex);
                }
            } else if (this.dragSourceType === 'field' && this.dragSourceIndex !== null) {
                @this.moveField(this.dragSourceIndex, targetIndex);
            }
        },

        onCanvasDragOver(event) {
            event.preventDefault();
        },

        onCanvasDrop(event) {
            if (this.dragSourceType === 'palette') {
                const data = event.dataTransfer.getData('text/plain');
                if (data.startsWith('preset:')) {
                    @this.addPreset(data.slice(7));
                } else {
                    @this.addField(data);
                }
            }
        },

        onChildDragStart(event, rowIndex, childIndex) {
            this.dragSourceType       = 'child';
            this.dragSourceRowIndex   = rowIndex;
            this.dragSourceChildIndex = childIndex;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', `${rowIndex},${childIndex}`);
            event.currentTarget.classList.add('opacity-50');
        },

        onRowDragOver(event) {
            event.currentTarget.classList.add('ring-2', 'ring-indigo-300', 'ring-inset');
        },

        onRowDragLeave(event) {
            event.currentTarget.classList.remove('ring-2', 'ring-indigo-300', 'ring-inset');
        },

        onRowDrop(event, rowIndex) {
            event.currentTarget.classList.remove('ring-2', 'ring-indigo-300', 'ring-inset');
            if (this.dragSourceType === 'palette') {
                const data = event.dataTransfer.getData('text/plain');
                if (data.startsWith('preset:')) {
                    @this.addPresetToRow(rowIndex, data.slice(7));
                } else {
                    @this.addFieldToRow(rowIndex, data);
                }
            }
        },
    };
}
</script>
@endpush
