<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Test;

use ArrayObject;
use Stratadox\PhpGenerics\Annotation\ReturnType;
use Stratadox\PhpGenerics\Test\Fixture\Map;
use Stratadox\PhpGenerics\Test\Fixture\Thing;
use TypeError;
use function gettype;
use function is_object;
use function spl_object_hash;
use function sprintf;

final class Map__Thing__Map__Thing__Thing extends Map
{
    /**
     * @ReturnType(force = "T")
     * @param Thing $offset
     * @return Map__Thing__Thing
     */
    public function offsetGet($offset): Map__Thing__Thing
    {
        if (!$offset instanceof Thing) {
            $this->throw($offset, Thing::class, __METHOD__, 0);
        }
        return ArrayObject::offsetGet($this->hash($offset));
    }

    /**
     * @param Thing $offset
     * @param Map__Thing__Thing $value
     */
    public function offsetSet($offset, $value): void
    {
        if (!$offset instanceof Thing) {
            $this->throw($offset, Thing::class, __METHOD__, 0);
        }
        if (!$value instanceof Map__Thing__Thing) {
            $this->throw($value, Map__Thing__Thing::class, __METHOD__, 1);
        }
        ArrayObject::offsetSet($this->hash($offset), $value);
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
