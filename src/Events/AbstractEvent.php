<?php

declare(strict_types=1);

namespace OpenFGA\Events;

use DateTimeImmutable;
use Override;

use function uniqid;

/**
 * Base implementation for domain events.
 *
 * Provides common functionality for all events including unique ID generation,
 * timestamp capture, and propagation control.
 */
abstract class AbstractEvent implements EventInterface
{
    private readonly string $eventId;

    private readonly DateTimeImmutable $occurredAt;

    private bool $propagationStopped = false;

    /**
     * @param array<string, mixed> $payload Event-specific data
     */
    public function __construct(
        private readonly array $payload = [],
    ) {
        $this->eventId = uniqid('event_', true);
        $this->occurredAt = new DateTimeImmutable;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getEventId(): string
    {
        return $this->eventId;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getEventType(): string
    {
        return static::class;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getOccurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}
