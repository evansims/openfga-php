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
    public static function getModel(): ?AuthorizationModelInterface;

    /**
     * Get the current store.
     */
    public static function getStore(): ?StoreInterface;

    /**
     * Check if an ambient context is currently active.
     */
    public static function hasContext(): bool;

    /**
     * Execute a callable within a new ambient context.
     *
     * @template T
     *
     * @param callable(): T                                $callback
     * @param array<string, mixed>                         $context
     * @param callable                                     $fn
     * @param ?ClientInterface                             $client
     * @param StoreInterface|string|null|null              $store
     * @param AuthorizationModelInterface|string|null|null $model
     *
     * @throws Throwable Re-throws any exception from the callback
     *
     * @return T
     */
    public static function with(
        callable $fn,
        ?ClientInterface $client = null,
        StoreInterface | string | null $store = null,
        AuthorizationModelInterface | string | null $model = null,
    ): mixed;
}
