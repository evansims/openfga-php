<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Models\TupleChangesInterface;
use OpenFGA\Models\TupleChanges;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function assert;
use function is_array;

final class ListChangesResponse extends Response
{
    public function __construct(
        public TupleChangesInterface $changes,
        public ?string $continuationToken,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'changes' => $this->changes,
            'continuation_token' => $this->continuationToken,
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return static
     */
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

        if (200 === $response->getStatusCode() && isset($data['changes']) && is_array($data['changes'])) {
            return static::fromArray($data);
        }

        Response::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
