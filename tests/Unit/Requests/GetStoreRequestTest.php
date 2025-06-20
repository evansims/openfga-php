<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Requests;

use OpenFGA\Exceptions\ClientException;
use OpenFGA\Network\RequestMethod;
use OpenFGA\Requests\GetStoreRequest;
use Psr\Http\Message\StreamFactoryInterface;

describe('GetStoreRequest', function (): void {
    test('can be instantiated', function (): void {
        $request = new GetStoreRequest(store: 'test-store');

        expect($request)->toBeInstanceOf(GetStoreRequest::class);
        expect($request->getStore())->toBe('test-store');
    });

    test('generates correct request context', function (): void {
        $streamFactory = test()->createMock(StreamFactoryInterface::class);

        $request = new GetStoreRequest(store: 'my-store');
        $context = $request->getRequest($streamFactory);

        expect($context->getMethod())->toBe(RequestMethod::GET);
        expect($context->getUrl())->toBe('/stores/my-store');
        expect($context->getBody())->toBeNull();
        expect($context->getHeaders())->toBe([]);
    });

    test('handles store IDs with special characters', function (): void {
        $streamFactory = test()->createMock(StreamFactoryInterface::class);

        $storeId = 'store-123-with-special_chars';
        $request = new GetStoreRequest(store: $storeId);
        $context = $request->getRequest($streamFactory);

        expect($request->getStore())->toBe($storeId);
        expect($context->getMethod())->toBe(RequestMethod::GET);
        expect($context->getUrl())->toBe('/stores/' . $storeId);
    });

    test('throws when store ID is empty', function (): void {
        new GetStoreRequest(store: '');
    })->throws(ClientException::class);
});
