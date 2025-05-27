<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Integration;

use OpenFGA\Models\Collections\Tuples;
use OpenFGA\Responses\ReadTuplesResponse;

test('ReadTuplesResponse constructor accepts camelCase parameter but schema defines snake_case', function (): void {
    // Create a ReadTuplesResponse directly with constructor to show it works
    $response = new ReadTuplesResponse(
        tuples: new Tuples([]),
        continuationToken: 'test_token_123',
    );

    expect($response)->toBeInstanceOf(ReadTuplesResponse::class);
    expect($response->getContinuationToken())->toBe('test_token_123');
    expect($response->getTuples())->toBeInstanceOf(Tuples::class);

    // Show that the schema defines snake_case property
    $schema = ReadTuplesResponse::schema();
    $properties = $schema->getProperties();

    $hasContinuationToken = false;
    foreach ($properties as $property) {
        if ('continuation_token' === $property->name) {
            $hasContinuationToken = true;

            break;
        }
    }

    expect($hasContinuationToken)->toBeTrue();
});
