<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Generator\Visitor;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitorAbstract;

final class ClassNameReplacement extends NodeVisitorAbstract
{
    /** @var string */
    private $newName;

    public function __construct(string $newName)
    {
        $this->newName = $newName;
    }

    public function enterNode(Node $node)
    {
        if (!$node instanceof Class_) {
            return null;
        }
        return new Class_(
            $this->newName,
            [
                'flags' => Class_::MODIFIER_FINAL,
                'extends' => new Name($node->name->toString()),
                'implements' => $node->implements,
                'stmts' => $node->stmts,
            ],
            ['comments' => []] + $node->getAttributes()
        );
    }
}
