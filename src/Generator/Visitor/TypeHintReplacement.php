<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Generator\Visitor;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\NodeVisitorAbstract;
use Stratadox\PhpGenerics\Generator\Type\TypeArgument;
use Stratadox\PhpGenerics\Generator\Type\TypeMap;

final class TypeHintReplacement extends NodeVisitorAbstract
{
    /** @var TypeMap|TypeArgument[] */
    private $typeMap;

    public function __construct(TypeMap $typeMap)
    {
        $this->typeMap = $typeMap;
    }

    public function leaveNode(Node $node)
    {
        if (!$node instanceof Node\Stmt\ClassMethod) {
            return;
        }
        if ((string) $node->name !== '__construct') {
            return;
        }
        foreach ($node->params as $parameter) {
            if (null === $parameter->type) {
                continue;
            }
            if (!isset($this->typeMap[(string) $parameter->type])) {
                continue;
            }
            $parameter->type = new Identifier(
                (string) $this->typeMap[(string) $parameter->type]
            );
        }
    }
}
