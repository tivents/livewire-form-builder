{{-- resources/views/submissions/show.blade.php --}}
<x-livewire-form-builder::layout title="Submission #{{ $submission->id }}">
    <div class="max-w-2xl mx-auto py-8 px-4">
        <div class="mb-6">
            <flux:link href="{{ route('livewire-form-builder.submissions.index', $form) }}" class="text-sm text-zinc-500">
                ← Submissions
            </flux:link>
            <flux:heading size="xl" class="mt-1">{{ $form->name }} – #{{ $submission->id }}</flux:heading>
            <flux:text class="text-sm text-zinc-500 mt-1">
                {{ $submission->created_at->format('d.m.Y H:i:s') }} from {{ $submission->ip }}
            </flux:text>
        </div>

        <flux:card class="p-0 divide-y divide-zinc-100">
            @foreach (($form->schema ?? []) as $field)
                @php
                    $key   = $field['key']   ?? null;
                    $type  = $field['type']  ?? null;
                    if (!$key || in_array($type, ['heading', 'hint', 'html'])) continue;
                    $value = $submission->data[$key] ?? null;
                @endphp
                <div class="px-5 py-4">
                    <p class="text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-1">
                        {{ $field['label'] ?? $key }}
                    </p>
                    @if (is_array($value))
                        <div class="space-y-2">
                            @foreach ($value as $row)
                                <div class="bg-zinc-50 rounded-lg px-3 py-2 text-sm">
                                    @if (is_array($row))
                                        @foreach ($row as $rk => $rv)
                                            <span class="text-zinc-400 text-xs">{{ $rk }}:</span>
                                            <span class="text-zinc-800">{{ $rv }}</span>
                                        @endforeach
                                    @else
                                        {{ $row }}
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @elseif ($value !== null && $value !== '')
                        <flux:text class="text-sm">{{ $value }}</flux:text>
                    @else
                        <flux:text class="text-sm text-zinc-400 italic">—</flux:text>
                    @endif
                </div>
            @endforeach
        </flux:card>
    </div>
</x-livewire-form-builder::layout>
