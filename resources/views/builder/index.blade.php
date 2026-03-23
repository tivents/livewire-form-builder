{{-- resources/views/builder/index.blade.php --}}
<div
    x-data="formArchitectBuilder()"
    x-init="init()"
    class="fa-builder flex h-screen bg-zinc-50 font-sans text-sm"
>
    {{-- ═══════════════════ LEFT: Field Palette ═══════════════════ --}}
    <aside class="fa-palette w-64 shrink-0 bg-white border-r border-zinc-200 flex flex-col overflow-hidden">
        <div class="px-4 py-3 border-b border-zinc-200">
            <p class="font-semibold text-zinc-700 text-xs uppercase tracking-wider">Field Types</p>
        </div>
        <div class="flex-1 overflow-y-auto p-3 space-y-4">
            @php
                $groupLabels = ['inputs' => 'Input Fields', 'layout' => 'Layout', 'advanced' => 'Advanced'];
            @endphp
            @foreach ($palette as $group => $fields)
                <div>
                    <p class="text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-2 px-1">
                        {{ $groupLabels[$group] ?? $group }}
                    </p>
                    <div class="space-y-0.5">
                        @foreach ($fields as $field)
                            <button
                                type="button"
                                wire:click="addField('{{ $field['type'] }}')"
                                class="group w-full flex items-center gap-2 px-3 py-2 rounded-lg text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 transition-colors cursor-grab active:cursor-grabbing"
                                draggable="true"
                                @dragstart="onPaletteDragStart($event, '{{ $field['type'] }}')"
                            >
                                <span class="shrink-0 w-4 h-4 text-zinc-400 group-hover:text-zinc-600">
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
        <header class="flex items-center justify-between px-5 py-3 bg-white border-b border-zinc-200 gap-4">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <input
                    wire:model.live.debounce.300ms="name"
                    type="text"
                    placeholder="Form name…"
                    class="text-base font-semibold text-zinc-800 bg-transparent border-b border-transparent hover:border-zinc-300 focus:border-zinc-700 focus:outline-none px-0 py-0.5 w-full max-w-sm transition-colors"
                />
            </div>

            {{-- Tab switcher --}}
            <nav class="flex gap-1 bg-zinc-100 rounded-lg p-1">
                @foreach (['builder' => 'Builder', 'preview' => 'Preview', 'json' => 'JSON'] as $tab => $label)
                    <button
                        wire:click="$set('activeTab', '{{ $tab }}')"
                        type="button"
                        class="px-3 py-1.5 text-xs font-medium rounded-md transition-colors {{ $activeTab === $tab ? 'bg-white shadow text-zinc-800' : 'text-zinc-500 hover:text-zinc-700' }}"
                    >{{ $label }}</button>
                @endforeach
            </nav>

            <flux:button wire:click="save" variant="primary" size="sm" icon="check">
                Save
            </flux:button>
        </header>

        {{-- Flash message --}}
        @if ($flashMessage)
            <div
                wire:click="dismissFlash"
                class="mx-5 mt-3 flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm cursor-pointer {{ $flashType === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200' }}"
            >
                @if ($flashType === 'error')
                    <flux:icon.exclamation-circle class="size-4 shrink-0" />
                @else
                    <flux:icon.check-circle class="size-4 shrink-0" />
                @endif
                <span class="flex-1">{{ $flashMessage }}</span>
                <flux:icon.x-mark class="size-3.5 shrink-0" />
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
                <div class="flex flex-col items-center justify-center h-full text-zinc-400 select-none">
                    <flux:icon.plus class="size-12 mb-3 opacity-30" />
                    <p class="text-sm font-medium">Drag fields here or click a type in the palette</p>
                </div>
            @else
                {{-- 12-column grid canvas --}}
                <div class="grid grid-cols-12 gap-4 max-w-3xl mx-auto" id="fa-canvas">
                    @foreach ($schema as $index => $field)
                        @php
                            $widthClass = \Tivents\LivewireFormBuilder\Support\AbstractFieldType::widthClass($field['width'] ?? 'full');
                            $isSelected = $selectedFieldIndex === $index;
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
                                        ? 'border-zinc-800 shadow-md ring-1 ring-zinc-800'
                                        : 'border-zinc-200 hover:border-zinc-300 hover:shadow-sm' }}"
                            >
                                {{-- Drag handle --}}
                                <div class="absolute left-2 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity cursor-grab text-zinc-300">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 5a1 1 0 100-2 1 1 0 000 2zm0 7a1 1 0 100-2 1 1 0 000 2zm0 7a1 1 0 100-2 1 1 0 000 2zm6-14a1 1 0 100-2 1 1 0 000 2zm0 7a1 1 0 100-2 1 1 0 000 2zm0 7a1 1 0 100-2 1 1 0 000 2z"/>
                                    </svg>
                                </div>

                                <div class="pl-4">
                                    @include('livewire-form-builder::partials.field-preview', ['field' => $field])
                                </div>

                                {{-- Action buttons (visible on hover/select) --}}
                                <div class="absolute top-2 right-2 flex items-center gap-0.5 opacity-0 group-hover:opacity-100 {{ $isSelected ? 'opacity-100' : '' }} transition-opacity">
                                    <flux:button wire:click.stop="moveUp({{ $index }})" variant="ghost" size="xs" icon="chevron-up" title="Move up" />
                                    <flux:button wire:click.stop="moveDown({{ $index }})" variant="ghost" size="xs" icon="chevron-down" title="Move down" />
                                    <flux:button wire:click.stop="duplicateField({{ $index }})" variant="ghost" size="xs" icon="document-duplicate" title="Duplicate" />
                                    <flux:button wire:click.stop="deleteField({{ $index }})" variant="danger" size="xs" icon="trash" title="Delete" />
                                </div>

                                {{-- Type badge --}}
                                <span class="absolute bottom-2 right-2 text-[10px] font-mono text-zinc-300">{{ $field['type'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ─── PREVIEW TAB ─── --}}
        @elseif ($activeTab === 'preview')
        <div class="flex-1 overflow-y-auto p-6">
            <div class="max-w-2xl mx-auto bg-white rounded-xl border border-zinc-200 shadow-sm p-8">
                <flux:heading size="lg" class="mb-6">{{ $name ?: 'Untitled Form' }}</flux:heading>
                <livewire:livewire-form-builder::renderer :schema="$schema" key="preview-{{ now()->timestamp }}" />
            </div>
        </div>

        {{-- ─── JSON TAB ─── --}}
        @elseif ($activeTab === 'json')
        <div class="flex-1 overflow-hidden flex flex-col p-5 gap-4">
            <div class="flex gap-3">
                <flux:button
                    x-data
                    @click="navigator.clipboard.writeText(@js($this->exportJson()))"
                    variant="ghost"
                    size="sm"
                    icon="clipboard-document"
                >
                    Copy JSON
                </flux:button>
                <label>
                    <flux:button as="span" variant="ghost" size="sm" icon="arrow-up-tray">
                        Import JSON
                    </flux:button>
                    <input type="file" accept=".json" class="hidden" x-data @change="
                        const file = $event.target.files[0];
                        if (!file) return;
                        const reader = new FileReader();
                        reader.onload = e => $wire.importJson(e.target.result);
                        reader.readAsText(file);
                    " />
                </label>
            </div>
            <pre class="flex-1 overflow-auto bg-zinc-900 text-green-400 text-xs rounded-xl p-5 font-mono leading-relaxed">{{ $this->exportJson() }}</pre>
        </div>
        @endif
    </div>

    {{-- ═══════════════════ RIGHT: Settings Panel ═══════════════════ --}}
    @if ($activeTab === 'builder' && $selectedField !== null)
    <aside class="w-72 shrink-0 bg-white border-l border-zinc-200 flex flex-col overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-200">
            <p class="font-semibold text-zinc-700 text-xs uppercase tracking-wider">Field Settings</p>
            <flux:button wire:click="selectField({{ $selectedFieldIndex }})" variant="ghost" size="xs" icon="x-mark" />
        </div>
        <div class="flex-1 overflow-y-auto">
            @include('livewire-form-builder::settings.panel', [
                'field'     => $selectedField,
                'index'     => $selectedFieldIndex,
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
            event.currentTarget.classList.add('ring-2', 'ring-zinc-400', 'ring-offset-1');
        },

        onFieldDragLeave(event) {
            event.currentTarget.classList.remove('ring-2', 'ring-zinc-400', 'ring-offset-1');
        },

        onFieldDrop(event, targetIndex) {
            event.currentTarget.classList.remove('ring-2', 'ring-zinc-400', 'ring-offset-1');

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
