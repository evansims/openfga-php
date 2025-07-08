<?php

declare(strict_types=1);

namespace OpenFGA\Context;

use OpenFGA\ClientInterface;
use OpenFGA\Models\{AuthorizationModelInterface, StoreInterface};
use RuntimeException;
use Throwable;

/**
 * Manages ambient context for OpenFGA operations.
 *
 * This interface provides a way to manage and access contextual information
 * (client, store, and model) throughout the execution of OpenFGA operations.
 * It implements a stack-based context system that allows nested contexts.
 */
interface ContextInterface
{
    /**
     * Get the current ambient context.
     *
     * @throws RuntimeException when no context is active
     */
    public static function current(): self;

    /**
     * Get the current nesting depth of contexts.
     *
     * @return int The number of active contexts in the stack
     */
    public static function depth(): int;

    /**
     * Get the current client.
     *
     * @return ClientInterface|null The current client instance or null if not set
     */
    public static function getClient(): ?ClientInterface;

    /**
     * Get the current authorization model.
     *
     * @return AuthorizationModelInterface|string|null The current model instance, model ID, or null if not set
     */
    public static function getModel(): AuthorizationModelInterface | string | null;

    /**
     * Get the previous context in the stack.
     */
    public static function getPrevious(): ?self;

    /**
     * Get the current store.
     *
     * @return StoreInterface|string|null The current store instance, store ID, or null if not set
     */
    public static function getStore(): StoreInterface | string | null;

    /**
     * Check if an ambient context is currently active.
     *
     * @return bool True if at least one context is active, false otherwise
     */
    public static function hasContext(): bool;

    /**
     * Execute a callable within a new ambient context.
     *
     * @template T
     *
     * @param callable(): T                           $fn     The callable to execute within the context
     * @param ?ClientInterface                        $client Optional client for the context
     * @param StoreInterface|string|null              $store  Optional store for the context
     * @param AuthorizationModelInterface|string|null $model  Optional model for the context
     *
     * @throws Throwable Re-throws any exception from the callable
     *
     * @return T The result of the callable execution
     */
    public static function with(
        callable $fn,
        ?ClientInterface $client = null,
        StoreInterface | string | null $store = null,
        AuthorizationModelInterface | string | null $model = null,
    ): mixed;
}
