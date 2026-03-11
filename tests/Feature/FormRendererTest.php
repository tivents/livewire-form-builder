<?php

use Livewire\Livewire;
use Tivents\LivewireFormBuilder\Components\FormRenderer;
use Tivents\LivewireFormBuilder\Contracts\FormRepositoryContract;
use Tivents\LivewireFormBuilder\Tests\Fakes\FakeFormRepository;

it('can mount with form id', function () {
    $form = app(FormRepositoryContract::class)->create([
        'name'   => 'Contact Form',
        'schema' => [
            ['type' => 'text',  'key' => 'name',  'label' => 'Name',  'required' => true],
            ['type' => 'email', 'key' => 'email', 'label' => 'Email'],
        ],
    ]);

    Livewire::test(FormRenderer::class, ['formId' => $form->id])
        ->assertSet('formId', $form->id)
        ->assertSet('formName', 'Contact Form')
        ->assertCount('schema', 2);
});

it('can mount with schema array', function () {
    $schema = [
        ['type' => 'text',  'key' => 'name',  'label' => 'Name'],
        ['type' => 'email', 'key' => 'email', 'label' => 'Email'],
    ];

    Livewire::test(FormRenderer::class, ['schema' => $schema])
        ->assertSet('formId', null)
        ->assertCount('schema', 2);
});

it('initializes form data with default values', function () {
    $schema = [
        ['type' => 'text',     'key' => 'name',      'label' => 'Name',      'default' => 'John'],
        ['type' => 'checkbox', 'key' => 'subscribe',  'label' => 'Subscribe', 'default' => true],
    ];

    Livewire::test(FormRenderer::class, ['schema' => $schema])
        ->assertSet('formData.name', 'John')
        ->assertSet('formData.subscribe', true);
});

it('can submit valid form', function () {
    $form = app(FormRepositoryContract::class)->create([
        'name'   => 'Contact Form',
        'schema' => [
            ['type' => 'text',  'key' => 'name',  'label' => 'Name',  'required' => true],
            ['type' => 'email', 'key' => 'email', 'label' => 'Email', 'required' => true],
        ],
    ]);

    Livewire::test(FormRenderer::class, ['formId' => $form->id])
        ->set('formData.name', 'John Doe')
        ->set('formData.email', 'john@example.com')
        ->call('submit')
        ->assertSet('submitted', true)
        ->assertHasNoErrors();

    $count = collect(FakeFormRepository::$submissions)
        ->where('form_id', $form->id)
        ->count();

    expect($count)->toBe(1);
});

it('validates required fields on submit', function () {
    $schema = [
        ['type' => 'text',  'key' => 'name',  'label' => 'Name',  'required' => true],
        ['type' => 'email', 'key' => 'email', 'label' => 'Email', 'required' => true],
    ];

    Livewire::test(FormRenderer::class, ['schema' => $schema])
        ->set('formData.name', '')
        ->set('formData.email', '')
        ->call('submit')
        ->assertSet('submitted', false);

    $component = Livewire::test(FormRenderer::class, ['schema' => $schema])
        ->set('formData.name', '')
        ->set('formData.email', '')
        ->call('submit');

    expect($component->get('validationErrors'))->not->toBeEmpty();
});

it('validates email format', function () {
    $schema = [
        ['type' => 'email', 'key' => 'email', 'label' => 'Email', 'required' => true],
    ];

    $component = Livewire::test(FormRenderer::class, ['schema' => $schema])
        ->set('formData.email', 'invalid-email')
        ->call('submit');

    expect($component->get('validationErrors'))->toHaveKey('email');
});

it('skips validation for hidden fields', function () {
    $schema = [
        ['type' => 'text', 'key' => 'trigger', 'label' => 'Trigger'],
        [
            'type'       => 'text',
            'key'        => 'conditional',
            'label'      => 'Conditional Field',
            'required'   => true,
            'conditions' => [
                'action' => 'show',
                'logic'  => 'and',
                'rules'  => [
                    ['field' => 'trigger', 'operator' => '==', 'value' => 'show'],
                ],
            ],
        ],
    ];

    Livewire::test(FormRenderer::class, ['schema' => $schema])
        ->set('formData.trigger', 'hide')
        ->set('formData.conditional', '')
        ->call('submit')
        ->assertSet('submitted', true);
});

it('validates visible conditional fields', function () {
    $schema = [
        ['type' => 'text', 'key' => 'trigger', 'label' => 'Trigger'],
        [
            'type'       => 'text',
            'key'        => 'conditional',
            'label'      => 'Conditional Field',
            'required'   => true,
            'conditions' => [
                'action' => 'show',
                'logic'  => 'and',
                'rules'  => [
                    ['field' => 'trigger', 'operator' => '==', 'value' => 'show'],
                ],
            ],
        ],
    ];

    $component = Livewire::test(FormRenderer::class, ['schema' => $schema])
        ->set('formData.trigger', 'show')
        ->set('formData.conditional', '')
        ->call('submit');

    expect($component->get('validationErrors'))->toHaveKey('conditional');
});

it('performs real-time validation on field update', function () {
    $schema = [
        ['type' => 'email', 'key' => 'email', 'label' => 'Email', 'required' => true],
    ];

    $component = Livewire::test(FormRenderer::class, ['schema' => $schema])
        ->set('formData.email', 'invalid-email');

    expect($component->get('validationErrors'))->toHaveKey('email');

    $component->set('formData.email', 'valid@example.com');

    expect($component->get('validationErrors'))->not->toHaveKey('email');
});

