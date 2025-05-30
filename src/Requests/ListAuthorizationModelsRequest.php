<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientError};
use OpenFGA\Exceptions\ClientThrowable;
use OpenFGA\Messages;
use OpenFGA\Network\{RequestContext, RequestMethod};
use OpenFGA\Translation\Translator;
use Override;
use Psr\Http\Message\StreamFactoryInterface;
use ReflectionException;

/**
 * Request for listing all authorization models in a store.
 *
 * This request retrieves a paginated list of authorization models, including
 * their IDs and metadata. It's useful for browsing available models, model
 * management interfaces, and selecting models for operations.
 *
 * @see ListAuthorizationModelsRequestInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Authorization%20Models/ListAuthorizationModels List authorization models API endpoint
 */
final readonly class ListAuthorizationModelsRequest implements ListAuthorizationModelsRequestInterface
{
    /**
     * Create a new authorization models listing request.
     *
     * @param string      $store             The ID of the store containing the authorization models
     * @param string|null $continuationToken Token for pagination to get the next page of results
     * @param int|null    $pageSize          Maximum number of models to return per page
     *
     * @throws ClientThrowable          If the store ID is empty
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public function __construct(
        private string $store,
        private ?string $continuationToken = null,
        private ?int $pageSize = null,
    ) {
        if ('' === $this->store) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_STORE_ID_EMPTY)]);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getContinuationToken(): ?string
    {
        return $this->continuationToken;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getPageSize(): ?int
    {
        return $this->pageSize;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $params = array_filter([
            'continuation_token' => $this->getContinuationToken(),
            'page_size' => $this->getPageSize(),
        ], static fn ($v): bool => null !== $v);

        $query = [] !== $params ? '?' . http_build_query($params) : '';

        return new RequestContext(
            method: RequestMethod::GET,
            url: '/stores/' . $this->store . '/authorization-models' . $query,
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
