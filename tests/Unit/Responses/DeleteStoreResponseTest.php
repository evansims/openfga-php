<?php

declare(strict_types=1);

use OpenFGA\Responses\{DeleteStoreResponse, DeleteStoreResponseInterface};

test('DeleteStoreResponse implements DeleteStoreResponseInterface', function (): void {
    $response = new DeleteStoreResponse();
    expect($response)->toBeInstanceOf(DeleteStoreResponseInterface::class);
});

// Note: fromResponse method testing would require integration tests due to SchemaValidator being final
// DeleteStoreResponse is a simple response object with no properties to test

test('DeleteStoreResponse can be instantiated', function (): void {
    $response = new DeleteStoreResponse();
    expect($response)->toBeInstanceOf(DeleteStoreResponse::class);
});
