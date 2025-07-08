<?php

declare(strict_types=1);

namespace OpenFGA\Events;

/**
 * Event fired when a high-level operation starts.
 *
 * This event tracks business operations like check, expand, writeTuples, etc.
 */
final class OperationStartedEvent extends AbstractEvent
{
    /**
     * @param array<string, mixed> $context
     * @param string               $operation
     * @param ?string              $storeId
     * @param ?string              $modelId
     */
    public function __construct(
        private readonly string $operation,
        private readonly ?string $storeId = null,
        private readonly ?string $modelId = null,
        private readonly array $context = [],
    ) {
        parent::__construct([
            'operation' => $this->operation,
            'store_id' => $this->storeId,
            'model_id' => $this->modelId,
            'context' => $this->context,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Get the model ID for the operation.
     *
     * @return string|null The model ID or null if not applicable
     */
    public function getModelId(): ?string
    {
        return $this->modelId;
    }

    /**
     * Get the OpenFGA operation name.
     *
     * @return string The operation name (for example, 'check', 'write', 'read')
     */
    public function getOperation(): string
    {
        return $this->operation;
    }

    /**
     * Get the store ID for the operation.
     *
     * @return string|null The store ID or null if not applicable
     */
    public function getStoreId(): ?string
    {
        return $this->storeId;
    }
}
