<?php

declare(strict_types=1);

namespace OpenFGA\Context;

use OpenFGA\ClientInterface;
use OpenFGA\Models\{AuthorizationModelInterface, StoreInterface};
use RuntimeException;
use Throwable;

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
     */
    public static function depth(): int;

    /**
     * Get the current client.
     */
    public static function getClient(): ?ClientInterface;

    /**
     * Get the current authorization model.
     */
    public static function getModel(): AuthorizationModelInterface | string | null;

    /**
     * Get the previous context in the stack.
     */
    public static function getPrevious(): ?self;

    /**
     * Get the current store.
     */
    public static function getStore(): StoreInterface | string | null;

    /**
     * Check if an ambient context is currently active.
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
