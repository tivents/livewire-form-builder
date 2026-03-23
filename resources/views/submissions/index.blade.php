{{-- resources/views/submissions/index.blade.php --}}
<x-livewire-form-builder::layout :title="'Submissions: ' . $form->name">
    <div class="max-w-5xl mx-auto py-8 px-4">
        <div class="flex items-center justify-between mb-6">
            <div>
                <flux:link href="{{ route('livewire-form-builder.forms.index') }}" class="text-sm text-zinc-500">
                    ← Forms
                </flux:link>
                <flux:heading size="xl" class="mt-1">{{ $form->name }} – Submissions</flux:heading>
            </div>
            <flux:button href="{{ route('livewire-form-builder.submissions.export', $form) }}" variant="ghost" icon="arrow-down-tray">
                Export CSV
            </flux:button>
        </div>

        <flux:card class="p-0 overflow-hidden">
            @if ($submissions->isEmpty())
                <div class="py-16 text-center">
                    <flux:text class="text-zinc-400 text-sm">No submissions yet.</flux:text>
                </div>
            @else
                <flux:table>
                    <flux:columns>
                        <flux:column>#</flux:column>
                        <flux:column>Submitted At</flux:column>
                        <flux:column>IP</flux:column>
                        <flux:column class="text-center">Status</flux:column>
                        <flux:column />
                    </flux:columns>
                    <flux:rows>
                        @foreach ($submissions as $sub)
                        <flux:row class="{{ !$sub->is_read ? 'font-semibold' : '' }}">
                            <flux:cell class="text-zinc-500">{{ $sub->id }}</flux:cell>
                            <flux:cell class="text-zinc-700">{{ $sub->created_at->format('d.m.Y H:i') }}</flux:cell>
                            <flux:cell class="font-mono text-xs text-zinc-400">{{ $sub->ip }}</flux:cell>
                            <flux:cell class="text-center">
                                @if ($sub->is_read)
                                    <flux:badge color="zinc" size="sm">Read</flux:badge>
                                @else
                                    <flux:badge color="indigo" size="sm">New</flux:badge>
                                @endif
                            </flux:cell>
                            <flux:cell class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button href="{{ route('livewire-form-builder.submissions.show', [$form, $sub]) }}" size="sm" variant="ghost">
                                        View
                                    </flux:button>
                                    <form action="{{ route('livewire-form-builder.submissions.destroy', [$form, $sub]) }}" method="POST"
                                          onsubmit="return confirm('Delete this submission?')">
                                        @csrf @method('DELETE')
                                        <flux:button type="submit" size="sm" variant="danger">Delete</flux:button>
                                    </form>
                                </div>
                            </flux:cell>
                        </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>
                <div class="px-5 py-4 border-t border-zinc-100">{{ $submissions->links() }}</div>
            @endif
        </flux:card>
    </div>
</x-livewire-form-builder::layout>
