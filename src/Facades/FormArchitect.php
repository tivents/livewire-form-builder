<?php

namespace Tivents\LivewireFormBuilder\Facades;

use Illuminate\Support\Facades\Facade;
use Tivents\LivewireFormBuilder\Support\FieldRegistry;

/**
 * @method static void register(string $type, string $class)
 * @method static array all()
 * @method static \Tivents\LivewireFormBuilder\Contracts\FieldTypeContract make(string $type, array $config = [])
 */
class LivewireFormBuilder extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return FieldRegistry::class;
    }
}
