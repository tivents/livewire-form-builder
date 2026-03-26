<?php

use Livewire\Livewire;
use Tivents\LivewireFormBuilder\Components\FormBuilder;
use Tivents\LivewireFormBuilder\Contracts\FormRepositoryContract;
use Tivents\LivewireFormBuilder\Tests\Fakes\FakeFormRepository;

it('can mount with new form', function () {
    Livewire::test(FormBuilder::class)
        ->assertSet('formId', null)
        ->assertSet('name', '')
        ->assertSet('isActive', true)
        ->assertSet('schema', []);
});

it('can mount with existing form', function () {
    $form = app(FormRepositoryContract::class)->create([
        'name'        => 'Test Form',
        'description' => 'Test description',
        'is_active'   => true,
        'schema'      => [
            ['type' => 'text', 'key' => 'field_1', 'label' => 'Name'],
        ],
    ]);

    Livewire::test(FormBuilder::class, ['formId' => $form->id])
        ->assertSet('formId', $form->id)
        ->assertSet('name', 'Test Form')
        ->assertSet('description', 'Test description')
        ->assertSet('isActive', true)
        ->assertCount('schema', 1);
});

it('can add field to schema', function () {
    Livewire::test(FormBuilder::class)
        ->call('addField', 'text')
        ->assertCount('schema', 1)
        ->assertSet('selectedFieldIndex', 0)
        ->call('addField', 'email')
        ->assertCount('schema', 2)
        ->assertSet('selectedFieldIndex', 1);
});

it('can add field after specific index', function () {
    Livewire::test(FormBuilder::class)
        ->call('addField', 'text')
        ->call('addField', 'email')
        ->call('addField', 'number', 0)
        ->assertCount('schema', 3)
        ->assertSet('selectedFieldIndex', 1)
        ->assertSet('schema.1.type', 'number');
});

it('can select and deselect field', function () {
    Livewire::test(FormBuilder::class)
        ->call('addField', 'text')
        ->call('selectField', 0)
        ->assertSet('selectedFieldIndex', 0)
        ->call('selectField', 0)
        ->assertSet('selectedFieldIndex', null);
});

it('can delete field', function () {
    Livewire::test(FormBuilder::class)
        ->call('addField', 'text')
        ->call('addField', 'email')
        ->assertCount('schema', 2)
        ->call('deleteField', 0)
        ->assertCount('schema', 1)
        ->assertSet('selectedFieldIndex', null);
});

it('can duplicate field', function () {
    Livewire::test(FormBuilder::class)
        ->call('addField', 'text')
        ->call('updateFieldConfig', 0, 'label', 'Original Label')
        ->call('duplicateField', 0)
        ->assertCount('schema', 2)
        ->assertSet('selectedFieldIndex', 1)
        ->assertSet('schema.1.label', 'Original Label');
});

it('can move field up', function () {
    Livewire::test(FormBuilder::class)
        ->call('addField', 'text')
        ->call('updateFieldConfig', 0, 'label', 'First')
        ->call('addField', 'email')
        ->call('updateFieldConfig', 1, 'label', 'Second')
        ->call('moveUp', 1)
        ->assertSet('schema.0.label', 'Second')
        ->assertSet('schema.1.label', 'First');
});

it('can move field down', function () {
    Livewire::test(FormBuilder::class)
        ->call('addField', 'text')
        ->call('updateFieldConfig', 0, 'label', 'First')
        ->call('addField', 'email')
        ->call('updateFieldConfig', 1, 'label', 'Second')
        ->call('moveDown', 0)
        ->assertSet('schema.0.label', 'Second')
        ->assertSet('schema.1.label', 'First');
});

it('can update field config', function () {
    Livewire::test(FormBuilder::class)
        ->call('addField', 'text')
        ->call('updateFieldConfig', 0, 'label', 'Full Name')
        ->call('updateFieldConfig', 0, 'required', true)
        ->assertSet('schema.0.label', 'Full Name')
        ->assertSet('schema.0.required', true);
});

it('can add field option', function () {
    Livewire::test(FormBuilder::class)
        ->call('addField', 'select')
        ->call('addFieldOption', 0)
        ->call('addFieldOption', 0)
        ->assertCount('schema.0.options', 2);
});

it('can update field option', function () {
    Livewire::test(FormBuilder::class)
        ->call('addField', 'select')
        ->call('addFieldOption', 0)
        ->call('updateFieldOption', 0, 0, 'label', 'Custom Label')
        ->call('updateFieldOption', 0, 0, 'value', 'custom_value')
        ->assertSet('schema.0.options.0.label', 'Custom Label')
        ->assertSet('schema.0.options.0.value', 'custom_value');
});

it('can remove field option', function () {
    Livewire::test(FormBuilder::class)
        ->call('addField', 'select')
        ->call('addFieldOption', 0)
        ->call('addFieldOption', 0)
        ->assertCount('schema.0.options', 2)
        ->call('removeFieldOption', 0, 0)
        ->assertCount('schema.0.options', 1);
});

it('can add condition to field', function () {
    Livewire::test(FormBuilder::class)
        ->call('addField', 'text')
        ->call('addCondition', 0)
        ->assertCount('schema.0.conditions.rules', 1)
        ->assertSet('schema.0.conditions.action', 'show')
        ->assertSet('schema.0.conditions.logic', 'and');
});

it('can remove condition from field', function () {
    Livewire::test(FormBuilder::class)
        ->call('addField', 'text')
        ->call('addCondition', 0)
        ->call('addCondition', 0)
        ->assertCount('schema.0.conditions.rules', 2)
        ->call('removeCondition', 0, 0)
        ->assertCount('schema.0.conditions.rules', 1);
});

