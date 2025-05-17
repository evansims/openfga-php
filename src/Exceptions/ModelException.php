<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use InvalidArgumentException;
use OpenFGA\Models\ModelInterface;

use function sprintf;

final class ModelException extends InvalidArgumentException
{
    public static function immutable(): self
    {
        return new self('Collection is immutable');
    }

    /**
     * @param class-string $itemType
     */
    public static function invalidItemType(string $itemType): self
    {
        return new self(sprintf(
            'Item type %s must implement %s',
            $itemType,
            ModelInterface::class,
        ));
    }

    /**
     * @param class-string $expectedType
     * @param class-string $actualType
     */
    public static function typeMismatch(string $expectedType, string $actualType): self
    {
        return new self(sprintf(
            'Expected item of type %s, got %s',
            $expectedType,
            $actualType,
        ));
    }

    public static function undefinedItemType(string $className): self
    {
        return new self(sprintf(
            'Class %s must define protected static string $itemType with a valid model class name',
            $className,
        ));
    }
}
