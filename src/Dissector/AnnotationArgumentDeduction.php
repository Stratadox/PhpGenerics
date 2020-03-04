<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Dissector;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use ReflectionException;
use Stratadox\PhpGenerics\Annotation\Generic;
use Stratadox\PhpGenerics\Dissector\Error\BaseClassIsNotGeneric;
use Stratadox\PhpGenerics\Dissector\Error\MissingTypeArgument;
use Stratadox\PhpGenerics\Dissector\Error\TypeArgumentNotFound;
use Stratadox\PhpGenerics\Dissector\Error\UnexpectedTypeArgument;
use Stratadox\PhpGenerics\Loader;
use Stratadox\PhpGenerics\Primitives;
use function assert;
use function class_exists;
use function count;
use function implode;
use function sprintf;
use function strrchr;
use function substr;

final class AnnotationArgumentDeduction implements ArgumentDeduction
{
    /** @var Reader */
    private $annotation;
    /** @var Primitives */
    private $primitives;
    /** @var string */
    private $separator;
    /** @var TypeDissector|null */
    private $dissect;
    /** @var Loader */
    private $loader;

    public function __construct(
        Reader $annotation,
        Primitives $primitives,
        string $separator,
        Loader $loader
    ) {
        $this->annotation = $annotation;
        $this->primitives = $primitives;
        $this->separator = $separator;
        $this->loader = $loader;
    }

    public function setDissector(TypeDissector $dissector): void
    {
        assert(null === $this->dissect);
        $this->dissect = $dissector;
    }

    public function for(
        string $namespace,
        string $baseClass,
        string $callingFile,
        string ...$types
    ): array {
        assert(null !== $this->dissect);
        $baseAnnotation = $this->annotationOf($baseClass);
        if ($baseAnnotation === null) {
            throw BaseClassIsNotGeneric::cannotUse($baseClass);
        }
        $expectedCount = $baseAnnotation->count;
        $typeArguments = [];
        $skip = 0;
        $genericTypeArgument = [];
        foreach ($types as $type) {
            $annotation = $this->annotationOf($type);
            if ($annotation !== null) {
                $skip += $annotation->count + ($skip === 0 ? 1 : 0);
            }
            if ($skip > 0) {
                $genericTypeArgument[] = $this->shortNameOf($type);
                $skip--;
                if ($skip === 0) {
                    $genericType = sprintf(
                        '%s\\%s',
                        $namespace,
                        implode($this->separator, $genericTypeArgument)
                    );
                    $this->loader->generate($this->dissect->typesFromFile(
                        $genericType,
                        $callingFile
                    ));
                    // @todo reset $genericTypeArgument
                    $typeArguments[] = $genericType;
                }
                continue;
            }
            if (!$this->primitives->includes($type) && !class_exists($type)) {
                throw TypeArgumentNotFound::for($type, $baseClass, count($typeArguments));
            }
            $typeArguments[] = $type;
        }
        if (count($typeArguments) > $expectedCount) {
            throw UnexpectedTypeArgument::for($baseClass, count($typeArguments) - 1);
        }
        if (count($typeArguments) < $expectedCount) {
            throw MissingTypeArgument::for($baseClass, count($typeArguments));
        }
        return $typeArguments;
    }

    private function annotationOf(string $class): ?Generic
    {
        try {
            $annotation = $this->annotation->getClassAnnotation(
                new ReflectionClass($class),
                Generic::class
            );
        } catch (ReflectionException $e) {
            return null;
        }
        assert($annotation instanceof Generic || $annotation === null);
        return $annotation;
    }

    private function shortNameOf(string $fqcn): string
    {
        return substr(strrchr('\\' . $fqcn, '\\'), 1);
    }
}
