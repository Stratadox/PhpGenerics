<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Dissector\Error;

use TypeError;
use function sprintf;

final class TypeArgumentNotFound extends TypeError implements InvalidTypeArgument
{
    public static function for(
        string $class,
        string $genericBaseClass,
        int $typeParameter
    ): InvalidTypeArgument {
        return new self(sprintf(
            'Could not find the class %s, type argument %d for generic class %s.',
            $class,
            $typeParameter,
            $genericBaseClass
        ));
    }
}
