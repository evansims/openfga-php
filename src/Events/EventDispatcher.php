<?php

declare(strict_types=1);

namespace OpenFGA\Events;

use Override;

use function array_key_exists;

/**
 * Simple event dispatcher implementation.
 *
 * Manages event listeners and dispatches events to registered handlers.
 * Supports event propagation control for stoppable events.
 */
final class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var array<string, array<callable(object): void>>
     */
    private array $listeners = [];

    #[Override]
    public function addListener(string $eventType, callable $listener): void
    {
        if (! array_key_exists($eventType, $this->listeners)) {
            $this->listeners[$eventType] = [];
        }

        $this->listeners[$eventType][] = $listener;
    }

    #[Override]
    public function dispatch(EventInterface $event): void
    {
        $eventType = $event->getEventType();

        if (! $this->hasListeners($eventType)) {
            return;
        }

        foreach ($this->listeners[$eventType] as $listener) {
            if ($event->isPropagationStopped()) {
                break;
            }

            $listener($event);
        }
    }

    /**
     * @return array<callable(object): void>
     */
    #[Override]
    public function getListeners(string $eventType): array
    {
        return $this->listeners[$eventType] ?? [];
    }

    #[Override]
    public function hasListeners(string $eventType): bool
    {
        return array_key_exists($eventType, $this->listeners) && [] !== $this->listeners[$eventType];
    }

    #[Override]
    public function removeListeners(string $eventType): void
    {
        unset($this->listeners[$eventType]);
    }
}
