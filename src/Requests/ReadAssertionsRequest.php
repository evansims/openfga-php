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
 * Request for reading test assertions associated with an authorization model.
 *
 * This request retrieves the test assertions that have been defined for an
 * authorization model. These assertions help validate model behavior and
 * ensure authorization logic works as expected in different scenarios.
 *
 * @see ReadAssertionsRequestInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Assertions/ReadAssertions Read assertions API endpoint
 */
final readonly class ReadAssertionsRequest implements ReadAssertionsRequestInterface
{
    /**
     * Create a new assertions reading request.
     *
     * @param string $store The ID of the store containing the assertions
     * @param string $model The ID of the authorization model containing the assertions
     *
     * @throws ClientThrowable          If the store ID or model ID is empty
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public function __construct(
        private string $store,
        private string $model,
    ) {
        if ('' === $this->store) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_STORE_ID_EMPTY)]);
        }

        if ('' === $this->model) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_MODEL_ID_EMPTY)]);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        return new RequestContext(
            method: RequestMethod::GET,
            url: '/stores/' . $this->store . '/assertions/' . $this->model,
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
