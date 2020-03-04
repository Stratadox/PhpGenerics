<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Generator;

use ReflectionException;
use Stratadox\PhpGenerics\Generator\Type\BuiltInType;
use Stratadox\PhpGenerics\Generator\Type\ClassType;
use Stratadox\PhpGenerics\Generator\Type\TypeArgument;
use Stratadox\PhpGenerics\Generator\Type\TypeArguments;
use Stratadox\PhpGenerics\Primitives;
use function array_map;

class TypeArgumentFactory
{
    /** @var Primitives */
    private $primitives;

    public function __construct(Primitives $primitives = null)
    {
        $this->primitives = $primitives ?: new Primitives();
    }

    public function for(string ...$types): TypeArguments
    {
        return new TypeArguments(...array_map([$this, 'argument'], $types));
    }

    /** @throws ReflectionException */
    private function argument(string $type): TypeArgument
    {
        if ($this->primitives->includes($type)) {
            return BuiltInType::$type();
        }
        return ClassType::fromFullName($type);
    }
}
