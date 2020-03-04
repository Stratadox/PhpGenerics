<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Generator\Type;

use Stratadox\ImmutableCollection\ImmutableCollection;

final class TypeArguments extends ImmutableCollection
{
    public function __construct(TypeArgument ...$arguments)
    {
        parent::__construct(...$arguments);
    }

    public function current(): TypeArgument
    {
        return parent::current();
    }
}
