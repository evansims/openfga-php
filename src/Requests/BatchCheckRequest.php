<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use const JSON_THROW_ON_ERROR;

use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Messages;
use OpenFGA\Models\Collections\BatchCheckItemsInterface;
use OpenFGA\Network\{RequestContext, RequestMethod};
use OpenFGA\Translation\Translator;
use Override;
use Psr\Http\Message\StreamFactoryInterface;
use ReflectionException;

/**
 * Request for performing multiple authorization checks in a single batch.
 *
 * This request allows checking multiple user-object relationships simultaneously
 * for better performance when multiple authorization decisions are needed.
 * Each check in the batch has a correlation ID to map results back to the
 * original requests.
 *
 * @see BatchCheckRequestInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Relationship%20Queries/BatchCheck
 */
final readonly class BatchCheckRequest implements BatchCheckRequestInterface
{
    /**
     * @param string                   $store  The store ID
     * @param string                   $model  The authorization model ID
     * @param BatchCheckItemsInterface $checks The batch check items
     *
     * @throws ClientThrowable          If the store ID or model ID is empty
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public function __construct(
        private string $store,
        private string $model,
        private BatchCheckItemsInterface $checks,
    ) {
        if ('' === $this->store) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_STORE_ID_EMPTY), ]);
        }

        if ('' === $this->model) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_MODEL_ID_EMPTY), ]);
        }

        if (0 === $this->checks->count()) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::INVALID_BATCH_CHECK_EMPTY), ]);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getChecks(): BatchCheckItemsInterface
    {
        return $this->checks;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = [
            'authorization_model_id' => $this->model,
            'checks' => $this->checks->jsonSerialize(),
        ];

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->store . '/batch-check',
            body: $stream,
        );
    }
}
