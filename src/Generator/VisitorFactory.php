<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Generator;

use PhpParser\NodeVisitor;
use ReflectionClass;
use Stratadox\PhpGenerics\Generator\Type\TypeArguments;

interface VisitorFactory
{
    /** @return NodeVisitor[] */
    public function replacing(
        string $namespace,
        string $newClassName,
        ReflectionClass $genericBase,
        TypeArguments $typeArguments
    ): array;
}

