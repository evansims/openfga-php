<?php

declare(strict_types=1);

use OpenFGA\Client;

it('creates and deletes a store', function () {
    $url = getenv('FGA_API_URL') ?: 'http://localhost:8080';
    $client = new Client(url: $url);

    $name = 'php-sdk-test-' . bin2hex(random_bytes(5));
    $response = $client->createStore(name: $name);
    expect($response->getId())->not()->toBe('');

    $delete = $client->deleteStore(store: $response->getId());
    expect($delete)->toBeInstanceOf(\OpenFGA\Responses\DeleteStoreResponseInterface::class);
});
