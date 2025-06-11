<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use const JSON_THROW_ON_ERROR;

use InvalidArgumentException;
use JsonException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Messages;
use OpenFGA\Network\{RequestContext, RequestMethod};
use OpenFGA\Translation\Translator;
use Override;
use Psr\Http\Message\StreamFactoryInterface;
use ReflectionException;

/**
 * Request for creating a new OpenFGA store.
 *
 * Stores provide data isolation for different applications or environments,
 * maintaining separate authorization models, relationship tuples, and providing
 * complete separation from other stores.
 *
 * @see CreateStoreRequestInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Stores/CreateStore Store creation API endpoint
 */
final readonly class CreateStoreRequest implements CreateStoreRequestInterface
{
    /**
     * Create a new store creation request.
     *
     * @param string $name The descriptive name for the new authorization store
     *
     * @throws ClientThrowable          If the store name is empty
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public function __construct(
        private string $name,
    ) {
        if ('' === $this->name) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_STORE_NAME_EMPTY)]);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException If the request body cannot be serialized to JSON
     */
    #[Override]
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = [
            'name' => $this->getName(),
        ];

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/',
            body: $stream,
            headers: [
                'Content-Type' => 'application/json',
            ],
        );
    }
}
