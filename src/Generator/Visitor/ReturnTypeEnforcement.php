<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Generator\Visitor;

use Doctrine\Common\Annotations\Reader;
use LogicException;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitorAbstract;
use ReflectionClass;
use ReflectionException;
use Stratadox\PhpGenerics\Annotation\ReturnType;
use Stratadox\PhpGenerics\Generator\Type\TypeArgument;
use Stratadox\PhpGenerics\Generator\Type\TypeMap;

final class ReturnTypeEnforcement extends NodeVisitorAbstract
{
    /** @var TypeMap|TypeArgument[] */
    private $types;
    /** @var Reader */
    private $annotation;
    /** @var ReflectionClass */
    private $reflection;

    public function __construct(
        TypeMap $types,
        Reader $annotation,
        ReflectionClass $genericBase
    ) {
        $this->types = $types;
        $this->annotation = $annotation;
        $this->reflection = $genericBase;
    }

    public function leaveNode(Node $node)
    {
        if (!$node instanceof ClassMethod) {
            return;
        }
        foreach ($this->annotationsOf($node) as $annotation) {
            if ($annotation instanceof ReturnType && $annotation->force) {
                $node->returnType = $this->returnTypeFor($annotation);
            }
        }
    }

    private function annotationsOf(ClassMethod $method): array
    {
        try {
            return $this->annotation->getMethodAnnotations(
                $this->reflection->getMethod((string) $method->name)
            );
        } catch (ReflectionException $e) {
            throw new LogicException(
                'Somehow the php parser and reflection disagree. You must be ' .
                'running insanely outdated software for this to ever happen. ' .
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    private function returnTypeFor(ReturnType $type): Identifier
    {
        return new Identifier((string) $this->types[$type->force]);
    }
}
