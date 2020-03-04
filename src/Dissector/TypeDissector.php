<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Dissector;

use Stratadox\PhpGenerics\Dissector\Error\InvalidTypeArgument;

interface TypeDissector
{
    /** @throws InvalidTypeArgument */
    public function typesToGenerate(
        string $class,
        int $levelsAway = 0
    ): TypeInformation;

    /** @throws InvalidTypeArgument */
    public function typesFromFile(string $class, string $file): TypeInformation;
}
