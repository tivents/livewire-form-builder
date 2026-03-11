<?php

use Illuminate\Support\Facades\Route;
use Tivents\LivewireFormBuilder\Http\Controllers\FormController;
use Tivents\LivewireFormBuilder\Http\Controllers\SubmissionController;

$prefix     = config('livewire-form-builder.route_prefix', 'livewire-form-builder');
$middleware = config('livewire-form-builder.middleware', ['web', 'auth']);

Route::prefix($prefix)
    ->middleware($middleware)
    ->name('livewire-form-builder.')
    ->group(function () {
        // Form management
        Route::get('/',                    [FormController::class, 'index'])->name('forms.index');
        Route::get('/create',              [FormController::class, 'create'])->name('forms.create');
        Route::get('/{formId}/edit',       [FormController::class, 'edit'])->name('forms.edit');
        Route::delete('/{formId}',         [FormController::class, 'destroy'])->name('forms.destroy');

        // Submission management
        Route::get('/{formId}/submissions',                          [SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('/{formId}/submissions/export',                   [SubmissionController::class, 'export'])->name('submissions.export');
        Route::get('/{formId}/submissions/{submissionId}',           [SubmissionController::class, 'show'])->name('submissions.show');
        Route::delete('/{formId}/submissions/{submissionId}',        [SubmissionController::class, 'destroy'])->name('submissions.destroy');
    });
