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

    /**
     * Get the exception if the operation failed.
     *
     * @return Throwable|null The exception or null if the operation succeeded
     */
    public function getException(): ?Throwable
    {
        return $this->exception;
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
     * Get the result of the operation.
     *
     * @return mixed The operation result (typically a Response object) or null if failed
     */
    public function getResult(): mixed
    {
        return $this->result;
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

    /**
     * Check if the operation completed successfully.
     *
     * @return bool True if the operation succeeded, false otherwise
     */
    public function isSuccessful(): bool
    {
        return $this->success;
    }
}
