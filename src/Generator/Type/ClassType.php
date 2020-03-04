<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Generator\Type;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use ReflectionClass;
use ReflectionException;
use function class_exists;

final class ClassType implements TypeArgument
{
    /** @var ReflectionClass */
    private $reflection;

    public function __construct(ReflectionClass $reflection)
    {
        $this->reflection = $reflection;
    }

    /** @throws ReflectionException */
    public static function fromFullName(string $fqcn): self
    {
        return new self(new ReflectionClass($fqcn));
    }

    public function typeCheck(Expr $expr): Node
    {
        return new Instanceof_($expr, $this->nameNode());
    }

    public function typeFetch(): Node
    {
        return new ClassConstFetch($this->nameNode(), new Identifier('class'));
    }

    public function requiresImport(): bool
    {
        return true;
    }

    public function fullName(): string
    {
        return $this->reflection->getName();
    }

    public function __toString(): string
    {
        return $this->reflection->getShortName();
    }

    private function nameNode(): Name
    {
        return new Name((string) $this);
    }
}
