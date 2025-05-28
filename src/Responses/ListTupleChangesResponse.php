<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\Collections\{TupleChanges, TupleChangesInterface};
use OpenFGA\Models\TupleChangeInterface;
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Override;

use Psr\Http\Message\{RequestInterface, ResponseInterface};

final class ListTupleChangesResponse extends Response implements ListTupleChangesResponseInterface
{
    private static ?SchemaInterface $schema = null;

    /**
     * @param TupleChangesInterface<TupleChangeInterface> $changes
     * @param ?string                                     $continuationToken
     */
    public function __construct(
        private TupleChangesInterface $changes,
        private ?string $continuationToken,
    ) {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getChanges(): TupleChangesInterface
    {
        return $this->changes;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getContinuationToken(): ?string
    {
        return $this->continuationToken;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public static function fromResponse(
        ResponseInterface $response,
        RequestInterface $request,
        SchemaValidator $validator,
    ): ListTupleChangesResponseInterface {
        // Handle successful responses
        if (200 === $response->getStatusCode()) {
            $data = self::parseResponse($response, $request);

            $validator->registerSchema(TupleChanges::schema());
            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

        // Handle network errors
        return RequestManager::handleResponseException(
            response: $response,
            request: $request,
        );
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'changes', type: 'object', className: TupleChanges::class, required: true),
                new SchemaProperty(name: 'continuation_token', type: 'string', required: false),
            ],
        );
    }
}
