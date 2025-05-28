<?php

declare(strict_types=1);

use OpenFGA\Responses\{WriteTuplesResponse, WriteTuplesResponseInterface};

test('WriteTuplesResponse implements WriteTuplesResponseInterface', function (): void {
    $response = new WriteTuplesResponse();
    expect($response)->toBeInstanceOf(WriteTuplesResponseInterface::class);
});

test('WriteTuplesResponse can be instantiated', function (): void {
    $response = new WriteTuplesResponse();
    expect($response)->toBeInstanceOf(WriteTuplesResponse::class);
});

test('WriteTuplesResponse creates new instances', function (): void {
    $response1 = new WriteTuplesResponse();
    $response2 = new WriteTuplesResponse();

    expect($response1)->not->toBe($response2);
    expect($response1)->toBeInstanceOf(WriteTuplesResponse::class);
    expect($response2)->toBeInstanceOf(WriteTuplesResponse::class);
});

// Note: fromResponse method testing would require integration tests due to SchemaValidator being final
// WriteTuplesResponse is a simple response object with no properties to test - it represents a successful write operation
