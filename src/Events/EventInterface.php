<?php

declare(strict_types=1);

namespace OpenFGA\Events;

use DateTimeImmutable;

/**
 * Base interface for all domain events.
 *
 * Events represent something significant that happened in the domain.
 * They are immutable value objects that capture the facts about what occurred.
 */
interface EventInterface
{
    /**
     * Get the unique identifier for this event.
     *
     * @return string A unique identifier for the event instance
     */
    public function getEventId(): string;

    /**
     * Get the name/type of this event.
     *
     * @return string The event type identifier
     */
    public function getEventType(): string;

    /**
     * Get when this event occurred.
     *
     * @return DateTimeImmutable The timestamp when the event was created
     */
    public function getOccurredAt(): DateTimeImmutable;

    /**
     * Get the event payload data.
     *
     * @return array<string, mixed> The event data
     */
    public function getPayload(): array;

    /**
     * Check if event propagation should be stopped.
     *
     * @return bool True if propagation should be stopped
     */
    public function isPropagationStopped(): bool;

    /**
     * Stop event propagation to remaining listeners.
     */
    public function stopPropagation(): void;
}
