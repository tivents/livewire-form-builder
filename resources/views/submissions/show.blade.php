{{-- resources/views/submissions/show.blade.php --}}
<x-livewire-form-builder::layout title="Submission #{{ $submission->id }}">
    <div class="max-w-2xl mx-auto py-8 px-4">
        <div class="mb-6">
            <a href="{{ route('livewire-form-builder.submissions.index', $form) }}" class="text-sm text-gray-500 hover:text-gray-700">← Submissions</a>
            <h1 class="text-2xl font-bold text-gray-800 mt-1">{{ $form->name }} – #{{ $submission->id }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $submission->created_at->format('d.m.Y H:i:s') }} from {{ $submission->ip }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm divide-y divide-gray-100">
            @foreach (($form->schema ?? []) as $field)
                @php
                    $key   = $field['key']   ?? null;
                    $type  = $field['type']  ?? null;
                    if (!$key || in_array($type, ['heading','hint','html'])) continue;
                    $value = $submission->data[$key] ?? null;
                @endphp
                <div class="px-5 py-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">{{ $field['label'] ?? $key }}</p>
                    @if (is_array($value))
                        <div class="space-y-2">
                            @foreach ($value as $row)
                                <div class="bg-gray-50 rounded-lg px-3 py-2 text-sm">
                                    @if (is_array($row))
                                        @foreach ($row as $rk => $rv) <span class="text-gray-500 text-xs">{{ $rk }}:</span> <span class="text-gray-800">{{ $rv }}</span> @endforeach
                                    @else
                                        {{ $row }}
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @elseif ($value !== null && $value !== '')
                        <p class="text-gray-800 text-sm">{{ $value }}</p>
                    @else
                        <p class="text-gray-400 text-sm italic">—</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</x-livewire-form-builder::layout>
