<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Dissector;

class TypeInformation
{
    /** @var string */
    private $targetNamespace;
    /** @var string */
    private $className;
    /** @var string */
    private $baseClass;
    /** @var string[] */
    private $typeArguments;

    public function __construct(
        string $targetNamespace,
        string $className,
        string $baseClass,
        string ...$typeArguments
    ) {
        $this->targetNamespace = $targetNamespace;
        $this->className = $className;
        $this->baseClass = $baseClass;
        $this->typeArguments = $typeArguments;
    }

    /**
     * Retrieves the target namespace for the new class.
     *
     * @return string The namespace
     */
    public function namespace(): string
    {
        return $this->targetNamespace;
    }

    /**
     * Retrieves the (short) class name for the new class.
     *
     * @return string The unqualified/short name
     */
    public function className(): string
    {
        return $this->className;
    }

    /**
     * Retrieves the (fully qualified) generic base class, which the new class
     * will extend from.
     *
     * @return string The fully qualified base class name
     */
    public function baseClass(): string
    {
        return $this->baseClass;
    }

    /**
     * Retrieves the type arguments, a list types that consists of either
     * primitive types (string, int, etc) or fully qualified class names.
     *
     * @return string[] The list of type arguments
     */
    public function typeArguments(): array
    {
        return $this->typeArguments;
    }
}
