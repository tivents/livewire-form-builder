{{-- resources/views/partials/field-preview.blade.php --}}
@php $type = $field['type'] ?? 'text'; @endphp

@if ($type === 'heading')
    @php $tag = $field['level'] ?? 'h2'; $sizes = ['h1'=>'text-2xl','h2'=>'text-xl','h3'=>'text-lg','h4'=>'text-base']; @endphp
    <{{ $tag }} class="{{ $sizes[$tag] ?? 'text-xl' }} font-bold text-gray-800">{{ $field['text'] ?? 'Heading' }}</{{ $tag }}>

@elseif ($type === 'hint')
    @php $styles = ['info'=>'bg-blue-50 text-blue-800 border-blue-200','warning'=>'bg-yellow-50 text-yellow-800 border-yellow-200','success'=>'bg-green-50 text-green-800 border-green-200','error'=>'bg-red-50 text-red-800 border-red-200']; $s = $styles[$field['style'] ?? 'info']; @endphp
    <div class="rounded-lg border px-3 py-2 text-xs {{ $s }}">{{ $field['text'] ?? 'Hint text' }}</div>

@elseif ($type === 'html')
    <div class="text-xs text-gray-500 bg-gray-50 rounded p-2 font-mono">{{ Str::limit($field['content'] ?? '<p>…</p>', 80) }}</div>

@elseif ($type === 'divider')
    <hr class="border-t border-gray-200" />

@elseif ($type === 'number')
    <div class="space-y-1.5">
        <div class="flex items-center gap-1">
            <span class="text-xs font-semibold text-gray-700">{{ $field['label'] ?? 'Number' }}</span>
            @if (!empty($field['required'])) <span class="text-red-500 text-xs">*</span> @endif
        </div>
        <div class="h-8 bg-gray-50 border border-gray-200 rounded-md px-2 flex items-center text-xs text-gray-400">
            0
        </div>
    </div>

@elseif ($type === 'toggle')
    <div class="flex items-center gap-3">
        <div class="w-10 h-5 bg-gray-200 rounded-full relative">
            <div class="w-4 h-4 bg-white rounded-full shadow absolute top-0.5 left-0.5"></div>
        </div>
        <span class="text-xs font-semibold text-gray-700">{{ $field['label'] ?? 'Toggle' }}</span>
    </div>

@elseif ($type === 'hidden')
    <div class="flex items-center gap-2 text-xs text-gray-400">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
        Hidden: {{ $field['key'] }}</div>
        <div class="flex items-center gap-1">
            <span class="text-xs font-semibold text-gray-700">{{ $field['label'] ?? 'Untitled' }}</span>
            @if (!empty($field['required'])) <span class="text-red-500 text-xs">*</span> @endif
            @if (!empty($field['hint'])) <span class="text-gray-400 text-xs">({{ $field['hint'] }})</span> @endif
        </div>

        @if (in_array($type, ['text', 'textarea']))
            <div class="h-8 bg-gray-50 border border-gray-200 rounded-md px-2 flex items-center text-xs text-gray-400">
                {{ $field['placeholder'] ?? '' }}
            </div>

        @elseif ($type === 'select')
            <div class="h-8 bg-gray-50 border border-gray-200 rounded-md px-2 flex items-center justify-between text-xs text-gray-400">
                <span>{{ $field['placeholder'] ?? 'Select…' }}</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>

        @elseif (in_array($type, ['checkbox', 'radio']))
            <div class="{{ !empty($field['inline']) ? 'flex gap-4' : 'space-y-1' }}">
                @foreach (array_slice($field['options'] ?? [], 0, 3) as $opt)
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 rounded-{{ $type === 'radio' ? 'full' : 'sm' }} border border-gray-300 bg-white"></div>
                        <span class="text-xs text-gray-600">{{ $opt['label'] }}</span>
                    </div>
                @endforeach
                @if (count($field['options'] ?? []) > 3)
                    <span class="text-xs text-gray-400">+{{ count($field['options']) - 3 }} more…</span>
                @endif
            </div>

        @elseif ($type === 'datetime')
            <div class="h-8 bg-gray-50 border border-gray-200 rounded-md px-2 flex items-center gap-2 text-xs text-gray-400">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                {{ $field['mode'] === 'time' ? 'hh:mm' : 'DD/MM/YYYY' }}
            </div>

        @elseif ($type === 'file')
            <div class="border-2 border-dashed border-gray-200 rounded-md py-4 flex flex-col items-center text-xs text-gray-400">
                <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                {{ !empty($field['multiple']) ? 'Upload files' : 'Upload file' }}
            </div>

        @elseif ($type === 'repeater')
            <div class="border border-dashed border-gray-300 rounded-lg p-3 text-xs text-gray-500 space-y-1">
                <p class="font-medium">Repeater: {{ count($field['children'] ?? []) }} child field(s)</p>
                @foreach (($field['children'] ?? []) as $child)
                    <span class="inline-block bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded text-[10px]">{{ $child['label'] ?? $child['type'] }}</span>
                @endforeach
                <div class="mt-2 text-indigo-400">+ {{ $field['add_label'] ?? 'Add item' }}</div>
            </div>
        @endif
    </div>
@endif
