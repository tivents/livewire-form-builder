{{-- resources/views/forms/index.blade.php --}}
<x-livewire-form-builder::layout title="Forms">
    <div class="max-w-5xl mx-auto py-8 px-4">
        <div class="flex items-center justify-between mb-6">
            <flux:heading size="xl">Forms</flux:heading>
            <flux:button href="{{ route('livewire-form-builder.forms.create') }}" variant="primary" icon="plus">
                New Form
            </flux:button>
        </div>

        @if (session('success'))
            <div class="mb-4 flex items-center gap-2 px-4 py-3 rounded-lg bg-green-50 text-green-800 border border-green-200 text-sm">
                <flux:icon.check-circle class="size-4 shrink-0" />
                {{ session('success') }}
            </div>
        @endif

        <flux:card class="p-0 overflow-hidden">
            @if ($forms->isEmpty())
                <div class="py-16 text-center">
                    <flux:text class="text-zinc-400 text-sm">
                        No forms yet.
                        <flux:link href="{{ route('livewire-form-builder.forms.create') }}">Create your first form →</flux:link>
                    </flux:text>
                </div>
            @else
                <flux:table>
                    <flux:columns>
                        <flux:column>Name</flux:column>
                        <flux:column class="text-center">Fields</flux:column>
                        <flux:column class="text-center">Submissions</flux:column>
                        <flux:column class="text-center">Status</flux:column>
                        <flux:column />
                    </flux:columns>
                    <flux:rows>
                        @foreach ($forms as $form)
                        <flux:row>
                            <flux:cell>
                                <p class="font-semibold text-zinc-800">{{ $form->name }}</p>
                                @if ($form->description)
                                    <p class="text-xs text-zinc-400 mt-0.5">{{ Str::limit($form->description, 60) }}</p>
                                @endif
                            </flux:cell>
                            <flux:cell class="text-center text-zinc-600">{{ $form->field_count }}</flux:cell>
                            <flux:cell class="text-center">
                                <flux:link href="{{ route('livewire-form-builder.submissions.index', $form) }}">
                                    {{ $form->submissions_count }}
                                </flux:link>
                            </flux:cell>
                            <flux:cell class="text-center">
                                @if ($form->is_active)
                                    <flux:badge color="green" size="sm">Active</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">Inactive</flux:badge>
                                @endif
                            </flux:cell>
                            <flux:cell class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button href="{{ route('livewire-form-builder.forms.edit', $form) }}" size="sm" variant="ghost">
                                        Edit
                                    </flux:button>
                                    <form action="{{ route('livewire-form-builder.forms.destroy', $form) }}" method="POST"
                                          onsubmit="return confirm('Delete this form?')">
                                        @csrf @method('DELETE')
                                        <flux:button type="submit" size="sm" variant="danger">Delete</flux:button>
                                    </form>
                                </div>
                            </flux:cell>
                        </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>
                <div class="px-5 py-4 border-t border-zinc-100">{{ $forms->links() }}</div>
            @endif
        </flux:card>
    </div>
</x-livewire-form-builder::layout>
