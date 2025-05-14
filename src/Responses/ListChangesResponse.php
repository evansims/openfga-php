<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\{ContinuationToken, ContinuationTokenInterface, TupleChanges, TupleChangesInterface};
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function is_array;

final class ListChangesResponse implements ListChangesResponseInterface
{
    use ResponseTrait;

    public function __construct(
        private TupleChangesInterface $changes,
        private ?ContinuationTokenInterface $continuationToken,
    ) {
    }

    public function getChanges(): TupleChangesInterface
    {
        return $this->changes;
    }

    public function getContinuationToken(): ?ContinuationTokenInterface
    {
        return $this->continuationToken;
    }

    public static function fromResponse(HttpResponseInterface $response): static
    {
        $json = (string) $response->getBody();

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new ApiUnexpectedResponseException($e->getMessage());
        }

        if (200 === $response->getStatusCode() && is_array($data) && isset($data['changes']) && is_array($data['changes'])) {
            // @phpstan-ignore-next-line
            $changes = TupleChanges::fromArray($data['changes']);
            // @phpstan-ignore-next-line
            $continuationToken = $data['continuation_token'] ? new ContinuationToken($data['continuation_token']) : null;

            return new self(
                changes: $changes,
                continuationToken: $continuationToken,
            );
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
