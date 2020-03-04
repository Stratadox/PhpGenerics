<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics;

use Doctrine\Common\Annotations\AnnotationException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Stratadox\PhpGenerics\Dissector\Error\InvalidTypeArgument;
use Stratadox\PhpGenerics\Dissector\MagicTypeDissector;
use Stratadox\PhpGenerics\Dissector\TypeDissector;
use function spl_autoload_register;
use function spl_autoload_unregister;

class Autoloader
{
    /** @var TypeDissector */
    private $dissector;
    /** @var Loader */
    private $loader;
    /** @var LoggerInterface */
    private $log;

    public function __construct(
        TypeDissector $dissector,
        Loader $loader,
        LoggerInterface $log
    ) {
        $this->dissector = $dissector;
        $this->loader = $loader;
        $this->log = $log;
    }

    /** @throws AnnotationException */
    public static function default(): self
    {
        return new self(
            MagicTypeDissector::default(),
            Loader::default(),
            new NullLogger()
        );
    }

    public function install(): void
    {
        spl_autoload_register([$this, 'load']);
    }

    public function uninstall(): void
    {
        spl_autoload_unregister([$this, 'load']);
    }

    public function load(string $class): void
    {
        try {
            $this->loader->generate($this->dissector->typesToGenerate($class, 2));
        } catch (InvalidTypeArgument $exception) {
            $this->log->info(
                'Not generically auto loading: ' . $exception->getMessage()
            );
        }
    }
}
