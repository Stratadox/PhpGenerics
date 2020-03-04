<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Test;

use PHPUnit\Framework\TestCase;
use stdClass;
use Stratadox\PhpGenerics\Autoloader;
use Stratadox\PhpGenerics\Test\Fixture\Collection;
use Stratadox\PhpGenerics\Test\Fixture\Thing;
use TypeError;

/**
 * @testdox Collection of Things
 */
class Collection_of_Things extends TestCase
{
    private $autoloader;

    protected function setUp(): void
    {
        $this->autoloader = Autoloader::default();
        $this->autoloader->install();
    }

    protected function tearDown(): void
    {
        $this->autoloader->uninstall();
    }

    /** @test */
    function looping_over_a_collection_of_things()
    {
        /** @var Collection<Thing>|Thing[] $things */
        $things = new Collection__Thing(new Thing('thing 1'), new Thing('thing 2'));

        $this->assertCount(2, $things);
        $names = [];
        foreach ($things as $thing) {
            $this->assertInstanceOf(Thing::class, $thing);
            $names[] = $thing->name();
        }
        $this->assertEquals(['thing 1', 'thing 2'], $names);
    }

    /** @test */
    function not_instantiating_a_things_collection_with_integers()
    {
        $this->expectException(TypeError::class);

        new Collection__Thing(1, 2);
    }

    /** @test */
    function not_putting_an_stdClass_in_a_things_collection()
    {
        /** @var Collection<Thing>|Thing[] $things */
        $things = new Collection__Thing(new Thing('thing 1'), new Thing('thing 2'));

        $this->expectException(TypeError::class);

        $things[1] = new stdClass();
    }
}
