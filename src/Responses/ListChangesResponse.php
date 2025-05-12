<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\{TupleChanges, TupleChangesInterface};
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function assert;
use function is_array;

final class ListChangesResponse implements ListChangesResponseInterface
{
    use ResponseTrait;

    public function __construct(
        private TupleChangesInterface $changes,
        private ?string $continuationToken,
    ) {
    }

    public function getChanges(): TupleChangesInterface
    {
        return $this->changes;
    }

    public function getContinuationToken(): ?string
    {
        return $this->continuationToken;
    }

    public static function fromArray(array $data): static
    {
        assert(isset($data['changes']) && is_array($data['changes']));

        return new self(
            changes: TupleChanges::fromArray($data['changes']),
            continuationToken: $data['continuation_token'] ?? null,
        );
    }

    public static function fromResponse(HttpResponseInterface $response): static
    {
        $json = (string) $response->getBody();

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new ApiUnexpectedResponseException($e->getMessage());
        }

        if (200 === $response->getStatusCode() && is_array($data)) {
            return static::fromArray($data);
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
