{{-- resources/views/forms/edit.blade.php --}}
<x-livewire-form-builder::layout title="Edit: {{ $form->name }}">
    <livewire:livewire-form-builder::builder :form-id="$form->id" />
</x-livewire-form-builder::layout>
