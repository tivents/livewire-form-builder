{{-- resources/views/submissions/viewer.blade.php --}}
@once
<style>
    .fa-sv { font-family: ui-sans-serif, system-ui, sans-serif; color: #111827; }
    .fa-sv *, .fa-sv *::before, .fa-sv *::after { box-sizing: border-box; }
    .fa-sv table { width: 100%; border-collapse: collapse; }
    .fa-sv th, .fa-sv td { text-align: left; padding: 0.625rem 0.875rem; font-size: 0.875rem; }
    .fa-sv thead th { background: #f9fafb; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; border-bottom: 1px solid #e5e7eb; }
    .fa-sv tbody tr { border-bottom: 1px solid #f3f4f6; }
    .fa-sv tbody tr:hover { background: #f9fafb; }
    .fa-sv .fa-sv-btn { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.625rem; font-size: 0.75rem; font-weight: 500; border: 1px solid transparent; border-radius: 0.375rem; cursor: pointer; transition: background 0.15s; background: none; }
    .fa-sv .fa-sv-btn-view   { color: #2563eb; border-color: #bfdbfe; background: #eff6ff; }
    .fa-sv .fa-sv-btn-view:hover   { background: #dbeafe; }
    .fa-sv .fa-sv-btn-delete { color: #dc2626; border-color: #fecaca; background: #fef2f2; }
    .fa-sv .fa-sv-btn-delete:hover { background: #fee2e2; }
    .fa-sv .fa-sv-btn-back   { color: #374151; border-color: #d1d5db; background: #f9fafb; }
    .fa-sv .fa-sv-btn-back:hover   { background: #f3f4f6; }
    .fa-sv .fa-sv-badge { display: inline-block; font-size: 0.7rem; font-weight: 600; padding: 0.1rem 0.5rem; border-radius: 9999px; background: #dcfce7; color: #166534; }
    .fa-sv .fa-sv-empty { padding: 3rem 1rem; text-align: center; color: #9ca3af; font-size: 0.875rem; }
    .fa-sv .fa-sv-dl dt { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-bottom: 0.125rem; }
    .fa-sv .fa-sv-dl dd { font-size: 0.9rem; color: #111827; margin: 0 0 1.25rem; }
    .fa-sv .fa-sv-panel { background: #fff; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden; }
    .fa-sv .fa-sv-panel-header { padding: 1rem 1.25rem; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; }
    .fa-sv .fa-sv-panel-title { font-weight: 600; font-size: 0.95rem; }
    .fa-sv .fa-sv-section-title { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #9ca3af; margin: 0 0 0.75rem; padding-bottom: 0.375rem; border-bottom: 1px solid #f3f4f6; }
    .fa-sv .fa-sv-pagination { display: flex; align-items: center; gap: 0.375rem; padding: 0.75rem 1rem; border-top: 1px solid #e5e7eb; font-size: 0.8rem; color: #6b7280; }
    .fa-sv .fa-sv-page-btn { padding: 0.25rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; background: #fff; cursor: pointer; font-size: 0.8rem; color: #374151; }
    .fa-sv .fa-sv-page-btn:hover { background: #f3f4f6; }
    .fa-sv .fa-sv-page-btn[disabled] { opacity: 0.4; cursor: not-allowed; }
    .fa-sv .fa-sv-page-active { background: #2563eb; color: #fff; border-color: #2563eb; }
</style>
@endonce

<div class="fa-sv">

    {{-- ── Detail view ──────────────────────────────────────────────── --}}
    @if ($activeSubmissionId)
        <div class="fa-sv-panel">
            <div class="fa-sv-panel-header">
                <span class="fa-sv-panel-title">
                    {{ $idField }}: {{ $activeSubmissionId }}
                </span>
                <div style="display:flex;gap:0.5rem;align-items:center;">
                    @if ($allowDelete)
                        <button
                            class="fa-sv-btn fa-sv-btn-delete"
                            wire:click="deleteSubmission('{{ $activeSubmissionId }}')"
                            wire:confirm="{{ __('livewire-form-builder::messages.viewer.really_delete') }}"
                        >
                            <svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            {{ __('livewire-form-builder::messages.viewer.delete') }}
                        </button>
                    @endif
                    <button class="fa-sv-btn fa-sv-btn-back" wire:click="closeDetail">
                        {{ __('livewire-form-builder::messages.viewer.back') }}
                    </button>
                </div>
            </div>

            <div style="padding:1.5rem 1.25rem;">

                {{-- Extra columns (submission-level fields) --}}
                @if (!empty($extraColumns))
                    <p class="fa-sv-section-title">{{ __('livewire-form-builder::messages.viewer.meta') }}</p>
                    <dl class="fa-sv-dl">
                        @foreach ($extraColumns as $col)
                            @php $colVal = $activeSubmissionMeta[$col['field']] ?? null; @endphp
                            <dt>{{ $col['label'] ?? $col['field'] }}</dt>
                            <dd>
                                @if ($colVal === null || $colVal === '')
                                    <span style="color:#9ca3af;">—</span>
                                @else
                                    {{ $colVal }}
                                @endif
                            </dd>
                        @endforeach
                    </dl>
                    <p class="fa-sv-section-title" style="margin-top:1.5rem;">{{ __('livewire-form-builder::messages.viewer.form_data') }}</p>
                @endif

                {{-- Form field values --}}
                <dl class="fa-sv-dl">
                    @forelse ($fieldLabels as $key => $label)
                        <dt>{{ $label }}</dt>
                        <dd>
                            @php $val = $activeSubmission[$key] ?? null; @endphp
                            @if (is_array($val))
                                {{ implode(', ', $val) }}
                            @elseif ($val === null || $val === '')
                                <span style="color:#9ca3af;">—</span>
                            @else
                                {{ $val }}
                            @endif
                        </dd>
                    @empty
                        @foreach ($activeSubmission as $key => $val)
                            <dt>{{ $key }}</dt>
                            <dd>
                                @if (is_array($val))
                                    {{ implode(', ', $val) }}
                                @elseif ($val === null || $val === '')
                                    <span style="color:#9ca3af;">—</span>
                                @else
                                    {{ $val }}
                                @endif
                            </dd>
                        @endforeach
                    @endforelse
                </dl>
            </div>
        </div>

    {{-- ── List view ────────────────────────────────────────────────── --}}
    @else
        <div class="fa-sv-panel">
            <div class="fa-sv-panel-header">
                <span class="fa-sv-panel-title">
                    {{ $formName ?: __('livewire-form-builder::messages.viewer.empty') }}
                    @if ($submissions->total() > 0)
                        <span class="fa-sv-badge" style="margin-left:0.5rem;">{{ $submissions->total() }}</span>
                    @endif
                </span>
            </div>

            @if ($submissions->isEmpty())
                <div class="fa-sv-empty">{{ __('livewire-form-builder::messages.viewer.empty') }}</div>
            @else
                <div style="overflow-x:auto;">
                    <table>
                        @php $previewCount = max(1, 4 - count($extraColumns)); @endphp
                        <thead>
                            <tr>
                                <th>{{ $dateField }}</th>
                                <th>{{ $idField }}</th>
                                {{-- Extra columns --}}
                                @foreach ($extraColumns as $col)
                                    <th>{{ $col['label'] ?? $col['field'] }}</th>
                                @endforeach
                                {{-- Up to 3 form fields (fewer if extra columns present) --}}
                                @foreach (array_slice($fieldLabels, 0, $previewCount, true) as $key => $label)
                                    <th>{{ $label }}</th>
                                @endforeach
                                <th style="text-align:right;">{{ __('livewire-form-builder::messages.viewer.column.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($submissions as $submission)
                                @php
                                    $sId   = $submission->{$idField} ?? $submission->id;
                                    $sDate = $submission->{$dateField} ?? null;
                                    $sData = $submission->data ?? [];
                                    if (!is_array($sData)) $sData = json_decode($sData, true) ?? [];
                                    $previewKeys = array_slice(array_keys($fieldLabels), 0, $previewCount);
                                @endphp
                                <tr>
                                    <td style="color:#6b7280;font-size:0.8rem;white-space:nowrap;">
                                        @if ($sDate instanceof \Carbon\Carbon || $sDate instanceof \DateTimeInterface)
                                            {{ $sDate->format('d.m.Y H:i') }}
                                        @elseif ($sDate)
                                            {{ \Carbon\Carbon::parse($sDate)->format('d.m.Y H:i') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td style="color:#9ca3af;font-size:0.8rem;">{{ $sId }}</td>

                                    {{-- Extra columns --}}
                                    @foreach ($extraColumns as $col)
                                        <td>{{ $submission->{$col['field']} ?? '—' }}</td>
                                    @endforeach

                                    {{-- Form data preview --}}
                                    @foreach ($previewKeys as $key)
                                        <td>
                                            @php $v = $sData[$key] ?? null; @endphp
                                            @if (is_array($v))
                                                {{ implode(', ', $v) }}
                                            @elseif ($v === null || $v === '')
                                                <span style="color:#d1d5db;">—</span>
                                            @else
                                                {{ \Illuminate\Support\Str::limit((string) $v, 40) }}
                                            @endif
                                        </td>
                                    @endforeach

                                    <td style="text-align:right;white-space:nowrap;">
                                        <button
                                            class="fa-sv-btn fa-sv-btn-view"
                                            wire:click="viewSubmission('{{ $sId }}')"
                                        >
                                            <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            {{ __('livewire-form-builder::messages.viewer.view') }}
                                        </button>
                                        @if ($allowDelete)
                                            <button
                                                class="fa-sv-btn fa-sv-btn-delete"
                                                wire:click="deleteSubmission('{{ $sId }}')"
                                                wire:confirm="{{ __('livewire-form-builder::messages.viewer.really_delete') }}"
                                                style="margin-left:0.25rem;"
                                            >
                                                <svg style="width:13px;height:13px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                {{ __('livewire-form-builder::messages.viewer.delete') }}
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($submissions->hasPages())
                    <div class="fa-sv-pagination">
                        <button
                            class="fa-sv-page-btn"
                            wire:click="previousPage"
                            @disabled($submissions->onFirstPage())
                        >{{ __('livewire-form-builder::messages.viewer.prev') }}</button>

                        @foreach ($submissions->getUrlRange(1, $submissions->lastPage()) as $page => $url)
                            <button
                                class="fa-sv-page-btn {{ $page == $submissions->currentPage() ? 'fa-sv-page-active' : '' }}"
                                wire:click="gotoPage({{ $page }})"
                            >{{ $page }}</button>
                        @endforeach

                        <button
                            class="fa-sv-page-btn"
                            wire:click="nextPage"
                            @disabled(!$submissions->hasMorePages())
                        >{{ __('livewire-form-builder::messages.viewer.next') }}</button>

                        <span style="margin-left:auto;">
                            {{ $submissions->firstItem() }}–{{ $submissions->lastItem() }} {{ __('livewire-form-builder::messages.viewer.of') }} {{ $submissions->total() }}
                        </span>
                    </div>
                @endif
            @endif
        </div>
    @endif
</div>
