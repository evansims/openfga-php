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
 * Request for listing all available stores with pagination support.
 *
 * This request retrieves a paginated list of stores accessible to the
 * authenticated user or application. It's useful for store selection
 * interfaces, administrative dashboards, and multi-tenant applications.
 *
 * @see ListStoresRequestInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Stores/ListStores List stores API endpoint
 */
final readonly class ListStoresRequest implements ListStoresRequestInterface
{
    /**
     * Create a new stores listing request.
     *
     * @param string|null $continuationToken Token for pagination to get the next page of results
     * @param int|null    $pageSize          Maximum number of stores to return per page
     *
     * @throws ClientThrowable          If the continuation token is empty (but not null)
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public function __construct(
        private ?string $continuationToken = null,
        private ?int $pageSize = null,
    ) {
        if (null !== $this->continuationToken && '' === $this->continuationToken) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_CONTINUATION_TOKEN_EMPTY)]);
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
            url: '/stores' . $query,
        );
    }
}
