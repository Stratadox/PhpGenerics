<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Generator\Visitor;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\NodeVisitorAbstract;
use ReflectionClass;

final class ParentBypass extends NodeVisitorAbstract
{
    /** @var ReflectionClass|null */
    private $parent;

    public function __construct(ReflectionClass $genericBase) {
        $parent = $genericBase->getParentClass();
        if ($parent) {
            $this->parent = $parent;
        }
    }

    public function enterNode(Node $node)
    {
        if (null === $this->parent) {
            return;
        }
        if (!$node instanceof StaticCall) {
            return;
        }
        if ((string) $node->class !== 'parent') {
            return;
        }
        $node->class = new Name($this->parent->getShortName());
    }
}
