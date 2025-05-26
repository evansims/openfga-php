<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Throwable;

enum SerializationError: string
{
    case EmptyCollection = 'empty_collection';

    case InvalidItemType = 'invalid_item_type';

    case Response = 'response';

    case UndefinedItemType = 'undefined_item_type';

    case MissingRequiredConstructorParameter = 'missing_required_constructor_parameter';

    case CouldNotAddItemsToCollection = 'could_not_add_items_to_collection';

    /**
     * Creates and returns a new SerializationException.
     *
     * @param ?RequestInterface    $request  The HTTP request that triggered the exception, if applicable.
     * @param ?ResponseInterface   $response The HTTP response received, if applicable.
     * @param array<string, mixed> $context  Additional context for the exception.
     * @param ?Throwable           $prev     The previous throwable used for exception chaining, if any.
     *
     * @return ClientThrowable The newly created SerializationException instance.
     */
    public function exception(
        ?RequestInterface $request = null,
        ?ResponseInterface $response = null,
        array $context = [],
        ?Throwable $prev = null,
    ): ClientThrowable {
        return new SerializationException($this, $request, $response, $context, $prev);
    }
}
