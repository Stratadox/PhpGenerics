<?php declare(strict_types=1);

namespace Stratadox\PhpGenerics\Annotation;

/**
 * Use this annotation to enforce return types on generic classes.
 *
 * @Annotation
 * @Target("METHOD")
 */
final class ReturnType
{
    /** @var string Type parameter to enforce on this method, e.g. "T" */
    public $force;
}
