<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use InvalidArgumentException;
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use ReflectionException;
use Throwable;

/**
 * Serialization error types for the OpenFGA SDK.
 *
 * Defines specific serialization and data processing failures that can occur
 * when converting between different data formats (JSON, objects, etc.) or
 * when validating data structures. Each case provides a factory method
 * to create the corresponding SerializationException.
 *
 * Serialization errors typically occur during data transformation between
 * JSON and PHP objects, schema validation, or when processing API responses.
 * These errors often indicate data format mismatches, missing required fields,
 * or type conversion failures that prevent proper object construction.
 *
 * @see https://openfga.dev/docs/getting-started/setup-openfga OpenFGA Setup Guide
 * @see SerializationException Concrete exception implementation
 */
enum SerializationError: string
{
    use ExceptionLocationTrait;

    case CouldNotAddItemsToCollection = 'could_not_add_items_to_collection';

    case EmptyCollection = 'empty_collection';

    case InvalidItemType = 'invalid_item_type';

    case MissingRequiredConstructorParameter = 'missing_required_constructor_parameter';

    case Response = 'response';

    case UndefinedItemType = 'undefined_item_type';

    /**
     * Create a new SerializationException for this error type.
     *
     * Factory method that creates a SerializationException instance with the
     * current error type and provided context information. This provides a
     * convenient way to generate typed exceptions with proper error categorization
     * and rich debugging context for OpenFGA serialization failures.
     *
     * The exception will automatically capture the correct file and line location
     * where this method was called (typically where `throw` occurs), ensuring
     * debuggers show the actual throw location rather than this factory method.
     *
     * @param RequestInterface|null  $request  The PSR-7 HTTP request being processed when serialization failed, if applicable
     * @param ResponseInterface|null $response The PSR-7 HTTP response containing invalid data, if applicable
     * @param array<string, mixed>   $context  Additional context data including serialization details, data format information, and debugging data
     * @param Throwable|null         $prev     The previous throwable used for exception chaining, if any
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If location capture fails
     *
     * @return SerializationException The newly created SerializationException instance with comprehensive error context
     */
    public function exception(
        ?RequestInterface $request = null,
        ?ResponseInterface $response = null,
        array $context = [],
        ?Throwable $prev = null,
    ): SerializationException {
        $exception = new SerializationException($this, $request, $response, $context, $prev);
        self::captureThrowLocation($exception);

        return $exception;
    }

    /**
     * Check if this serialization error is related to collection operations.
     *
     * Useful for identifying errors that occur during collection manipulation
     * and providing appropriate error handling strategies.
     *
     * @return bool True if the error is collection-related, false otherwise
     */
    public function isCollectionError(): bool
    {
        return match ($this) {
            self::CouldNotAddItemsToCollection,
            self::EmptyCollection,
            self::InvalidItemType,
            self::UndefinedItemType => true,
            self::MissingRequiredConstructorParameter,
            self::Response => false,
        };
    }

    /**
     * Check if this serialization error indicates a data type validation failure.
     *
     * Useful for distinguishing between validation errors and structural errors
     * during serialization processes.
     *
     * @return bool True if the error is type-related, false otherwise
     */
    public function isTypeValidationError(): bool
    {
        return match ($this) {
            self::InvalidItemType,
            self::UndefinedItemType,
            self::MissingRequiredConstructorParameter => true,
            self::CouldNotAddItemsToCollection,
            self::EmptyCollection,
            self::Response => false,
        };
    }
}
