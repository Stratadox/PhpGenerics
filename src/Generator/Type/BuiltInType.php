<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Generator\Type;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use Stratadox\PhpGenerics\Primitives;

/**
 * @method static TypeArgument string()
 * @method static TypeArgument bool()
 * @method static TypeArgument int()
 * @method static TypeArgument float()
 * @method static TypeArgument object()
 * @method static TypeArgument array()
 * @method static TypeArgument resource()
 * @method static TypeArgument void()
 */
final class BuiltInType implements TypeArgument
{
    /** @var string */
    private $type;
    /** @var Name */
    private $typeCheck;
    /** @var Primitives */
    private static $primitives;

    public function __construct(string $type, Name $typeCheck)
    {
        $this->type = $type;
        $this->typeCheck = $typeCheck;
    }

    public static function __callStatic($name, $arguments)
    {
        return new self($name, new Name(self::check($name)));
    }

    private static function check(string $type): string
    {
        if (null === self::$primitives) {
            self::$primitives = new Primitives();
        }
        return self::$primitives->checkFor($type);
    }

    public function typeCheck(Expr $expr): Node
    {
        return new FuncCall($this->typeCheck, [new Arg($expr)]);
    }

    public function typeFetch(): Node
    {
        return new String_($this->type);
    }

    public function requiresImport(): bool
    {
        return false;
    }

    public function fullName(): string
    {
        return $this->type;
    }

    public function __toString(): string
    {
        return $this->type;
    }
}
