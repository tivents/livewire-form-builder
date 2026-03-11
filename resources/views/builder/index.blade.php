{{-- resources/views/builder/index.blade.php --}}
<div
    x-data="formArchitectBuilder()"
    x-init="init()"
    class="fa-builder flex h-screen bg-gray-50 font-sans text-sm"
>
    {{-- ═══════════════════ LEFT: Field Palette ═══════════════════ --}}
    <aside class="fa-palette w-64 flex-none bg-white border-r border-gray-200 flex flex-col overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200">
            <h2 class="font-semibold text-gray-700 text-xs uppercase tracking-wider">Field Types</h2>
        </div>
        <div class="flex-1 overflow-y-auto p-3 space-y-4">
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
        <header class="flex items-center justify-between px-5 py-3 bg-white border-b border-gray-200 gap-4">
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
                <div class="grid grid-cols-12 gap-4 max-w-3xl mx-auto" id="fa-canvas">
                    @foreach ($schema as $index => $field)
                        @php
                            $widthClass = \Tivents\LivewireFormBuilder\Support\AbstractFieldType::widthClass($field['width'] ?? 'full');
                            $isSelected = $selectedFieldIndex === $index;
                            $isLayout   = in_array($field['type'] ?? '', ['heading', 'hint', 'html']);
                        @endphp
                        <div
                            class="{{ $widthClass }} relative"
                            data-field-key="{{ $field['key'] }}"
                            data-index="{{ $index }}"
                            draggable="true"
                            @dragstart="onFieldDragStart($event, {{ $index }})"
                            @dragover.prevent="onFieldDragOver($event, {{ $index }})"
                            @dragleave="onFieldDragLeave($event)"
                            @drop.prevent="onFieldDrop($event, {{ $index }})"
                        >
                            {{-- Field card --}}
                            <div
                                wire:click="selectField({{ $index }})"
                                class="group relative border rounded-xl p-4 bg-white cursor-pointer transition-all select-none
                                    {{ $isSelected
                                        ? 'border-indigo-500 shadow-md shadow-indigo-100 ring-1 ring-indigo-500'
                                        : 'border-gray-200 hover:border-gray-300 hover:shadow-sm' }}"
                            >
                                {{-- Drag handle --}}
                                <div class="absolute left-2 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity cursor-grab text-gray-300">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 5a1 1 0 100-2 1 1 0 000 2zm0 7a1 1 0 100-2 1 1 0 000 2zm0 7a1 1 0 100-2 1 1 0 000 2zm6-14a1 1 0 100-2 1 1 0 000 2zm0 7a1 1 0 100-2 1 1 0 000 2zm0 7a1 1 0 100-2 1 1 0 000 2z"/>
                                    </svg>
                                </div>

                                <div class="pl-4">
                                    {{-- Field preview --}}
                                    @include('livewire-form-builder::partials.field-preview', ['field' => $field])
                                </div>

                                {{-- Action buttons (visible on hover/select) --}}
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

                                {{-- Type badge --}}
                                <span class="absolute bottom-2 right-2 text-[10px] font-mono text-gray-300">{{ $field['type'] }}</span>
                            </div>
                        </div>
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
    @if ($activeTab === 'builder' && $selectedField !== null)
    <aside class="w-72 flex-none bg-white border-l border-gray-200 flex flex-col overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
            <h2 class="font-semibold text-gray-700 text-xs uppercase tracking-wider">Field Settings</h2>
            <button wire:click="selectField({{ $selectedFieldIndex }})" type="button" class="text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto">
            @include('livewire-form-builder::settings.panel', [
                'field' => $selectedField,
                'index' => $selectedFieldIndex,
                'fieldKeys' => $fieldKeys,
            ])
        </div>
    </aside>
    @endif
</div>

@push('scripts')
<script>
function formArchitectBuilder() {
    return {
        dragSourceIndex: null,
        dragSourceType: null,   // 'palette' | 'field'

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
                const type = event.dataTransfer.getData('text/plain');
                @this.addField(type, targetIndex);
            } else if (this.dragSourceType === 'field' && this.dragSourceIndex !== null) {
                @this.moveField(this.dragSourceIndex, targetIndex);
            }
        },

        onCanvasDragOver(event) {
            event.preventDefault();
        },

        onCanvasDrop(event) {
            if (this.dragSourceType === 'palette') {
                const type = event.dataTransfer.getData('text/plain');
                @this.addField(type);
            }
        },
    };
}
</script>
@endpush
