<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;
use ReflectionException;

use function is_bool;
use function is_object;

/**
 * Represents the result of a single check within a batch check response.
 *
 * Each result contains whether the check was allowed and any error information
 * if the check failed to complete successfully.
 *
 * @see BatchCheckSingleResultInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Relationship%20Queries/BatchCheck
 */
final class BatchCheckSingleResult implements BatchCheckSingleResultInterface
{
    public const string OPENAPI_MODEL = 'BatchCheckSingleResult';

    private static ?SchemaInterface $schema = null;

    /**
     * Create a new batch check single result.
     *
     * @param ?bool   $allowed Whether this check was allowed
     * @param ?object $error   Error information if the check failed
     */
    public function __construct(
        private readonly ?bool $allowed = null,
        private readonly ?object $error = null,
    ) {
    }

    /**
     * @inheritDoc
     *
     * @param array<string, mixed> $data
     *
     * @throws InvalidArgumentException If the provided data structure is invalid
     * @throws ReflectionException      If schema reflection fails
     */
    public static function fromArray(array $data): static
    {
        $allowed = null;

        if (isset($data['allowed']) && is_bool($data['allowed'])) {
            $allowed = $data['allowed'];
        }

        $error = null;

        if (isset($data['error']) && is_object($data['error'])) {
            $error = $data['error'];
        }

        return new self(
            allowed: $allowed,
            error: $error,
        );
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException If schema reflection fails
     */
    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'allowed', type: 'boolean', required: false),
                new SchemaProperty(name: 'error', type: 'object', required: false),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getAllowed(): ?bool
    {
        return $this->allowed;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getError(): ?object
    {
        return $this->error;
    }

    /**
     * @inheritDoc
     *
     * @return array<string, mixed>
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return array_filter([
            'allowed' => $this->allowed,
            'error' => $this->error,
        ], static fn ($value): bool => null !== $value);
    }

    /**
     * @inheritDoc
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        if (null !== $this->allowed) {
            $data['allowed'] = $this->allowed;
        }

        if (null !== $this->error) {
            $data['error'] = $this->error;
        }

        return $data;
    }
}
