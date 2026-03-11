<?php

use Tivents\LivewireFormBuilder\Support\ConditionalLogic;

it('returns true when field has no conditions', function () {
    $field = ['key' => 'name', 'type' => 'text'];
    $formData = [];

    expect(ConditionalLogic::isVisible($field, $formData))->toBeTrue();
});

it('returns true when conditions array is empty', function () {
    $field = [
        'key' => 'name',
        'conditions' => ['rules' => []],
    ];
    $formData = [];

    expect(ConditionalLogic::isVisible($field, $formData))->toBeTrue();
});

it('evaluates operator correctly', function () {
    $field = [
        'key' => 'conditional',
        'conditions' => [
            'action' => 'show',
            'logic' => 'and',
            'rules' => [
                ['field' => 'trigger', 'operator' => '==', 'value' => 'yes'],
            ],
        ],
    ];

    expect(ConditionalLogic::isVisible($field, ['trigger' => 'yes']))->toBeTrue();
    expect(ConditionalLogic::isVisible($field, ['trigger' => 'no']))->toBeFalse();
});

it('evaluates unequal operator correctly', function () {
    $field = [
        'key' => 'conditional',
        'conditions' => [
            'action' => 'show',
            'logic' => 'and',
            'rules' => [
                ['field' => 'trigger', 'operator' => '!=', 'value' => 'no'],
            ],
        ],
    ];

    expect(ConditionalLogic::isVisible($field, ['trigger' => 'yes']))->toBeTrue();
    expect(ConditionalLogic::isVisible($field, ['trigger' => 'no']))->toBeFalse();
});

it('evaluates greater operator correctly', function () {
    $field = [
        'key' => 'conditional',
        'conditions' => [
            'action' => 'show',
            'logic' => 'and',
            'rules' => [
                ['field' => 'age', 'operator' => '>', 'value' => 18],
            ],
        ],
    ];

    expect(ConditionalLogic::isVisible($field, ['age' => 25]))->toBeTrue();
    expect(ConditionalLogic::isVisible($field, ['age' => 15]))->toBeFalse();
    expect(ConditionalLogic::isVisible($field, ['age' => 18]))->toBeFalse();
});

it('evaluates smaller operator correctly', function () {
    $field = [
        'key' => 'conditional',
        'conditions' => [
            'action' => 'show',
            'logic' => 'and',
            'rules' => [
                ['field' => 'price', 'operator' => '<', 'value' => 100],
            ],
        ],
    ];

    expect(ConditionalLogic::isVisible($field, ['price' => 50]))->toBeTrue();
    expect(ConditionalLogic::isVisible($field, ['price' => 150]))->toBeFalse();
    expect(ConditionalLogic::isVisible($field, ['price' => 100]))->toBeFalse();
});

it('evaluates greater then operator correctly', function () {
    $field = [
        'key' => 'conditional',
        'conditions' => [
            'action' => 'show',
            'logic' => 'and',
            'rules' => [
                ['field' => 'score', 'operator' => '>=', 'value' => 50],
            ],
        ],
    ];

    expect(ConditionalLogic::isVisible($field, ['score' => 75]))->toBeTrue();
    expect(ConditionalLogic::isVisible($field, ['score' => 50]))->toBeTrue();
    expect(ConditionalLogic::isVisible($field, ['score' => 25]))->toBeFalse();
});

it('evaluates smaller or equal operator correctly', function () {
    $field = [
        'key' => 'conditional',
        'conditions' => [
            'action' => 'show',
            'logic' => 'and',
            'rules' => [
                ['field' => 'limit', 'operator' => '<=', 'value' => 100],
            ],
        ],
    ];

    expect(ConditionalLogic::isVisible($field, ['limit' => 50]))->toBeTrue();
    expect(ConditionalLogic::isVisible($field, ['limit' => 100]))->toBeTrue();
    expect(ConditionalLogic::isVisible($field, ['limit' => 150]))->toBeFalse();
});

it('evaluates contains operator correctly', function () {
    $field = [
        'key' => 'conditional',
        'conditions' => [
            'action' => 'show',
            'logic' => 'and',
            'rules' => [
                ['field' => 'message', 'operator' => 'contains', 'value' => 'hello'],
            ],
        ],
    ];

    expect(ConditionalLogic::isVisible($field, ['message' => 'hello world']))->toBeTrue();
    expect(ConditionalLogic::isVisible($field, ['message' => 'say hello']))->toBeTrue();
    expect(ConditionalLogic::isVisible($field, ['message' => 'goodbye']))->toBeFalse();
});

