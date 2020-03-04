<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Generator;

use LogicException;
use ReflectionClass;
use ReflectionException;
use function file_get_contents;
use function sprintf;

class SourceExtractor
{
    public function contentsOf(string $class): string
    {
        return file_get_contents($this->reflectionOf($class)->getFileName());
    }

    public function reflectionOf(string $class): ReflectionClass
    {
        try {
            return new ReflectionClass($class);
        } catch (ReflectionException $e) {
            throw new LogicException(
                sprintf('Class `%s` not found.', $class),
                $e->getCode(),
                $e
            );
        }
    }
}
