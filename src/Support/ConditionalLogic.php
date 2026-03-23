<?php

namespace Tivents\LivewireFormBuilder\Support;

/**
 * Evaluates form field visibility based on conditional logic rules.
 *
 * Each condition rule has the shape:
 * [
 *   'field'    => 'field_key',
 *   'operator' => '==' | '!=' | 'contains' | 'not_contains' | '>' | '<' | 'empty' | 'not_empty',
 *   'value'    => mixed,
 * ]
 *
 * A field config may have:
 * 'conditions' => [
 *   'action'   => 'show' | 'hide',
 *   'logic'    => 'and' | 'or',
 *   'rules'    => [ ...rule, ...rule ]
 * ]
 */
class ConditionalLogic
{
    /**
     * Determine if a field should be VISIBLE given current form data.
     */
    public static function isVisible(array $fieldConfig, array $formData): bool
    {
        $conditions = $fieldConfig['conditions'] ?? null;

        if (empty($conditions) || empty($conditions['rules'])) {
            return true;
        }

        $action = $conditions['action'] ?? 'show';
        $logic  = $conditions['logic']  ?? 'and';
        $rules  = $conditions['rules']  ?? [];

        $results = array_map(fn ($rule) => self::evaluate($rule, $formData), $rules);

        $match = $logic === 'or'
            ? in_array(true, $results, true)
            : !in_array(false, $results, true);

        return $action === 'show' ? $match : !$match;
    }

    protected static function evaluate(array $rule, array $formData): bool
    {
        $fieldValue = $formData[$rule['field']] ?? null;
        $ruleValue  = $rule['value'] ?? null;
        $operator   = $rule['operator'] ?? '==';

        return match ($operator) {
            '=='           => $fieldValue == $ruleValue,
            '!='           => $fieldValue != $ruleValue,
            '>'            => is_numeric($fieldValue) && $fieldValue > $ruleValue,
            '<'            => is_numeric($fieldValue) && $fieldValue < $ruleValue,
            '>='           => is_numeric($fieldValue) && $fieldValue >= $ruleValue,
            '<='           => is_numeric($fieldValue) && $fieldValue <= $ruleValue,
            'contains'     => is_string($fieldValue) && str_contains($fieldValue, $ruleValue),
            'not_contains' => is_string($fieldValue) && !str_contains($fieldValue, $ruleValue),
            'empty'        => empty($fieldValue),
            'not_empty'    => !empty($fieldValue),
            'in'           => in_array($fieldValue, (array) $ruleValue),
            'not_in'       => !in_array($fieldValue, (array) $ruleValue),
            default        => false,
        };
    }

    /**
     * Filter a schema to only visible fields given current data.
     * Returns [ 'key' => bool (visible) ] map.
     */
    public static function visibilityMap(array $schema, array $formData): array
    {
        $map = [];
        foreach ($schema as $field) {
            $key = $field['key'] ?? null;
            if ($key) {
                $map[$key] = self::isVisible($field, $formData);
            }
            // Include row children in the visibility map
            if (($field['type'] ?? '') === 'row') {
                foreach ($field['children'] ?? [] as $child) {
                    $ck = $child['key'] ?? null;
                    if ($ck) {
                        $map[$ck] = self::isVisible($child, $formData);
                    }
                }
            }
        }
        return $map;
    }
}
