<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Events;

use OpenFGA\Events\{AbstractEvent, EventDispatcher, EventInterface};

describe('EventDispatcher', function (): void {
    test('addListener registers event listeners correctly', function (): void {
        $dispatcher = new EventDispatcher;
        $called = false;

        $listener = function (EventInterface $event) use (&$called): void {
            $called = true;
        };

        $dispatcher->addListener('TestEvent', $listener);

        expect($dispatcher->hasListeners('TestEvent'))->toBeTrue();
        expect($dispatcher->getListeners('TestEvent'))->toHaveCount(1);
        expect($called)->toBeFalse();
    });

    test('dispatch calls registered listeners', function (): void {
        $dispatcher = new EventDispatcher;
        $callCount = 0;

        $listener = function (EventInterface $event) use (&$callCount): void {
            $callCount++;
        };

        $dispatcher->addListener('TestEvent', $listener);
        $dispatcher->addListener('TestEvent', $listener);

        $event = new class extends AbstractEvent {
            public function getEventType(): string
            {
                return 'TestEvent';
            }
        };

        $dispatcher->dispatch($event);

        expect($callCount)->toBe(2);
    });

    test('dispatch stops propagation when event requests it', function (): void {
        $dispatcher = new EventDispatcher;
        $callCount = 0;

        $stoppingListener = function (EventInterface $event) use (&$callCount): void {
            $callCount++;
            $event->stopPropagation();
        };

        $normalListener = function (EventInterface $event) use (&$callCount): void {
            $callCount++;
        };

        $dispatcher->addListener('TestEvent', $stoppingListener);
        $dispatcher->addListener('TestEvent', $normalListener);

        $event = new class extends AbstractEvent {
            public function getEventType(): string
            {
                return 'TestEvent';
            }
        };

        $dispatcher->dispatch($event);

        expect($callCount)->toBe(1);
        expect($event->isPropagationStopped())->toBeTrue();
    });

    test('removeListeners removes all listeners for event type', function (): void {
        $dispatcher = new EventDispatcher;

        $listener = function (EventInterface $event): void {
            // No-op
        };

        $dispatcher->addListener('TestEvent', $listener);
        $dispatcher->addListener('TestEvent', $listener);
        $dispatcher->addListener('OtherEvent', $listener);

        expect($dispatcher->hasListeners('TestEvent'))->toBeTrue();
        expect($dispatcher->hasListeners('OtherEvent'))->toBeTrue();

        $dispatcher->removeListeners('TestEvent');

        expect($dispatcher->hasListeners('TestEvent'))->toBeFalse();
        expect($dispatcher->hasListeners('OtherEvent'))->toBeTrue();
    });

    test('dispatch does nothing when no listeners registered', function (): void {
        $dispatcher = new EventDispatcher;

        $event = new class extends AbstractEvent {
            public function getEventType(): string
            {
                return 'UnknownEvent';
            }
        };

        // Should not throw any exceptions
        $dispatcher->dispatch($event);

        expect(true)->toBeTrue(); // Test passes if no exception thrown
    });

    test('hasListeners returns false for unknown event types', function (): void {
        $dispatcher = new EventDispatcher;

        expect($dispatcher->hasListeners('UnknownEvent'))->toBeFalse();
    });

    test('getListeners returns empty array for unknown event types', function (): void {
        $dispatcher = new EventDispatcher;

        expect($dispatcher->getListeners('UnknownEvent'))->toBe([]);
    });
});
