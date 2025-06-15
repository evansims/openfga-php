<?php

declare(strict_types=1);

namespace OpenFGA\Context;

use OpenFGA\ClientInterface;
use OpenFGA\Models\{AuthorizationModelInterface, StoreInterface};
use Override;
use RuntimeException;

/**
 * Ambient Context Manager.
 *
 * Provides Python-style context management for PHP, allowing functions
 * to access shared context without explicit parameter passing. This enables
 * a more ergonomic API where client, store, and model can be set once and
 * used implicitly by helper functions.
 *
 * Contexts support inheritance - child contexts automatically inherit values
 * from their parent context unless explicitly overridden. This allows for
 * flexible nesting where you can override just the pieces you need.
 *
 * @example Basic usage with all parameters
 * context(function() {
 *     // All helper functions can now omit client/store/model parameters
 *     $allowed = allowed(tuple: tuple('user:anne', 'viewer', 'doc:1'));
 *     $users = users('doc:1', 'viewer', filter('user'));
 *     write(tuple('user:bob', 'editor', 'doc:1'));
 * }, client: $client, store: $store, model: $model);
 * @example Nested contexts with inheritance
 * context(function() {
 *     // Uses outer context's client and store
 *     $users1 = users('doc:1', 'viewer', filter('user')); // Uses model1
 *
 *     context(function() {
 *         // Inherits client/store, but uses different model
 *         $users2 = users('doc:2', 'editor', filter('user')); // Uses model2
 *     }, model: $model2);
 *
 * }, client: $client, store: $store, model: $model1);
 * @example Partial context override
 * context(function() {
 *     // Set base client and store
 *     context(function() {
 *         // Override just the store for this operation
 *         $allowed = allowed(tuple: tuple('user:anne', 'admin', 'store:settings'));
 *     }, store: $adminStore);
 * }, client: $client, store: $userStore, model: $model);
 * @example Error handling
 * try {
 *     $result = context(function() {
 *         // If no context is active and helpers are called without params,
 *         // they will throw descriptive exceptions
 *         return allowed(tuple: tuple('user:test', 'viewer', 'doc:1'));
 *     }, client: $client); // Missing store/model will cause error
 * } catch (ClientException $e) {
 *     // Handle missing context parameters
 * }
 */
final class Context implements ContextInterface
{
    private static ?self $current = null;

    private static int $depth = 0;

    private function __construct(
        private readonly ?ClientInterface $client = null,
        private readonly StoreInterface | string | null $store = null,
        private readonly AuthorizationModelInterface | string | null $model = null,
        private readonly ?self $previous = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function current(): self
    {
        return self::$current ?? throw new RuntimeException('No context is currently active. Wrap your code in Context::with() or use the context helper method.');
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function depth(): int
    {
        return self::$depth;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function getClient(): ?ClientInterface
    {
        return self::$current?->client;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function getModel(): AuthorizationModelInterface | string | null
    {
        return self::$current?->model;
    }

    /**
     * Get the previous context in the stack.
     */
    #[Override]
    public static function getPrevious(): ?self
    {
        return self::$current?->previous;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function getStore(): StoreInterface | string | null
    {
        return self::$current?->store;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function hasContext(): bool
    {
        return self::$current instanceof self;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function with(
        callable $fn,
        ?ClientInterface $client = null,
        StoreInterface | string | null $store = null,
        AuthorizationModelInterface | string | null $model = null,
    ): mixed {
        $previous = self::$current;

        // Inherit from parent context if values not explicitly provided
        self::$current = new self(
            client: $client ?? $previous?->client,
            store: $store ?? $previous?->store,
            model: $model ?? $previous?->model,
            previous: $previous,
        );

        ++self::$depth;

        try {
            return $fn();
        } finally {
            --self::$depth;
            self::$current = $previous;
        }
    }
}
