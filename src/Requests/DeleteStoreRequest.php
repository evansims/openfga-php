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
 * Request for permanently deleting a store and all its data.
 *
 * This request removes the entire store, including all authorization models,
 * relationship tuples, and associated metadata. This operation is irreversible
 * and should be used with extreme caution in production environments.
 *
 * @see DeleteStoreRequestInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Stores/DeleteStore Delete store API endpoint
 */
final readonly class DeleteStoreRequest implements DeleteStoreRequestInterface
{
    /**
     * Create a new store deletion request.
     *
     * @param string $store The ID of the store to delete
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
            method: RequestMethod::DELETE,
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
