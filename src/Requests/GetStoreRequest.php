<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Messages;
use OpenFGA\Network\{RequestContext, RequestMethod};
use OpenFGA\Translation\Translator;
use Override;
use Psr\Http\Message\StreamFactoryInterface;
use ReflectionException;

/**
 * Request for retrieving store information by its ID.
 *
 * This request fetches the details of a specific store, including its name
 * and metadata. It's useful for store management, displaying store information,
 * and validating store existence before performing operations.
 *
 * @see GetStoreRequestInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Stores/GetStore Get store API endpoint
 */
final readonly class GetStoreRequest implements GetStoreRequestInterface
{
    /**
     * Create a new store retrieval request.
     *
     * @param string $store The ID of the store to retrieve
     *
     * @throws ClientThrowable          If the store ID is empty
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public function __construct(
        private string $store,
    ) {
        if ('' === $this->store) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_STORE_ID_EMPTY)]);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        return new RequestContext(
            method: RequestMethod::GET,
            url: '/stores/' . $this->store,
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getStore(): string
    {
        return $this->store;
    }
}
