<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Dissector\Error;

use TypeError;
use function sprintf;

final class BaseClassIsNotGeneric extends TypeError implements InvalidTypeArgument
{
    public static function cannotUse(string $class): InvalidTypeArgument
    {
        return new self(sprintf('%s is not a generic base class.', $class));
    }
}
