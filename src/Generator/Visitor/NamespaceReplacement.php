<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Generator\Visitor;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeVisitorAbstract;

final class NamespaceReplacement extends NodeVisitorAbstract
{
    /** @var string */
    private $namespace;

    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    public function enterNode(Node $node)
    {
        if (!$node instanceof Namespace_) {
            return null;
        }
        return new Namespace_(
            new Name($this->namespace),
            $node->stmts,
            $node->getAttributes()
        );
    }
}
