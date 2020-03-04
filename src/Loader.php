<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics;

use Doctrine\Common\Annotations\AnnotationException;
use Stratadox\PhpGenerics\Dissector\TypeInformation;
use Stratadox\PhpGenerics\Generator\PhpSourceGenerator;
use Stratadox\PhpGenerics\Generator\SourceGenerator;
use Stratadox\PhpGenerics\Generator\Type\TypeArguments;
use Stratadox\PhpGenerics\Generator\TypeArgumentFactory;
use Stratadox\PhpGenerics\Writer\FileWriter;
use function get_declared_classes;
use function in_array;

class Loader
{
    /** @var SourceGenerator */
    private $generator;
    /** @var FileWriter */
    private $writer;
    /** @var TypeArgumentFactory */
    private $types;

    public function __construct(
        SourceGenerator $generator,
        FileWriter $writer,
        TypeArgumentFactory $types
    ) {
        $this->generator = $generator;
        $this->writer = $writer;
        $this->types = $types;
    }

    /** @throws AnnotationException */
    public static function default(): self
    {
        return new self(
            PhpSourceGenerator::default(),
            FileWriter::default(),
            new TypeArgumentFactory()
        );
    }

    public function generate(TypeInformation $generic): void
    {
        $this->make(
            $generic->className(),
            $generic->namespace(),
            $generic->baseClass(),
            $this->types->for(...$generic->typeArguments())
        );
    }

    private function make(
        string $class,
        string $namespace,
        string $base,
        TypeArguments $typeArguments
    ): void {
        if (in_array($namespace . '\\' . $class, get_declared_classes())) {
            return;
        }
        $this->writer->write($namespace, $class, $this->generator->generate(
            $namespace,
            $class,
            $base,
            $typeArguments
        ));
        require $this->writer->pathFor($namespace, $class);
    }
}
