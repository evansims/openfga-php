<?php

declare(strict_types=1);

namespace OpenFGA\Events;

/**
 * Event dispatcher interface for handling domain events.
 *
 * The event dispatcher decouples event publishers from subscribers,
 * allowing for flexible event handling and observability without
 * tight coupling between business logic and infrastructure concerns.
 */
interface EventDispatcherInterface
{
    /**
     * Register an event listener for a specific event type.
     *
     * @param string                 $eventType The class name or identifier of the event to listen for
     * @param callable(object): void $listener  The callable to invoke when the event is dispatched
     */
    public function addListener(string $eventType, callable $listener): void;

    /**
     * Dispatch an event to all registered listeners.
     *
     * Calls all listeners registered for the given event's type.
     * If an event is stoppable and a listener stops propagation,
     * remaining listeners will not be called.
     *
     * @param EventInterface $event The event to dispatch
     */
    public function dispatch(EventInterface $event): void;

    /**
     * Get all registered listeners for a specific event type.
     *
     * @param  string                        $eventType The event type to get listeners for
     * @return array<callable(object): void> Array of listeners for the event type
     */
    public function getListeners(string $eventType): array;

    /**
     * Check if there are any listeners for a specific event type.
     *
     * @param  string $eventType The event type to check
     * @return bool   True if there are listeners, false otherwise
     */
    public function hasListeners(string $eventType): bool;

    /**
     * Remove all listeners for a specific event type.
     *
     * @param string $eventType The event type to clear listeners for
     */
    public function removeListeners(string $eventType): void;
}
