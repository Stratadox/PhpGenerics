<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Generator\Visitor;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Stratadox\PhpGenerics\Annotation\Generic;
use Stratadox\PhpGenerics\Generator\Type\TypeArguments;
use function in_array;
use function strpos;

final class ImportsReplacement extends NodeVisitorAbstract
{
    /** @var string[] */
    private $newImports;

    public function __construct(
        string $genericBaseClass,
        TypeArguments $typeArguments
    ) {
        $this->newImports = [$genericBaseClass];
        foreach ($typeArguments as $argument) {
            if (
                $argument->requiresImport() &&
                !in_array($argument->fullName(), $this->newImports)
            ) {
                $this->newImports[] = $argument->fullName();
            }
        }
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Namespace_) {
            foreach ($this->newImports as $import) {
                $this->addImportTo($node, $import);
            }
        } elseif ($node instanceof Use_) {
            if ($this->shouldRemoveImport($node->uses[0]->name->toString())) {
                return NodeTraverser::REMOVE_NODE;
            }
        }
        return null;
    }

    private function addImportTo(Node $namespace, string $import): void
    {
        $position = 0;
        foreach ($namespace->stmts as $stmt) {
            if ($stmt instanceof Use_ && $stmt->uses[0]->name->toString() < $import) {
                $position++;
            }
        }
        $namespace->stmts = array_merge(
            array_slice($namespace->stmts, 0, $position),
            [new Use_([new UseUse(new Name($import))])],
            array_slice($namespace->stmts, $position)
        );
    }

    private function shouldRemoveImport(string $use): bool
    {
        return strpos($use, 'Stratadox\\PhpGenerics\\Generic\\') === 0
            || $use === Generic::class;
    }
}
