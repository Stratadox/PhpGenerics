<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Test\Unit;

use PHPUnit\Framework\TestCase;
use Stratadox\PhpGenerics\Generator\PhpSourceGenerator;
use Stratadox\PhpGenerics\Generator\SourceGenerator;
use Stratadox\PhpGenerics\Generator\TypeArgumentFactory;
use Stratadox\PhpGenerics\Test\Assertion\EqualsCode;
use Stratadox\PhpGenerics\Test\Fixture\Collection;
use Stratadox\PhpGenerics\Test\Fixture\Map;
use Stratadox\PhpGenerics\Test\Fixture\Thing;

/**
 * @testdox Generating the concrete classes
 */
class Generating_the_concrete_classes extends TestCase
{
    /** @var TypeArgumentFactory */
    private $typeArguments;
    /** @var SourceGenerator */
    private $generator;

    protected function setUp(): void
    {
        $this->typeArguments = new TypeArgumentFactory();
        $this->generator = PhpSourceGenerator::default();
    }

    /** @test */
    function generating_a_Map_of_Things_to_other_Things()
    {
        $this->assertThat(
            $this->generator->generate(
                'Stratadox\\PhpGenerics\\Test',
                'Map__Thing__Thing',
                Map::class,
                $this->typeArguments->for(Thing::class, Thing::class)
            ),
            EqualsCode::in(
                __DIR__ . '/../Fixture/Expectation/Map__Thing__Thing.php'
            )
        );
    }

    /** @test */
    function generating_a_Map_of_strings_to_other_Things()
    {
        $this->assertThat(
            $this->generator->generate(
                'Stratadox\\PhpGenerics\\Test',
                'Map__string__Thing',
                Map::class,
                $this->typeArguments->for('string', Thing::class)
            ),
            EqualsCode::in(
                __DIR__ . '/../Fixture/Expectation/Map__string__Thing.php'
            )
        );
    }

    /** @test */
    function generating_a_Collection_of_integers()
    {
        $this->assertThat(
            $this->generator->generate(
                'Stratadox\\PhpGenerics\\Test',
                'Collection__int',
                Collection::class,
                $this->typeArguments->for('int')
            ),
            EqualsCode::in(
                __DIR__ . '/../Fixture/Expectation/Collection__int.php'
            )
        );
    }
}
