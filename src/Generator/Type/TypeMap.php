<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Generator\Type;

use ArrayObject;
use TypeError;
use function gettype;
use function is_string;
use function sprintf;

final class TypeMap extends ArrayObject
{
    /**
     * @param string $offset
     * @return TypeArgument
     */
    public function offsetGet($offset): TypeArgument
    {
        if (!is_string($offset)) {
            $this->throw($offset, 'string', __METHOD__, 0);
        }
        return parent::offsetGet($offset);
    }

    /**
     * @param string $offset
     * @param TypeArgument $value
     */
    public function offsetSet($offset, $value): void
    {
        if (!is_string($offset)) {
            $this->throw($offset, 'string', __METHOD__, 0);
        }
        if (!$value instanceof TypeArgument) {
            $this->throw($value, TypeArgument::class, __METHOD__, 1);
        }
        parent::offsetSet($offset, $value);
    }

    private function throw($value, string $type, string $source, int $argument): void
    {
        throw new TypeError(sprintf(
            'Argument %d passed to %s must be of type %s, %s given',
            $argument,
            $source,
            $type,
            gettype($value)
        ));
    }
}
