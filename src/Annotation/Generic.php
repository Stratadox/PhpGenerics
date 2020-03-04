<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Annotation;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 */
final class Generic
{
    /**
     * @var int The amount of type parameters for the generic class
     * @Required
     */
    public $count;
}
