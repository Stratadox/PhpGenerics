<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Test\Fixture;

class Thing implements Named
{
    /** @var string */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function name(): string
    {
        return $this->name;
    }
}
