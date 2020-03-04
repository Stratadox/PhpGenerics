<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Generator;

use Doctrine\Common\Annotations\Reader;
use PhpParser\NodeVisitor;
use ReflectionClass;
use Stratadox\PhpGenerics\Generator\Type\TypeArguments;
use Stratadox\PhpGenerics\Generator\Visitor\ClassNameReplacement;
use Stratadox\PhpGenerics\Generator\Visitor\DocCommentReplacement;
use Stratadox\PhpGenerics\Generator\Visitor\ImportsReplacement;
use Stratadox\PhpGenerics\Generator\Visitor\InstanceOfReplacement;
use Stratadox\PhpGenerics\Generator\Visitor\NamespaceReplacement;
use Stratadox\PhpGenerics\Generator\Visitor\ParentBypass;
use Stratadox\PhpGenerics\Generator\Visitor\ReturnTypeEnforcement;
use Stratadox\PhpGenerics\Generator\Visitor\TypeHintReplacement;
use Stratadox\PhpGenerics\Generator\Visitor\TypeMapAssembly;
use Stratadox\PhpGenerics\Generator\Type\TypeMap;
use Stratadox\PhpGenerics\Generator\Visitor\TypeNameReplacement;

final class StandardVisitorFactory implements VisitorFactory
{
    /** @var Reader */
    private $annotationReader;

    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /** @return NodeVisitor[] */
    public function replacing(
        string $namespace,
        string $newClassName,
        ReflectionClass $genericBase,
        TypeArguments $typeArguments
    ): array {
        $typeMap = new TypeMap();
        return [
            new NamespaceReplacement($namespace),
            new ImportsReplacement(
                $genericBase->getName(),
                $typeArguments
            ),
            new ClassNameReplacement($newClassName),
            new TypeMapAssembly(
                $typeMap,
                $typeArguments,
                'Stratadox\\PhpGenerics\\Generic\\'
            ),
            new TypeHintReplacement($typeMap),
            new ReturnTypeEnforcement(
                $typeMap,
                $this->annotationReader,
                $genericBase
            ),
            new DocCommentReplacement(
                $typeMap,
                '@param %s ',
                '@return %s'
            ),
            new InstanceOfReplacement($typeMap),
            new ParentBypass($genericBase),
            new TypeNameReplacement($typeMap),
        ];
    }
}
