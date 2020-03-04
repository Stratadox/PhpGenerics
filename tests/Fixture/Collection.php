<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Test\Fixture;

use ArrayObject;
use Stratadox\PhpGenerics\Annotation\Generic;
use Stratadox\PhpGenerics\Annotation\ReturnType;
use Stratadox\PhpGenerics\Generic\T;
use TypeError;
use function gettype;
use function is_int;
use function sprintf;

/** @Generic(count = 1) */
class Collection extends ArrayObject
{
    public function __construct(T ...$items)
    {
        parent::__construct($items);
    }

    /**
     * @ReturnType(force = "T")
     * @param int $offset
     * @return T
     */
    public function offsetGet($offset)
    {
        if (!is_int($offset)) {
            $this->throw($offset, 'int', __METHOD__, 0);
        }
        return parent::offsetGet($offset);
    }

    /**
     * @param int $offset
     * @param T $value
     */
    public function offsetSet($offset, $value): void
    {
        if (null !== $offset && !is_int($offset)) {
            $this->throw($offset, 'int or null', __METHOD__, 0);
        }
        if (!$value instanceof T) {
            $this->throw($value, T::class, __METHOD__, 1);
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
