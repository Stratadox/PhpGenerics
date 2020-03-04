<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Dissector\Visitor;

use PhpParser\Node;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitorAbstract;

final class ImportDetection extends NodeVisitorAbstract
{
    /** @var string[] */
    private $imports = [];

    public function enterNode(Node $node)
    {
        if (!$node instanceof Use_) {
            return;
        }
        foreach ($node->uses as $use) {
            $this->imports[$this->tag($use)] = (string) $use->name;
        }
    }

    public function imports(): array
    {
        return $this->imports;
    }

    private function tag(UseUse $use): string
    {
        return (string) ($use->alias ?? $use->name->getLast());
    }
}
