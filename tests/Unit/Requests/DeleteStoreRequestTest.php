<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Requests;

use OpenFGA\Exceptions\ClientException;
use OpenFGA\Network\RequestMethod;
use OpenFGA\Requests\DeleteStoreRequest;
use Psr\Http\Message\StreamFactoryInterface;

describe('DeleteStoreRequest', function (): void {
    test('can be instantiated', function (): void {
        $request = new DeleteStoreRequest(store: 'test-store');

        expect($request)->toBeInstanceOf(DeleteStoreRequest::class);
        expect($request->getStore())->toBe('test-store');
    });

    test('generates correct request context', function (): void {
        $streamFactory = test()->createMock(StreamFactoryInterface::class);

        $request = new DeleteStoreRequest(store: 'store-to-delete');
        $context = $request->getRequest($streamFactory);

        expect($context->getMethod())->toBe(RequestMethod::DELETE);
        expect($context->getUrl())->toBe('/stores/store-to-delete');
        expect($context->getBody())->toBeNull();
        expect($context->getHeaders())->toBe([]);
    });

    test('handles store IDs with special characters', function (): void {
        $streamFactory = test()->createMock(StreamFactoryInterface::class);

        $storeId = 'store-123-with-special_chars';
        $request = new DeleteStoreRequest(store: $storeId);
        $context = $request->getRequest($streamFactory);

        expect($request->getStore())->toBe($storeId);
        expect($context->getMethod())->toBe(RequestMethod::DELETE);
        expect($context->getUrl())->toBe('/stores/' . $storeId);
    });

    test('throws when store ID is empty', function (): void {
        new DeleteStoreRequest(store: '');
    })->throws(ClientException::class);
});
