<?php
declare(strict_types=1);

namespace Stratadox\PhpGenerics\Generator;

use Stratadox\PhpGenerics\Generator\Type\TypeArguments;

interface SourceGenerator
{
    /**
     * Generates a concrete class, inheriting from the generic base class.
     *
     * @param string $namespace            The namespace to use for this class
     * @param string $newClassName         Short/unqualified new class name
     * @param string $genericBaseClass     Fully qualified generic class name
     * @param TypeArguments $typeArguments Replacements for the type parameters
     * @return string                      Contents of the new concrete class
     */
    public function generate(
        string $namespace,
        string $newClassName,
        string $genericBaseClass,
        TypeArguments $typeArguments
    ): string;
}
