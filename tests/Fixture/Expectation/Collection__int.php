<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Test;

use ArrayObject;
use Stratadox\PhpGenerics\Annotation\ReturnType;
use Stratadox\PhpGenerics\Test\Fixture\Collection;
use TypeError;
use function gettype;
use function is_int;
use function sprintf;

final class Collection__int extends Collection
{
    public function __construct(int ...$items)
    {
        ArrayObject::__construct($items);
    }

    /**
     * @ReturnType(force = "T")
     * @param int $offset
     * @return int
     */
    public function offsetGet($offset): int
    {
        if (!is_int($offset)) {
            $this->throw($offset, 'int', __METHOD__, 0);
        }
        return ArrayObject::offsetGet($offset);
    }

    /**
     * @param int $offset
     * @param int $value
     */
    public function offsetSet($offset, $value): void
    {
        if (null !== $offset && !is_int($offset)) {
            $this->throw($offset, 'int or null', __METHOD__, 0);
        }
        if (!is_int($value)) {
            $this->throw($value, 'int', __METHOD__, 1);
        }
        ArrayObject::offsetSet($offset, $value);
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
