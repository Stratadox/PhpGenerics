<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Test;

use PHPUnit\Framework\TestCase;
use stdClass;
use Stratadox\PhpGenerics\Autoloader;
use Stratadox\PhpGenerics\Test\Fixture\Collection;
use Stratadox\PhpGenerics\Test\Fixture\Thing;
use TypeError;

/**
 * @testdox Collection of Collections of Things
 */
class Collection_of_Collections_of_Things extends TestCase
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
    function looping_over_a_collection_of_collections_of_things()
    {
        /** @var Collection<Collection<Thing>>|Thing[][] $things */
        $things = new Collection__Collection__Thing(
            new Collection__Thing(new Thing('thing 1.1'), new Thing('thing 1.2')),
            new Collection__Thing(new Thing('thing 2.1'), new Thing('thing 2.2'))
        );

        $this->assertCount(2, $things);
        $names = [];
        foreach ($things as $thingCollection) {
            foreach ($thingCollection as $thing) {
                $this->assertInstanceOf(Thing::class, $thing);
                $names[] = $thing->name();
            }
        }
        $this->assertEquals(
            ['thing 1.1', 'thing 1.2', 'thing 2.1', 'thing 2.2'],
            $names
        );
    }

    /** @test */
    function not_instantiating_with_an_stdClass_collection()
    {
        $stdClassCollection = new Collection__stdClass(new stdClass());

        $this->expectException(TypeError::class);

        new Collection__Collection__Thing($stdClassCollection);
    }
}
