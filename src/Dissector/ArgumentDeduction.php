<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Dissector;

use Stratadox\PhpGenerics\Dissector\Error\InvalidTypeArgument;

interface ArgumentDeduction
{
    /** @throws InvalidTypeArgument */
    public function for(
        string $namespace,
        string $baseClass,
        string $callingFile,
        string ...$types
    ): array;
}
