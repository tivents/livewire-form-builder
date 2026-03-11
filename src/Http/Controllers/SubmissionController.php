<?php

namespace Tivents\LivewireFormBuilder\Http\Controllers;

use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tivents\LivewireFormBuilder\Contracts\FormRepositoryContract;

class SubmissionController extends Controller
{
    public function __construct(protected FormRepositoryContract $repo) {}

    public function index(int|string $formId)
    {
        $form        = $this->repo->findOrFail($formId);
        $submissions = $this->repo->paginateSubmissions($formId, config('livewire-form-builder.per_page', 25));
        return view('livewire-form-builder::submissions.index', compact('form', 'submissions'));
    }

    public function show(int|string $formId, int|string $submissionId)
    {
        $form       = $this->repo->findOrFail($formId);
        $submission = $this->repo->findSubmissionOrFail($formId, $submissionId);
        return view('livewire-form-builder::submissions.show', compact('form', 'submission'));
    }

    public function destroy(int|string $formId, int|string $submissionId)
    {
        $this->repo->deleteSubmission($submissionId);
        return redirect()
            ->route('livewire-form-builder.submissions.index', $formId)
            ->with('success', 'Submission deleted.');
    }

    public function export(int|string $formId): StreamedResponse
    {
        $form        = $this->repo->findOrFail($formId);
        $submissions = $this->repo->paginateSubmissions($formId, 99999);
        $schema      = is_array($form->schema) ? $form->schema : (json_decode($form->schema, true) ?? []);

        $columns = collect($schema)
            ->filter(fn ($f) => !in_array($f['type'] ?? '', ['heading', 'hint', 'html']))
            ->map(fn ($f) => ['key' => $f['key'], 'label' => $f['label'] ?? $f['key']])
            ->values();

        $items = method_exists($submissions, 'items') ? $submissions->items() : (array) $submissions;

        return response()->streamDownload(function () use ($items, $columns) {
            $out = fopen('php://output', 'w');

            fputcsv($out, array_merge(
                ['ID', 'Submitted At', 'IP'],
                $columns->pluck('label')->toArray()
            ));

            foreach ($items as $sub) {
                $data = is_array($sub->data) ? $sub->data : (json_decode($sub->data, true) ?? []);
                $row  = [
                    $sub->id,
                    $sub->created_at instanceof \DateTimeInterface
                        ? $sub->created_at->format('Y-m-d H:i:s')
                        : $sub->created_at,
                    $sub->ip ?? '',
                ];
                foreach ($columns as $col) {
                    $val  = $data[$col['key']] ?? '';
                    $row[] = is_array($val) ? implode(', ', $val) : $val;
                }
                fputcsv($out, $row);
            }

            fclose($out);
        }, 'submissions-' . $formId . '-' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
