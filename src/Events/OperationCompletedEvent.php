<?php

declare(strict_types=1);

namespace OpenFGA\Events;

use Throwable;

use function is_object;

/**
 * Event fired when a high-level operation completes.
 *
 * This event tracks the completion of business operations with success/failure information.
 */
final class OperationCompletedEvent extends AbstractEvent
{
    /**
     * @param array<string, mixed> $context
     * @param string               $operation
     * @param bool                 $success
     * @param ?Throwable           $exception
     * @param ?string              $storeId
     * @param ?string              $modelId
     * @param mixed|null           $result
     */
    public function __construct(
        private readonly string $operation,
        private readonly bool $success,
        private readonly ?Throwable $exception = null,
        private readonly ?string $storeId = null,
        private readonly ?string $modelId = null,
        private readonly array $context = [],
        private readonly mixed $result = null,
    ) {
        parent::__construct([
            'operation' => $this->operation,
            'success' => $this->success,
            'store_id' => $this->storeId,
            'model_id' => $this->modelId,
            'context' => $this->context,
            'error_message' => $this->exception?->getMessage(),
            'result_type' => is_object($this->result) ? $this->result::class : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    public function getException(): ?Throwable
    {
        return $this->exception;
    }

    public function getModelId(): ?string
    {
        return $this->modelId;
    }

    public function getOperation(): string
    {
        return $this->operation;
    }

    public function getResult(): mixed
    {
        return $this->result;
    }

    public function getStoreId(): ?string
    {
        return $this->storeId;
    }

    public function isSuccessful(): bool
    {
        return $this->success;
    }
}
