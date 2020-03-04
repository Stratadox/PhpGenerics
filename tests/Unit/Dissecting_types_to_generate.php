<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Test\Unit;

use Exception;
use NoNamespace;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Stratadox\PhpGenerics\Dissector\TypeDissector;
use Stratadox\PhpGenerics\Dissector\Error\InvalidTypeArgument;
use Stratadox\PhpGenerics\Dissector\MagicTypeDissector;
use Stratadox\PhpGenerics\Dissector\TypeInformation;
use Stratadox\PhpGenerics\Test\Fixture\Collection;
use Stratadox\PhpGenerics\Test\Fixture\Map;
use Stratadox\PhpGenerics\Test\Fixture\Thing;
use Stratadox\PhpGenerics\Test\Fixture\Item as Thing2;

/**
 * @testdox Dissecting types to generate
 */
class Dissecting_types_to_generate extends TestCase
{
    /** @var TypeDissector */
    private $dissector;

    protected function setUp(): void
    {
        $this->dissector = MagicTypeDissector::default();
    }

    /** @test */
    function finding_the_details_required_to_generate_a_Collection_of_Things()
    {
        $types = $this->dissector->typesToGenerate(Collection__Thing::class);

        $this->assertEquals(
            'Stratadox\\PhpGenerics\\Test\\Unit',
            $types->namespace()
        );
        $this->assertEquals(
            'Collection__Thing',
            $types->className()
        );
        $this->assertEquals(
            Collection::class,
            $types->baseClass()
        );
        $this->assertEquals(
            [Thing::class],
            $types->typeArguments()
        );
    }

    /** @test */
    function finding_the_details_required_to_generate_a_Collection_of_strings()
    {
        $types = $this->dissector->typesToGenerate(Collection__string::class);

        $this->assertEquals(
            'Stratadox\\PhpGenerics\\Test\\Unit',
            $types->namespace()
        );
        $this->assertEquals(
            'Collection__string',
            $types->className()
        );
        $this->assertEquals(
            Collection::class,
            $types->baseClass()
        );
        $this->assertEquals(
            ['string'],
            $types->typeArguments()
        );
    }

    /** @test */
    function finding_the_original_type_when_the_names_differ()
    {
        $types = $this->dissector->typesToGenerate(
            Collection__Thing2::class
        );

        $this->assertEquals(
            'Collection__Thing2',
            $types->className()
        );
        $this->assertEquals(
            Collection::class,
            $types->baseClass()
        );
        $this->assertEquals(
            ['Stratadox\\PhpGenerics\\Test\\Fixture\\Item'],
            $types->typeArguments()
        );
    }

    /** @test */
    function finding_details_for_a_Map_of_Collection_of_strings_to_strings()
    {
        // Map < Collection < string >, string >
        $types = $this->dissector->typesToGenerate(
            Map__Collection__string__string::class
        );

        $this->assertEquals(
            'Map__Collection__string__string',
            $types->className()
        );
        $this->assertEquals(
            Map::class,
            $types->baseClass()
        );
        $this->assertEquals(
            [Collection__string::class, 'string'],
            $types->typeArguments()
        );
    }

    /** @test */
    function finding_details_for_a_Collection_of_Maps_of_Collection_of_strings_to_strings()
    {
        // Collection < Map < Collection < string >, string > >
        $types = $this->dissector->typesToGenerate(
            Collection__Map__Collection__string__string::class
        );

        $this->assertEquals(
            'Collection__Map__Collection__string__string',
            $types->className()
        );
        $this->assertEquals(
            Collection::class,
            $types->baseClass()
        );
        $this->assertEquals(
            [Map__Collection__string__string::class],
            $types->typeArguments()
        );
    }

    /** @test */
    function finding_the_details_when_constructed_with_reflection()
    {
        $reflection = new ReflectionMethod(MagicTypeDissector::class, 'typesToGenerate');
        /** @var TypeInformation $types */
        $types = $reflection->invoke($this->dissector, Collection__Thing::class);

        $this->assertEquals(
            'Stratadox\\PhpGenerics\\Test\\Unit',
            $types->namespace()
        );
        $this->assertEquals(
            'Collection__Thing',
            $types->className()
        );
        $this->assertEquals(
            Collection::class,
            $types->baseClass()
        );
        $this->assertEquals(
            [Thing::class],
            $types->typeArguments()
        );
    }

    /** @test */
    function finding_details_of_generic_classes_in_the_global_namespace()
    {
        $types = $this->dissector->typesToGenerate(NoNamespace__int::class);

        $this->assertEquals(
            'Stratadox\\PhpGenerics\\Test\\Unit',
            $types->namespace()
        );
        $this->assertEquals(
            'NoNamespace__int',
            $types->className()
        );
        $this->assertEquals(
            NoNamespace::class,
            $types->baseClass()
        );
        $this->assertEquals(
            ['int'],
            $types->typeArguments()
        );
    }

    /** @test */
    function finding_details_using_the_imports_of_a_predefined_file()
    {
        $types = $this->dissector->typesFromFile(
            Collection__EqualsCode::class,
            __DIR__ . '/Generating_the_concrete_classes.php'
        );

        $this->assertEquals(
            ['Stratadox\\PhpGenerics\\Test\\Assertion\\EqualsCode'],
            $types->typeArguments()
        );
    }

    /** @test */
    function not_dissecting_non_generic_base_classes()
    {
        $this->expectException(InvalidTypeArgument::class);
        $this->expectExceptionMessage(
            TestCase::class . ' is not a generic base class.'
        );

        $this->dissector->typesToGenerate(
            TestCase__string::class
        );
    }

    /** @test */
    function not_dissecting_non_generic_base_classes_in_the_global_namespace()
    {
        $this->expectException(InvalidTypeArgument::class);
        $this->expectExceptionMessage(
            Exception::class . ' is not a generic base class.'
        );

        $this->dissector->typesToGenerate(Exception::class);
    }

    /** @test */
    function not_dissecting_from_non_existing_base_class()
    {
        $this->expectException(InvalidTypeArgument::class);
        $this->expectExceptionMessage(
            NotThere::class . ' is not a generic base class.'
        );

        $this->dissector->typesToGenerate(
            NotThere__string::class
        );
    }

    /** @test */
    function not_dissecting_when_an_argument_is_missing()
    {
        $this->expectException(InvalidTypeArgument::class);
        $this->expectExceptionMessage(
            'Missing type argument for type parameter 1 of generic class ' .
            Map::class
        );

        $this->dissector->typesToGenerate(
            Map__string::class
        );
    }

    /** @test */
    function not_dissecting_with_too_many_arguments()
    {
        $this->expectException(InvalidTypeArgument::class);
        $this->expectExceptionMessage(
            'Unexpected type argument 2 for generic class ' . Map::class
        );

        $this->dissector->typesToGenerate(
            Map__string__string__string::class
        );
    }

    /** @test */
    function not_dissecting_from_non_existing_type_argument()
    {
        $this->expectException(InvalidTypeArgument::class);
        $this->expectExceptionMessage(
            'Could not find the class ' . NotThere::class . ', type argument 0 ' .
            'for generic class ' . Collection::class
        );

        $this->dissector->typesToGenerate(
            Collection__NotThere::class
        );
    }
}
