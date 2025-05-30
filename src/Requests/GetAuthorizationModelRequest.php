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
 * Request for retrieving a specific authorization model by its ID.
 *
 * This request fetches the complete definition of an authorization model,
 * including all type definitions, relations, and conditions. It's useful for
 * inspecting model configurations, debugging, and model management.
 *
 * @see GetAuthorizationModelRequestInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Authorization%20Models/GetAuthorizationModel Get authorization model API endpoint
 */
final readonly class GetAuthorizationModelRequest implements GetAuthorizationModelRequestInterface
{
    /**
     * Create a new authorization model retrieval request.
     *
     * @param string $store The ID of the store containing the authorization model
     * @param string $model The ID of the authorization model to retrieve
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
            url: '/stores/' . $this->store . '/authorization-models/' . $this->model,
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
