<?php

namespace Tivents\LivewireFormBuilder\Support;

use InvalidArgumentException;
use Tivents\LivewireFormBuilder\Contracts\FieldTypeContract;

class FieldRegistry
{
    /** @var array<string, class-string<FieldTypeContract>> */
    protected array $types = [];

    public function register(string $type, string $class): void
    {
        if (!is_a($class, FieldTypeContract::class, true)) {
            throw new InvalidArgumentException("[$class] must implement FieldTypeContract.");
        }

        $this->types[$type] = $class;
    }

    public function all(): array
    {
        return $this->types;
    }

    public function get(string $type): string
    {
        if (!isset($this->types[$type])) {
            throw new InvalidArgumentException("Field type [$type] is not registered.");
        }

        return $this->types[$type];
    }

    public function make(string $type, array $config = []): FieldTypeContract
    {
        $class = $this->get($type);
        return new $class($config);
    }

    public function has(string $type): bool
    {
        return isset($this->types[$type]);
    }

    /**
     * Return palette groups: [ 'inputs' => [ ['type'=>'text', 'label'=>'Text', ...], ... ] ]
     */
    public function palette(): array
    {
        $groups = [];

        foreach ($this->types as $type => $class) {
            $group = $class::group();
            $groups[$group][] = [
                'type'          => $type,
                'label'         => $class::label(),
                'icon'          => $class::icon(),
                'defaultConfig' => $class::defaultConfig(),
            ];
        }

        return $groups;
    }
}