it('dispatches form-submitted event on successful submit', function () {
    $form = app(FormRepositoryContract::class)->create([
        'name'   => 'Contact Form',
        'schema' => [
            ['type' => 'text', 'key' => 'name', 'label' => 'Name'],
        ],
    ]);

    Livewire::test(FormRenderer::class, ['formId' => $form->id])
        ->set('formData.name', 'John')
        ->call('submit')
        ->assertDispatched('form-submitted');
});

it('stores submission with ip and user agent', function () {
    $form = app(FormRepositoryContract::class)->create([
        'name'   => 'Contact Form',
        'schema' => [
            ['type' => 'text', 'key' => 'name', 'label' => 'Name'],
        ],
    ]);

    Livewire::test(FormRenderer::class, ['formId' => $form->id])
        ->set('formData.name', 'John')
        ->call('submit');

    $submission = collect(FakeFormRepository::$submissions)
        ->first(fn ($s) => $s->form_id === $form->id);

    expect($submission)->not->toBeNull()
        ->and($submission->data)->toHaveKey('name')
        ->and($submission->data['name'])->toBe('John');
});

it('computes visibility map correctly', function () {
    $schema = [
        ['type' => 'text', 'key' => 'trigger', 'label' => 'Trigger'],
        [
            'type'       => 'text',
            'key'        => 'conditional',
            'label'      => 'Conditional',
            'conditions' => [
                'action' => 'show',
                'logic'  => 'and',
                'rules'  => [
                    ['field' => 'trigger', 'operator' => '==', 'value' => 'show'],
                ],
            ],
        ],
    ];

    $component = Livewire::test(FormRenderer::class, ['schema' => $schema])
        ->set('formData.trigger', 'hide');

    expect($component->get('visibilityMap.trigger'))->toBeTrue()
        ->and($component->get('visibilityMap.conditional'))->toBeFalse();

    $component->set('formData.trigger', 'show');

    expect($component->get('visibilityMap.conditional'))->toBeTrue();
});

it('validates repeater field children', function () {
    $schema = [
        [
            'type'     => 'repeater',
            'key'      => 'items',
            'label'    => 'Items',
            'children' => [
                ['type' => 'text',  'key' => 'name',  'label' => 'Name',  'required' => true],
                ['type' => 'email', 'key' => 'email', 'label' => 'Email', 'required' => true],
            ],
        ],
    ];

    $component = Livewire::test(FormRenderer::class, ['schema' => $schema])
        ->set('formData.items', [
            ['name' => 'John', 'email' => 'invalid-email'],
            ['name' => '',     'email' => 'jane@example.com'],
        ])
        ->call('submit');

    expect($component->get('validationErrors'))->not->toBeEmpty();
});

it('handles form without submission storage when formId is null', function () {
    $schema = [
        ['type' => 'text', 'key' => 'name', 'label' => 'Name'],
    ];

    Livewire::test(FormRenderer::class, ['schema' => $schema])
        ->set('formData.name', 'John')
        ->call('submit')
        ->assertSet('submitted', true);

    expect(FakeFormRepository::$submissions)->toBeEmpty();
});

it('displays success message after submission', function () {
    $schema = [
        ['type' => 'text', 'key' => 'name', 'label' => 'Name'],
    ];

    Livewire::test(FormRenderer::class, ['schema' => $schema])
        ->set('formData.name', 'John')
        ->call('submit')
        ->assertSet('submitted', true)
        ->assertSet('successMessage', 'Thank you! Your response has been recorded.');
});

it('handles multiple field types correctly', function () {
    $schema = [
        ['type' => 'text',     'key' => 'text_field',     'label' => 'Text'],
        ['type' => 'number',   'key' => 'number_field',   'label' => 'Number'],
        ['type' => 'checkbox', 'key' => 'checkbox_field', 'label' => 'Checkbox'],
        ['type' => 'select',   'key' => 'select_field',   'label' => 'Select'],
    ];

    Livewire::test(FormRenderer::class, ['schema' => $schema])
        ->set('formData.text_field', 'Test')
        ->set('formData.number_field', 42)
        ->set('formData.checkbox_field', true)
        ->set('formData.select_field', 'option1')
        ->call('submit')
        ->assertSet('submitted', true);
});

it('clears validation errors when field becomes valid', function () {
    $schema = [
        ['type' => 'email', 'key' => 'email', 'label' => 'Email', 'required' => true],
    ];

    $component = Livewire::test(FormRenderer::class, ['schema' => $schema])
        ->set('formData.email', 'invalid');

    expect($component->get('validationErrors'))->toHaveKey('email');

    $component->set('formData.email', 'valid@example.com');

    expect($component->get('validationErrors.email'))->toBeEmpty();
});

it('handles empty schema gracefully', function () {
    Livewire::test(FormRenderer::class, ['schema' => []])
        ->call('submit')
        ->assertSet('submitted', true);
});

it('handles fields without keys gracefully', function () {
    $schema = [
        ['type' => 'heading', 'label' => 'Section Title'],
        ['type' => 'text',    'key'   => 'name', 'label' => 'Name'],
    ];

    Livewire::test(FormRenderer::class, ['schema' => $schema])
        ->set('formData.name', 'John')
        ->call('submit')
        ->assertSet('submitted', true);
});
