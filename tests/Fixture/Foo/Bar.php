<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Test\Fixture\Foo;

use Stratadox\PhpGenerics\Annotation\Generic;
use Stratadox\PhpGenerics\Generic\T;

/** @Generic(count = 1) */
class Bar
{
    /** @var T */
    private $instance;

    public function __construct(T $instance)
    {
        $this->instance = $instance;
    }

    public function foo(): T
    {
        return $this->instance;
    }
}
