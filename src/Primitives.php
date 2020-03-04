<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics;

use function array_key_exists;

class Primitives
{
    private const TYPES = [
        'string' => 'is_string',
        'bool' => 'is_bool',
        'int' => 'is_int',
        'float' => 'is_float',
        'object' => 'is_object',
        'array' => 'is_array',
        'resource' => 'is_resource',
        'void' => 'is_null',
    ];

    public function includes(string $type): bool
    {
        return array_key_exists($type, self::TYPES);
    }

    public function checkFor(string $type): string
    {
        return self::TYPES[$type];
    }
}
