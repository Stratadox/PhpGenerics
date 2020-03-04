<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Test\Fixture;

use ArrayObject;
use Stratadox\PhpGenerics\Annotation\Generic;
use Stratadox\PhpGenerics\Annotation\ReturnType;
use Stratadox\PhpGenerics\Generic\K;
use Stratadox\PhpGenerics\Generic\T;
use TypeError;
use function gettype;
use function is_object;
use function spl_object_hash;
use function sprintf;

/** @Generic(count = 2) */
class Map extends ArrayObject
{
    /**
     * @ReturnType(force = "T")
     * @param K $offset
     * @return T
     */
    public function offsetGet($offset)
    {
        if (!$offset instanceof K) {
            $this->throw($offset, K::class, __METHOD__, 0);
        }
        return parent::offsetGet($this->hash($offset));
    }

    /**
     * @param K $offset
     * @param T $value
     */
    public function offsetSet($offset, $value): void
    {
        if (!$offset instanceof K) {
            $this->throw($offset, K::class, __METHOD__, 0);
        }
        if (!$value instanceof T) {
            $this->throw($value, T::class, __METHOD__, 1);
        }
        parent::offsetSet($this->hash($offset), $value);
    }

    protected function throw($value, string $type, string $source, int $argument): void
    {
        throw new TypeError(sprintf(
            'Argument %d passed to %s must be of type %s, %s given',
            $argument,
            $source,
            $type,
            gettype($value)
        ));
    }

    protected function hash($offset): string
    {
        return is_object($offset) ? spl_object_hash($offset) : (string) $offset;
    }
}
