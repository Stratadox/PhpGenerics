<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Dissector\Error;

use TypeError;
use function sprintf;

final class MissingTypeArgument extends TypeError implements InvalidTypeArgument
{
    public static function for(
        string $genericBaseClass,
        int $typeParameter
    ): InvalidTypeArgument {
        return new self(sprintf(
            'Missing type argument for type parameter %d of generic class %s.',
            $typeParameter,
            $genericBaseClass
        ));
    }
}
