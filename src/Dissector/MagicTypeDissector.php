<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Dissector;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use LogicException;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Stratadox\PhpGenerics\Dissector\Error\InvalidTypeArgument;
use Stratadox\PhpGenerics\Dissector\Visitor\ImportDetection;
use Stratadox\PhpGenerics\Dissector\Visitor\NamespaceDetection;
use Stratadox\PhpGenerics\Loader;
use Stratadox\PhpGenerics\Primitives;
use function array_map;
use function array_slice;
use function debug_backtrace;
use function explode;
use function file_get_contents;
use function sprintf;
use function strrpos;
use function substr;
use const DEBUG_BACKTRACE_IGNORE_ARGS as IGNORE_ARGS;

final class MagicTypeDissector implements TypeDissector
{
    /** @var Parser */
    private $parser;
    /** @var ArgumentDeduction */
    private $arguments;
    /** @var Primitives */
    private $primitives;
    /** @var string */
    private $separator;

    public function __construct(
        Parser $parser,
        ArgumentDeduction $arguments,
        Primitives $primitives,
        string $separator
    ) {
        $this->parser = $parser;
        $this->arguments = $arguments;
        $this->primitives = $primitives;
        $this->separator = $separator;
    }

    /** @throws AnnotationException */
    public static function default(): self
    {
        AnnotationRegistry::registerUniqueLoader('class_exists');
        // @todo get rid of this bidirectional dependency!
        $deduction = new AnnotationArgumentDeduction(
            new AnnotationReader(),
            new Primitives(),
            '__',
            Loader::default()
        );
        $dissector = new self(
            (new ParserFactory())->create(ParserFactory::PREFER_PHP7),
            $deduction,
            new Primitives(),
            '__'
        );
        $deduction->setDissector($dissector);
        return $dissector;
    }

    public function typesToGenerate(
        string $class,
        int $levelsAway = 0
    ): TypeInformation {
        return $this->extract(
            $class,
            $this->fileFor(debug_backtrace(IGNORE_ARGS), $levelsAway),
            ...explode($this->separator, $this->shortName($class))
        );
    }

    public function typesFromFile(string $class, string $file): TypeInformation
    {
        return $this->extract(
            $class,
            $file,
            ...explode($this->separator, $this->shortName($class))
        );
    }

    private function fileFor(array $stackTrace, int $minimumDistance): string
    {
        foreach (array_slice($stackTrace, $minimumDistance) as $entry) {
            if (isset($entry['file'])) {
                return $entry['file'];
            }
        }
        throw new LogicException('Failed to find calling file.');
    }

    /** @throws InvalidTypeArgument */
    private function extract(
        string $class,
        string $callingFile,
        string $base,
        string ...$types
    ): TypeInformation {
        $traverser = new NodeTraverser();
        $callerNamespace = new NamespaceDetection();
        $imports = new ImportDetection();
        $traverser->addVisitor($imports);
        $traverser->addVisitor($callerNamespace);
        // here be dragons
        $traverser->traverse($this->parser->parse(file_get_contents($callingFile)));
        $baseClass = $this->baseClass($imports, $callerNamespace, $base);
        return new TypeInformation(
            $this->namespace($class),
            $this->shortName($class),
            $baseClass,
            ...$this->arguments->for(
                $this->namespace($class),
                $baseClass,
                $callingFile,
                ...$this->fullyQualified($imports, $callerNamespace, ...$types)
            )
        );
    }

    private function namespace(string $fqcn): string
    {
        $pos = strrpos($fqcn, '\\');
        return $pos === false ? '' : substr($fqcn, 0, $pos);
    }

    private function shortName(string $fqcn): string
    {
        return substr(strrchr('\\' . $fqcn, '\\'), 1);
    }

    private function baseClass(
        ImportDetection $used,
        NamespaceDetection $detect,
        string $name
    ): string {
        return $used->imports()[$name] ?? sprintf('%s\\%s', $detect->namespace(), $name);
    }

    private function fullyQualified(
        ImportDetection $detected,
        NamespaceDetection $caller,
        string ...$types
    ): array {
        return array_map(
            function (string $argument) use ($detected, $caller): string {
                return $detected->imports()[$argument] ??
                    ($this->primitives->includes($argument) ?
                        $argument :
                        sprintf('%s\\%s', $caller->namespace(), $argument)
                    );
            },
            $types);
    }
}