it('evaluates not_contains operator correctly', function () {
    $field = [
        'key' => 'conditional',
        'conditions' => [
            'action' => 'show',
            'logic' => 'and',
            'rules' => [
                ['field' => 'text', 'operator' => 'not_contains', 'value' => 'spam'],
            ],
        ],
    ];

    expect(ConditionalLogic::isVisible($field, ['text' => 'clean message']))->toBeTrue();
    expect(ConditionalLogic::isVisible($field, ['text' => 'this is spam']))->toBeFalse();
});

it('evaluates empty operator correctly', function () {
    $field = [
        'key' => 'conditional',
        'conditions' => [
            'action' => 'show',
            'logic' => 'and',
            'rules' => [
                ['field' => 'optional', 'operator' => 'empty', 'value' => null],
            ],
        ],
    ];

    expect(ConditionalLogic::isVisible($field, ['optional' => '']))->toBeTrue();
    expect(ConditionalLogic::isVisible($field, ['optional' => null]))->toBeTrue();
    expect(ConditionalLogic::isVisible($field, []))->toBeTrue();
    expect(ConditionalLogic::isVisible($field, ['optional' => 'value']))->toBeFalse();
});

it('evaluates not_empty operator correctly', function () {
    $field = [
        'key' => 'conditional',
        'conditions' => [
            'action' => 'show',
            'logic' => 'and',
            'rules' => [
                ['field' => 'required', 'operator' => 'not_empty', 'value' => null],
            ],
        ],
    ];

    expect(ConditionalLogic::isVisible($field, ['required' => 'value']))->toBeTrue();
    expect(ConditionalLogic::isVisible($field, ['required' => '']))->toBeFalse();
    expect(ConditionalLogic::isVisible($field, []))->toBeFalse();
});

it('evaluates in operator correctly', function () {
    $field = [
        'key' => 'conditional',
        'conditions' => [
            'action' => 'show',
            'logic' => 'and',
            'rules' => [
                ['field' => 'country', 'operator' => 'in', 'value' => ['US', 'UK', 'CA']],
            ],
        ],
    ];

    expect(ConditionalLogic::isVisible($field, ['country' => 'US']))->toBeTrue();
    expect(ConditionalLogic::isVisible($field, ['country' => 'UK']))->toBeTrue();
    expect(ConditionalLogic::isVisible($field, ['country' => 'DE']))->toBeFalse();
});

it('evaluates not_in operator correctly', function () {
    $field = [
        'key' => 'conditional',
        'conditions' => [
            'action' => 'show',
            'logic' => 'and',
            'rules' => [
                ['field' => 'status', 'operator' => 'not_in', 'value' => ['banned', 'suspended']],
            ],
        ],
    ];

    expect(ConditionalLogic::isVisible($field, ['status' => 'active']))->toBeTrue();
    expect(ConditionalLogic::isVisible($field, ['status' => 'banned']))->toBeFalse();
    expect(ConditionalLogic::isVisible($field, ['status' => 'suspended']))->toBeFalse();
});

it('handles AND logic with multiple rules', function () {
    $field = [
        'key' => 'conditional',
        'conditions' => [
            'action' => 'show',
            'logic' => 'and',
            'rules' => [
                ['field' => 'age', 'operator' => '>=', 'value' => 18],
                ['field' => 'country', 'operator' => '==', 'value' => 'US'],
            ],
        ],
    ];

    expect(ConditionalLogic::isVisible($field, ['age' => 25, 'country' => 'US']))->toBeTrue();
    expect(ConditionalLogic::isVisible($field, ['age' => 15, 'country' => 'US']))->toBeFalse();
    expect(ConditionalLogic::isVisible($field, ['age' => 25, 'country' => 'UK']))->toBeFalse();
    expect(ConditionalLogic::isVisible($field, ['age' => 15, 'country' => 'UK']))->toBeFalse();
});

it('handles OR logic with multiple rules', function () {
    $field = [
        'key' => 'conditional',
        'conditions' => [
            'action' => 'show',
            'logic' => 'or',
            'rules' => [
                ['field' => 'role', 'operator' => '==', 'value' => 'admin'],
                ['field' => 'role', 'operator' => '==', 'value' => 'moderator'],
            ],
        ],
    ];

    expect(ConditionalLogic::isVisible($field, ['role' => 'admin']))->toBeTrue();
    expect(ConditionalLogic::isVisible($field, ['role' => 'moderator']))->toBeTrue();
    expect(ConditionalLogic::isVisible($field, ['role' => 'user']))->toBeFalse();
});

it('handles hide action correctly', function () {
    $field = [
        'key' => 'conditional',
        'conditions' => [
            'action' => 'hide',
            'logic' => 'and',
            'rules' => [
                ['field' => 'trigger', 'operator' => '==', 'value' => 'hide_me'],
            ],
        ],
    ];

    expect(ConditionalLogic::isVisible($field, ['trigger' => 'hide_me']))->toBeFalse();
    expect(ConditionalLogic::isVisible($field, ['trigger' => 'show_me']))->toBeTrue();
});

