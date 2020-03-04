<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Test;

use PHPUnit\Framework\TestCase;
use stdClass;
use Stratadox\PhpGenerics\Autoloader;
use Stratadox\PhpGenerics\Test\Fixture\Map;
use Stratadox\PhpGenerics\Test\Fixture\Thing;
use TypeError;

/**
 * @testdox Map of Things to other Things
 */
class Map_of_Things_to_other_Things extends TestCase
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
    function fetching_the_Thing_mapped_to_the_other_Thing()
    {
        /** @var Map[Thing, Thing] $things */
        $things = new Map__Thing__Thing();
        $key = new Thing('key');
        $value = new Thing('value');
        $things[$key] = $value;

        $this->assertEquals($value, $things[$key]);
    }

    /** @test */
    function distinguishing_between_keys_by_reference()
    {
        /** @var Map[Thing, Thing] $things */
        $things = new Map__Thing__Thing();
        $key1 = new Thing('key');
        $key2 = new Thing('key');
        $value1 = new Thing('value 1');
        $value2 = new Thing('value 2');
        $things[$key1] = $value1;
        $things[$key2] = $value2;

        $this->assertEquals($value1, $things[$key1]);
        $this->assertEquals($value2, $things[$key2]);
    }

    /** @test */
    function not_using_a_non_Thing_key()
    {
        /** @var Map[Thing, Thing] $things */
        $things = new Map__Thing__Thing();
        $key = new stdClass();
        $value = new Thing('value');

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('Argument 0');

        $things[$key] = $value;
    }

    /** @test */
    function not_using_a_non_Thing_value()
    {
        /** @var Map[Thing, Thing] $things */
        $things = new Map__Thing__Thing();
        $key = new Thing('key');
        $value = new stdClass();

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('Argument 1');

        $things[$key] = $value;
    }
}
