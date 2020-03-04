<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Generator\Visitor;

use PhpParser\Node;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Name;
use PhpParser\NodeVisitorAbstract;
use Stratadox\PhpGenerics\Generator\Type\TypeArgument;
use Stratadox\PhpGenerics\Generator\Type\TypeMap;

final class InstanceOfReplacement extends NodeVisitorAbstract
{
    /** @var TypeMap|TypeArgument[] */
    private $typeMap;

    public function __construct(TypeMap $typeMap)
    {
        $this->typeMap = $typeMap;
    }

    public function leaveNode(Node $node)
    {
        if (!$node instanceof Instanceof_) {
            return null;
        }
        if (!$node->class instanceof Name) {
            return null;
        }
        if (!isset($this->typeMap[(string) $node->class])) {
            return null;
        }
        return $this->typeMap[(string) $node->class]->typeCheck($node->expr);
    }
}
