<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use const JSON_THROW_ON_ERROR;

use InvalidArgumentException;
use JsonException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Messages;
use OpenFGA\Models\AssertionInterface;
use OpenFGA\Models\Collections\AssertionsInterface;
use OpenFGA\Network\{RequestContext, RequestMethod};
use OpenFGA\Translation\Translator;
use Override;
use Psr\Http\Message\StreamFactoryInterface;
use ReflectionException;

/**
 * Request for writing test assertions to validate authorization model behavior.
 *
 * This request stores test assertions that define expected authorization outcomes
 * for specific scenarios. Assertions are used to validate that authorization models
 * behave correctly and can be run as part of testing and validation workflows.
 *
 * @see WriteAssertionsRequestInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Assertions/WriteAssertions Write assertions API endpoint
 */
final readonly class WriteAssertionsRequest implements WriteAssertionsRequestInterface
{
    /**
     * Create a new assertions writing request.
     *
     * @param AssertionsInterface<AssertionInterface> $assertions The collection of assertions to write
     * @param string                                  $store      The ID of the store to write assertions to
     * @param string                                  $model      The ID of the authorization model to write assertions for
     *
     * @throws ClientThrowable          If the store ID or model ID is empty
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public function __construct(
        private AssertionsInterface $assertions,
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
    public function getAssertions(): AssertionsInterface
    {
        return $this->assertions;
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
     *
     * @throws JsonException
     */
    #[Override]
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = ['assertions' => $this->assertions->jsonSerialize()];

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::PUT,
            url: '/stores/' . $this->store . '/assertions/' . $this->model,
            body: $stream,
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
