<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Writer;

use function dirname;
use function file_put_contents;
use function is_dir;
use function mkdir;
use function sprintf;
use function str_replace;
use const DIRECTORY_SEPARATOR as DS;

class FileWriter
{
    /** @var string */
    private $baseDirectory;
    /** @var string */
    private $separator;

    public function __construct(string $baseDirectory, string $separator)
    {
        $this->baseDirectory = $baseDirectory;
        $this->separator = $separator;
    }

    public static function default(): self
    {
        return new self(
            dirname(__DIR__, 2) . DS . 'Generated' . DS,
            DS
        );
    }

    public function write(string $namespace, string $class, string $content): void
    {
        $path = $this->pathFor($namespace, $class);
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
        file_put_contents($path, $content);
    }

    public function pathFor(string $namespace, string $class): string
    {
        return sprintf(
            '%s%s%s%s.php',
            $this->baseDirectory,
            str_replace('\\', $this->separator, $namespace),
            $this->separator,
            $class
        );
    }
}
