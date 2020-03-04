<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Generator;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PhpParser\NodeTraverser;
use PhpParser\NodeTraverserInterface;
use PhpParser\NodeVisitor;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard as StandardAstPrinter;
use PhpParser\PrettyPrinterAbstract as AstPrinter;
use Stratadox\PhpGenerics\Generator\Type\TypeArguments;

final class PhpSourceGenerator implements SourceGenerator
{
    /** @var Parser */
    private $parser;
    /** @var AstPrinter */
    private $printer;
    /** @var SourceExtractor */
    private $extract;
    /** @var VisitorFactory */
    private $visitors;

    public function __construct(
        Parser $parser,
        AstPrinter $printer,
        SourceExtractor $extract,
        VisitorFactory $visitors
    ) {
        $this->parser = $parser;
        $this->printer = $printer;
        $this->extract = $extract;
        $this->visitors = $visitors;
    }

    /** @throws AnnotationException */
    public static function default(): self
    {
        AnnotationRegistry::registerUniqueLoader('class_exists');
        return new self(
            (new ParserFactory())->create(ParserFactory::PREFER_PHP7),
            new StandardAstPrinter(),
            new SourceExtractor(),
            new StandardVisitorFactory(new AnnotationReader())
        );
    }

    public function generate(
        string $namespace,
        string $newClassName,
        string $genericBaseClass,
        TypeArguments $typeArguments
    ): string {
        return $this->produceConcreteChildClass(
            $this->extract->contentsOf($genericBaseClass),
            new NodeTraverser(),
            ...$this->visitors->replacing(
                $namespace,
                $newClassName,
                $this->extract->reflectionOf($genericBaseClass),
                $typeArguments
            )
        );
    }

    private function produceConcreteChildClass(
        string $baseClassContent,
        NodeTraverserInterface $traverser,
        NodeVisitor ...$visitors
    ): string {
        foreach ($visitors as $visitor) {
            $traverser->addVisitor($visitor);
        }
        return '<?php '.$this->printer->prettyPrint(
            $traverser->traverse($this->parser->parse($baseClassContent))
        );
    }
}
