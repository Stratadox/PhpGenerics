<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Generator\Visitor;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Use_ as UseClause;
use PhpParser\NodeVisitorAbstract;
use Stratadox\PhpGenerics\Generator\Type\TypeArguments;
use Stratadox\PhpGenerics\Generator\Type\TypeMap;
use function strpos;

final class TypeMapAssembly extends NodeVisitorAbstract
{
    /** @var TypeMap */
    private $typeMap;
    /** @var TypeArguments */
    private $argumentFor;
    /** @var string */
    private $genericNamespace;
    /** @var int */
    private $parameterNumber = 0;

    public function __construct(
        TypeMap $typeMap,
        TypeArguments $arguments,
        string $genericNamespace
    ) {
        $this->typeMap = $typeMap;
        $this->argumentFor = $arguments;
        $this->genericNamespace = $genericNamespace;
    }

    public function enterNode(Node $useClause)
    {
        if (!$useClause instanceof UseClause) {
            return;
        }
        if (!$this->isGenericTypeParameter($this->nameOf($useClause))) {
            return;
        }
        $this->typeMap[$this->nameOf($useClause)->getLast()] =
            $this->argumentFor[$this->parameterNumber++];
    }

    private function nameOf(UseClause $import): Name
    {
        return $import->uses[0]->name;
    }

    private function isGenericTypeParameter(Name $import): bool
    {
        return strpos((string) $import, $this->genericNamespace) === 0;
    }
}
