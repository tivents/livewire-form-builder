{{-- resources/views/submissions/index.blade.php --}}
<x-livewire-form-builder::layout :title="'Submissions: ' . $form->name">
    <div class="max-w-5xl mx-auto py-8 px-4">
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route('livewire-form-builder.forms.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Forms</a>
                <h1 class="text-2xl font-bold text-gray-800 mt-1">{{ $form->name }} – Submissions</h1>
            </div>
            <a href="{{ route('livewire-form-builder.submissions.export', $form) }}"
               class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-xl hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export CSV
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            @if ($submissions->isEmpty())
                <div class="py-16 text-center text-gray-400 text-sm">No submissions yet.</div>
            @else
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">#</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">Submitted At</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">IP</th>
                            <th class="text-center px-5 py-3 font-semibold text-gray-600">Status</th>
                            <th class="text-right px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($submissions as $sub)
                        <tr class="hover:bg-gray-50 transition-colors {{ !$sub->is_read ? 'font-semibold' : '' }}">
                            <td class="px-5 py-3 text-gray-600">{{ $sub->id }}</td>
                            <td class="px-5 py-3 text-gray-700">{{ $sub->created_at->format('d.m.Y H:i') }}</td>
                            <td class="px-5 py-3 text-gray-500 font-mono text-xs">{{ $sub->ip }}</td>
                            <td class="px-5 py-3 text-center">
                                @if ($sub->is_read)
                                    <span class="text-xs text-gray-400">Read</span>
                                @else
                                    <span class="inline-block bg-indigo-100 text-indigo-700 text-xs font-bold px-2 py-0.5 rounded-full">New</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('livewire-form-builder.submissions.show', [$form, $sub]) }}"
                                       class="text-xs px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg">View</a>
                                    <form action="{{ route('livewire-form-builder.submissions.destroy', [$form, $sub]) }}" method="POST"
                                          onsubmit="return confirm('Delete this submission?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-5 py-4 border-t border-gray-100">{{ $submissions->links() }}</div>
            @endif
        </div>
    </div>
</x-livewire-form-builder::layout>
