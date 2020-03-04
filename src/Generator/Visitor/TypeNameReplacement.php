<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Generator\Visitor;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\NodeVisitorAbstract;
use Stratadox\PhpGenerics\Generator\Type\TypeArgument;
use Stratadox\PhpGenerics\Generator\Type\TypeMap;

final class TypeNameReplacement extends NodeVisitorAbstract
{
    /** @var TypeMap|TypeArgument[] */
    private $typeMap;

    public function __construct(TypeMap $typeMap)
    {
        $this->typeMap = $typeMap;
    }

    public function leaveNode(Node $node)
    {
        if (!$node instanceof ClassConstFetch) {
            return null;
        }
        if (!$node->name instanceof Identifier) {
            return null;
        }
        if ((string) $node->name !== 'class') {
            return null;
        }
        return $this->typeMap[(string) $node->class]->typeFetch();
    }
}