it('creates visibility map for schema', function () {
    $schema = [
        ['key' => 'always_visible', 'type' => 'text'],
        [
            'key' => 'conditional',
            'type' => 'text',
            'conditions' => [
                'action' => 'show',
                'logic' => 'and',
                'rules' => [
                    ['field' => 'trigger', 'operator' => '==', 'value' => 'yes'],
                ],
            ],
        ],
        [
            'key' => 'hidden',
            'type' => 'text',
            'conditions' => [
                'action' => 'hide',
                'logic' => 'and',
                'rules' => [
                    ['field' => 'trigger', 'operator' => '==', 'value' => 'yes'],
                ],
            ],
        ],
    ];

    $map = ConditionalLogic::visibilityMap($schema, ['trigger' => 'yes']);

    expect($map)->toHaveKeys(['always_visible', 'conditional', 'hidden'])
        ->and($map['always_visible'])->toBeTrue()
        ->and($map['conditional'])->toBeTrue()
        ->and($map['hidden'])->toBeFalse();
});

it('ignores fields without keys in visibility map', function () {
    $schema = [
        ['type' => 'heading', 'label' => 'Section'],
        ['key' => 'name', 'type' => 'text'],
    ];

    $map = ConditionalLogic::visibilityMap($schema, []);

    expect($map)->toHaveKey('name')
        ->and($map)->not->toHaveKey('');
});

it('handles missing field in form data', function () {
    $field = [
        'key' => 'conditional',
        'conditions' => [
            'action' => 'show',
            'logic' => 'and',
            'rules' => [
                ['field' => 'nonexistent', 'operator' => '==', 'value' => 'yes'],
            ],
        ],
    ];

    expect(ConditionalLogic::isVisible($field, []))->toBeFalse();
});

it('handles unknown operator gracefully', function () {
    $field = [
        'key' => 'conditional',
        'conditions' => [
            'action' => 'show',
            'logic' => 'and',
            'rules' => [
                ['field' => 'trigger', 'operator' => 'unknown_op', 'value' => 'yes'],
            ],
        ],
    ];

    expect(ConditionalLogic::isVisible($field, ['trigger' => 'yes']))->toBeFalse();
});

it('handles comparison operators with non-numeric values', function () {
    $field = [
        'key' => 'conditional',
        'conditions' => [
            'action' => 'show',
            'logic' => 'and',
            'rules' => [
                ['field' => 'text', 'operator' => '>', 'value' => 10],
            ],
        ],
    ];

    expect(ConditionalLogic::isVisible($field, ['text' => 'not a number']))->toBeFalse();
});

it('handles contains operator with non-string values', function () {
    $field = [
        'key' => 'conditional',
        'conditions' => [
            'action' => 'show',
            'logic' => 'and',
            'rules' => [
                ['field' => 'number', 'operator' => 'contains', 'value' => '5'],
            ],
        ],
    ];

    expect(ConditionalLogic::isVisible($field, ['number' => 12345]))->toBeFalse();
});

it('handles complex multi-rule AND logic', function () {
    $field = [
        'key' => 'conditional',
        'conditions' => [
            'action' => 'show',
            'logic' => 'and',
            'rules' => [
                ['field' => 'age', 'operator' => '>=', 'value' => 18],
                ['field' => 'consent', 'operator' => '==', 'value' => true],
                ['field' => 'email', 'operator' => 'not_empty', 'value' => null],
            ],
        ],
    ];

    expect(ConditionalLogic::isVisible($field, [
        'age' => 25,
        'consent' => true,
        'email' => 'test@example.com',
    ]))->toBeTrue();

    expect(ConditionalLogic::isVisible($field, [
        'age' => 25,
        'consent' => false,
        'email' => 'test@example.com',
    ]))->toBeFalse();
});

it('handles complex multi-rule OR logic', function () {
    $field = [
        'key' => 'conditional',
        'conditions' => [
            'action' => 'show',
            'logic' => 'or',
            'rules' => [
                ['field' => 'is_admin', 'operator' => '==', 'value' => true],
                ['field' => 'is_moderator', 'operator' => '==', 'value' => true],
                ['field' => 'is_vip', 'operator' => '==', 'value' => true],
            ],
        ],
    ];

    expect(ConditionalLogic::isVisible($field, [
        'is_admin' => false,
        'is_moderator' => false,
        'is_vip' => true,
    ]))->toBeTrue();

    expect(ConditionalLogic::isVisible($field, [
        'is_admin' => false,
        'is_moderator' => false,
        'is_vip' => false,
    ]))->toBeFalse();
});
