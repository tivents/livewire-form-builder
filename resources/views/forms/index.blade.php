{{-- resources/views/forms/index.blade.php --}}
<x-livewire-form-builder::layout :title="__('livewire-form-builder::messages.forms.title')">
    <div class="max-w-5xl mx-auto py-8 px-4">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">{{ __('livewire-form-builder::messages.forms.title') }}</h1>
            <a href="{{ route('livewire-form-builder.forms.create') }}"
               class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('livewire-form-builder::messages.forms.new') }}
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 text-green-800 border border-green-200 text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            @if ($forms->isEmpty())
                <div class="py-16 text-center text-gray-400">
                    <p class="text-sm">{{ __('livewire-form-builder::messages.forms.empty') }} <a href="{{ route('livewire-form-builder.forms.create') }}" class="text-indigo-600 hover:underline">{{ __('livewire-form-builder::messages.forms.create_first') }}</a></p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">{{ __('livewire-form-builder::messages.forms.column.name') }}</th>
                            <th class="text-center px-5 py-3 font-semibold text-gray-600">{{ __('livewire-form-builder::messages.forms.column.fields') }}</th>
                            <th class="text-center px-5 py-3 font-semibold text-gray-600">{{ __('livewire-form-builder::messages.forms.column.submissions') }}</th>
                            <th class="text-center px-5 py-3 font-semibold text-gray-600">{{ __('livewire-form-builder::messages.forms.column.status') }}</th>
                            <th class="text-right px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($forms as $form)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-gray-800">{{ $form->name }}</p>
                                @if ($form->description) <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($form->description, 60) }}</p> @endif
                            </td>
                            <td class="px-5 py-4 text-center text-gray-600">{{ $form->field_count }}</td>
                            <td class="px-5 py-4 text-center">
                                <a href="{{ route('livewire-form-builder.submissions.index', $form) }}" class="text-indigo-600 hover:underline">{{ $form->submissions_count }}</a>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if ($form->is_active)
                                    <span class="inline-block bg-green-100 text-green-700 text-xs font-medium px-2 py-0.5 rounded-full">{{ __('livewire-form-builder::messages.forms.status.active') }}</span>
                                @else
                                    <span class="inline-block bg-gray-100 text-gray-500 text-xs font-medium px-2 py-0.5 rounded-full">{{ __('livewire-form-builder::messages.forms.status.inactive') }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('livewire-form-builder.forms.edit', $form) }}"
                                       class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">{{ __('livewire-form-builder::messages.forms.edit') }}</a>
                                    <form action="{{ route('livewire-form-builder.forms.destroy', $form) }}" method="POST"
                                          onsubmit="return confirm('{{ __('livewire-form-builder::messages.forms.delete_confirm') }}')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors">{{ __('livewire-form-builder::messages.forms.delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-5 py-4 border-t border-gray-100">{{ $forms->links() }}</div>
            @endif
        </div>
    </div>
</x-livewire-form-builder::layout>
