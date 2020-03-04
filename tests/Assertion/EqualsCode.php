<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Test\Assertion;

use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\PrettyPrinterAbstract;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\IsEqual;
use function file_get_contents;
use function sprintf;

final class EqualsCode extends Constraint
{
    /** @var string */
    private $file;
    /** @var Parser */
    private $parser;
    /** @var PrettyPrinterAbstract */
    private $printer;

    public function __construct(
        string $file,
        Parser $parser,
        PrettyPrinterAbstract $printer
    ) {
        $this->file = $file;
        $this->parser = $parser;
        $this->printer = $printer;
    }

    public static function in(string $file): self
    {
        return new self(
            $file,
            (new ParserFactory())->create(ParserFactory::PREFER_PHP7),
            new Standard()
        );
    }

    public function toString(): string
    {
        return sprintf('is equal to the code in `%s`', $this->file);
    }

    public function evaluate(
        $other,
        string $description = '',
        bool $returnResult = false
    ) {
        return (
            new IsEqual($this->code(file_get_contents($this->file)))
        )->evaluate($this->code($other), $description, $returnResult);
    }

    private function code(string $code)
    {
        return $this->printer->prettyPrint($this->parser->parse($code));
    }
}
