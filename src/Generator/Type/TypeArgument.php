<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Generator\Type;

use PhpParser\Node;
use PhpParser\Node\Expr;

interface TypeArgument
{
    public function typeCheck(Expr $expr): Node;
    public function typeFetch(): Node;
    public function requiresImport(): bool;
    public function fullName(): string;
    public function __toString(): string;
}
