<?php

namespace Tivents\LivewireFormBuilder\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Tivents\LivewireFormBuilder\Contracts\FormRepositoryContract;

class FormController extends Controller
{
    public function __construct(protected FormRepositoryContract $repo) {}

    public function index()
    {
        $forms = $this->repo->paginate(config('livewire-form-builder.per_page', 25));
        return view('livewire-form-builder::forms.index', compact('forms'));
    }

    public function create()
    {
        return view('livewire-form-builder::forms.create');
    }

    public function edit(int|string $formId)
    {
        $form = $this->repo->findOrFail($formId);
        return view('livewire-form-builder::forms.edit', compact('form'));
    }

    public function destroy(int|string $formId)
    {
        $this->repo->delete($formId);
        return redirect()
            ->route('livewire-form-builder.forms.index')
            ->with('success', 'Form deleted.');
    }
}
