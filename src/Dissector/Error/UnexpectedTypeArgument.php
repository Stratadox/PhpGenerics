<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Dissector\Error;

use TypeError;
use function sprintf;

final class UnexpectedTypeArgument extends TypeError implements InvalidTypeArgument
{
    public static function for(
        string $genericBaseClass,
        int $typeParameter
    ): InvalidTypeArgument {
        return new self(sprintf(
            'Unexpected type argument %d for generic class %s.',
            $typeParameter,
            $genericBaseClass
        ));
    }
}