it('can save new form', function () {
    Livewire::test(FormBuilder::class)
        ->set('name', 'Contact Form')
        ->set('description', 'Get in touch')
        ->call('addField', 'text')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('flashMessage', 'Form saved successfully!');

    $saved = collect(FakeFormRepository::$forms)->first(fn ($f) => $f->name === 'Contact Form');
    expect($saved)->not->toBeNull();
});

it('can update existing form', function () {
    $repo = app(FormRepositoryContract::class);
    $form = $repo->create(['name' => 'Original Name', 'schema' => []]);

    Livewire::test(FormBuilder::class, ['formId' => $form->id])
        ->set('name', 'Updated Name')
        ->call('save')
        ->assertHasNoErrors();

    expect($repo->findOrFail($form->id)->name)->toBe('Updated Name');
});

it('can export schema as json', function () {
    $component = Livewire::test(FormBuilder::class)
        ->set('name', 'Test Form')
        ->set('description', 'Test')
        ->call('addField', 'text');

    $json = $component->instance()->exportJson();
    $data = json_decode($json, true);

    expect($data)->toHaveKeys(['name', 'description', 'schema'])
        ->and($data['name'])->toBe('Test Form')
        ->and($data['schema'])->toHaveCount(1);
});

it('can import schema from json', function () {
    $json = json_encode([
        'name'        => 'Imported Form',
        'description' => 'From JSON',
        'schema'      => [
            ['type' => 'text',  'key' => 'name',  'label' => 'Name'],
            ['type' => 'email', 'key' => 'email', 'label' => 'Email'],
        ],
    ]);

    Livewire::test(FormBuilder::class)
        ->call('importJson', $json)
        ->assertSet('name', 'Imported Form')
        ->assertSet('description', 'From JSON')
        ->assertCount('schema', 2);
});

it('handles invalid json import gracefully', function () {
    Livewire::test(FormBuilder::class)
        ->call('importJson', 'invalid{json')
        ->assertSet('flashMessage', 'Invalid JSON.')
        ->assertSet('flashType', 'error');
});

it('can reorder fields', function () {
    Livewire::test(FormBuilder::class)
        ->call('addField', 'text')
        ->call('updateFieldConfig', 0, 'label', 'First')
        ->call('addField', 'email')
        ->call('updateFieldConfig', 1, 'label', 'Second')
        ->call('addField', 'number')
        ->call('updateFieldConfig', 2, 'label', 'Third')
        ->tap(function ($component) {
            $keys = [
                $component->get('schema.2.key'),
                $component->get('schema.0.key'),
                $component->get('schema.1.key'),
            ];
            $component->call('reorderFields', $keys);
        })
        ->assertSet('schema.0.label', 'Third')
        ->assertSet('schema.1.label', 'First')
        ->assertSet('schema.2.label', 'Second');
});

it('can add child field to repeater', function () {
    Livewire::test(FormBuilder::class)
        ->call('addField', 'repeater')
        ->call('addChildField', 0, 'text')
        ->call('addChildField', 0, 'email')
        ->assertCount('schema.0.children', 2);
});

it('can delete child field from repeater', function () {
    Livewire::test(FormBuilder::class)
        ->call('addField', 'repeater')
        ->call('addChildField', 0, 'text')
        ->call('addChildField', 0, 'email')
        ->assertCount('schema.0.children', 2)
        ->call('deleteChildField', 0, 0)
        ->assertCount('schema.0.children', 1);
});

it('dispatches form-saved event on save', function () {
    Livewire::test(FormBuilder::class)
        ->set('name', 'Event Test Form')
        ->call('save')
        ->assertDispatched('form-saved');
});

it('dispatches field-added event when adding field', function () {
    Livewire::test(FormBuilder::class)
        ->call('addField', 'text')
        ->assertDispatched('field-added');
});

it('generates unique field keys', function () {
    $component = Livewire::test(FormBuilder::class)
        ->call('addField', 'text')
        ->call('addField', 'text')
        ->call('addField', 'text');

    $key1 = $component->get('schema.0.key');
    $key2 = $component->get('schema.1.key');
    $key3 = $component->get('schema.2.key');

    expect($key1)->not->toBe($key2)
        ->and($key2)->not->toBe($key3)
        ->and($key1)->not->toBe($key3);
});

it('computes palette property correctly', function () {
    $component = Livewire::test(FormBuilder::class);
    $palette = $component->get('palette');

    expect($palette)->toBeArray()
        ->and($palette)->not->toBeEmpty();
});

it('computes selected field property correctly', function () {
    Livewire::test(FormBuilder::class)
        ->call('addField', 'text')
        ->tap(function ($component) {
            // addField auto-selects the new field (selectedFieldIndex = 0)
            expect($component->get('selectedField'))->toBeArray()
                ->and($component->get('selectedField.type'))->toBe('text');
        })
        ->call('selectField', 0)  // toggle: deselects since index 0 is already selected
        ->assertSet('selectedField', null);
});

it('computes field keys property correctly', function () {
    $component = Livewire::test(FormBuilder::class)
        ->call('addField', 'text')
        ->call('updateFieldConfig', 0, 'label', 'Name Field')
        ->call('addField', 'email')
        ->call('updateFieldConfig', 1, 'label', 'Email Field');

    $fieldKeys = $component->get('fieldKeys');

    expect($fieldKeys)->toBeArray()
        ->and($fieldKeys)->toHaveCount(2)
        ->and(array_values($fieldKeys))->toContain('Name Field', 'Email Field');
});
