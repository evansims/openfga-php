<?php

declare(strict_types=1);

namespace OpenFGA\Context;

use OpenFGA\ClientInterface;
use OpenFGA\Models\{AuthorizationModelInterface, StoreInterface};
use RuntimeException;

/**
 * Ambient Context Manager.
 *
 * Provides Python-style context management for PHP, allowing functions
 * to access shared context without explicit parameter passing.
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
    public static function current(): self
    {
        return self::$current ?? throw new RuntimeException('No context is currently active. Wrap your code in Context::with() or use the context helper method.');
    }

    /**
     * @inheritDoc
     */
    public static function depth(): int
    {
        return self::$depth;
    }

    /**
     * @inheritDoc
     */
    public static function getClient(): ?ClientInterface
    {
        return self::$current?->client;
    }

    /**
     * @inheritDoc
     */
    public static function getModel(): ?AuthorizationModelInterface
    {
        return self::$current?->model;
    }

    /**
     * @inheritDoc
     */
    public static function getStore(): ?StoreInterface
    {
        return self::$current?->store;
    }

    /**
     * @inheritDoc
     */
    public static function hasContext(): bool
    {
        return self::$current instanceof self;
    }

    /**
     * @inheritDoc
     */
    public static function with(
        callable $fn,
        ?ClientInterface $client = null,
        StoreInterface | string | null $store = null,
        AuthorizationModelInterface | string | null $model = null,
    ): mixed {
        $previous = self::$current;

        self::$current = new self(
            client: $client,
            store: $store,
            model: $model,
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
