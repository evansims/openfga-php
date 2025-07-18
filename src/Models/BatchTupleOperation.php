<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\{Messages, Translation\Translator};
use OpenFGA\Models\Collections\{TupleKeys, TupleKeysInterface};
use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty};
use Override;
use ReflectionException;

/**
 * Represents a batch tuple operation containing both writes and deletes.
 *
 * This model organizes tuple operations for batch processing, allowing you to
 * specify both tuples to write and tuples to delete in a single operation.
 * The batch processor will automatically chunk these operations to respect
 * API limits while maintaining the grouping of related changes.
 *
 * @see https://openfga.dev/docs/api#/Relationship%20Tuples/Write
 */
final class BatchTupleOperation implements BatchTupleOperationInterface
{
    /**
     * Maximum number of tuples allowed per API request.
     */
    public const int MAX_TUPLES_PER_REQUEST = 100;

    public const string OPENAPI_MODEL = 'BatchTupleOperation';

    private static ?SchemaInterface $schema = null;

    /**
     * Create a new batch tuple operation.
     *
     * @param TupleKeysInterface|null $writes  Collection of tuples to write
     * @param TupleKeysInterface|null $deletes Collection of tuples to delete
     */
    public function __construct(
        private readonly ?TupleKeysInterface $writes = null,
        private readonly ?TupleKeysInterface $deletes = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'writes', type: 'object', className: TupleKeys::class, required: false),
                new SchemaProperty(name: 'deletes', type: 'object', className: TupleKeys::class, required: false),
            ],
        );
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If chunk size is invalid
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public function chunk(int $chunkSize = self::MAX_TUPLES_PER_REQUEST): array
    {
        // Validate chunk size
        if (0 >= $chunkSize) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::BATCH_TUPLE_CHUNK_SIZE_POSITIVE)]);
        }

        if (self::MAX_TUPLES_PER_REQUEST < $chunkSize) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::BATCH_TUPLE_CHUNK_SIZE_EXCEEDED, ['max_size' => self::MAX_TUPLES_PER_REQUEST])]);
        }

        // Empty operations should return empty array
        if ($this->isEmpty()) {
            return [];
        }

        if (! $this->requiresChunking($chunkSize)) {
            return [$this];
        }

        $chunks = [];

        /** @var array<TupleKeyInterface> $writes */
        $writes = $this->writes instanceof TupleKeysInterface ? [...$this->writes] : [];

        /** @var array<TupleKeyInterface> $deletes */
        $deletes = $this->deletes instanceof TupleKeysInterface ? [...$this->deletes] : [];

        while ([] !== $writes || [] !== $deletes) {
            /** @var array<TupleKeyInterface> $chunkWrites */
            $chunkWrites = [];

            /** @var array<TupleKeyInterface> $chunkDeletes */
            $chunkDeletes = [];
            $remaining = $chunkSize;

            // Take writes first, up to the remaining capacity
            while ([] !== $writes && 0 < $remaining) {
                $chunkWrites[] = array_shift($writes);
                --$remaining;
            }

            // Take deletes with remaining capacity
            while ([] !== $deletes && 0 < $remaining) {
                $chunkDeletes[] = array_shift($deletes);
                --$remaining;
            }

            $chunks[] = new self(
                writes: [] !== $chunkWrites ? new TupleKeys($chunkWrites) : null,
                deletes: [] !== $chunkDeletes ? new TupleKeys($chunkDeletes) : null,
            );
        }

        return $chunks;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getDeletes(): ?TupleKeysInterface
    {
        return $this->deletes;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getTotalOperations(): int
    {
        $writeCount = $this->writes?->count() ?? 0;
        $deleteCount = $this->deletes?->count() ?? 0;

        return $writeCount + $deleteCount;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getWrites(): ?TupleKeysInterface
    {
        return $this->writes;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function isEmpty(): bool
    {
        return 0 === $this->getTotalOperations();
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
            'writes' => $this->writes?->jsonSerialize(),
            'deletes' => $this->deletes?->jsonSerialize(),
        ], static fn ($value): bool => null !== $value);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function requiresChunking(int $chunkSize = self::MAX_TUPLES_PER_REQUEST): bool
    {
        return $chunkSize < $this->getTotalOperations();
    }
}
