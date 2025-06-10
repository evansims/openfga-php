<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Events;

use DateTimeImmutable;
use OpenFGA\Events\AbstractEvent;

describe('AbstractEvent', function (): void {
    test('generates unique event IDs', function (): void {
        $event1 = new class extends AbstractEvent {};
        $event2 = new class extends AbstractEvent {};

        expect($event1->getEventId())->not->toBe($event2->getEventId());
        expect($event1->getEventId())->toBeString();
        expect($event2->getEventId())->toBeString();
    });

    test('captures occurrence timestamp', function (): void {
        $before = new DateTimeImmutable;
        $event = new class extends AbstractEvent {};
        $after = new DateTimeImmutable;

        expect($event->getOccurredAt())->toBeInstanceOf(DateTimeImmutable::class);
        expect($event->getOccurredAt()->getTimestamp())->toBeGreaterThanOrEqual($before->getTimestamp());
        expect($event->getOccurredAt()->getTimestamp())->toBeLessThanOrEqual($after->getTimestamp());
    });

    test('uses class name as event type by default', function (): void {
        $event = new class extends AbstractEvent {};

        expect($event->getEventType())->toBe($event::class);
    });

    test('stores and returns payload data', function (): void {
        $payload = ['key' => 'value', 'number' => 42];
        $event = new class($payload) extends AbstractEvent {};

        expect($event->getPayload())->toBe($payload);
    });

    test('defaults to empty payload when none provided', function (): void {
        $event = new class extends AbstractEvent {};

        expect($event->getPayload())->toBe([]);
    });

    test('propagation control works correctly', function (): void {
        $event = new class extends AbstractEvent {};

        expect($event->isPropagationStopped())->toBeFalse();

        $event->stopPropagation();

        expect($event->isPropagationStopped())->toBeTrue();
    });

    test('event with custom payload includes data in payload', function (): void {
        $customData = [
            'operation' => 'check',
            'store_id' => 'store-123',
            'success' => true,
        ];

        $event = new class($customData) extends AbstractEvent {};

        expect($event->getPayload())->toBe($customData);
        expect($event->getPayload()['operation'])->toBe('check');
        expect($event->getPayload()['store_id'])->toBe('store-123');
        expect($event->getPayload()['success'])->toBeTrue();
    });
});
