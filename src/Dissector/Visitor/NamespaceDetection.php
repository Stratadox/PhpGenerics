<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Dissector\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

final class NamespaceDetection extends NodeVisitorAbstract
{
    private $namespace = '';

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->namespace = (string) $node->name;
        }
    }

    public function namespace(): string
    {
        return $this->namespace;
    }
}
