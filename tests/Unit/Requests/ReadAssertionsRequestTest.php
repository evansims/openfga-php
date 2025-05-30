<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Requests;

use OpenFGA\Exceptions\ClientException;
use OpenFGA\Network\RequestMethod;
use OpenFGA\Requests\{ReadAssertionsRequest, ReadAssertionsRequestInterface};
use Psr\Http\Message\StreamFactoryInterface;

describe('ReadAssertionsRequest', function (): void {
    beforeEach(function (): void {
        $this->streamFactory = $this->createMock(StreamFactoryInterface::class);
    });

    test('implements ReadAssertionsRequestInterface', function (): void {
        $request = new ReadAssertionsRequest('store', 'model');
        expect($request)->toBeInstanceOf(ReadAssertionsRequestInterface::class);
    });

    test('constructs with required parameters', function (): void {
        $request = new ReadAssertionsRequest(
            store: 'test-store-id',
            model: 'model-id-123',
        );

        expect($request->getStore())->toBe('test-store-id');
        expect($request->getModel())->toBe('model-id-123');
    });

    test('getRequest returns RequestContext', function (): void {
        $request = new ReadAssertionsRequest(
            store: 'test-store',
            model: 'model-xyz',
        );

        $context = $request->getRequest($this->streamFactory);

        expect($context->getMethod())->toBe(RequestMethod::GET);
        expect($context->getUrl())->toBe('/stores/test-store/assertions/model-xyz');
        expect($context->getBody())->toBeNull();
        expect($context->useApiUrl())->toBeTrue();
    });

    test('handles UUID format IDs', function (): void {
        $storeId = '550e8400-e29b-41d4-a716-446655440000';
        $modelId = '660e8400-e29b-41d4-a716-446655440001';

        $request = new ReadAssertionsRequest(
            store: $storeId,
            model: $modelId,
        );

        $context = $request->getRequest($this->streamFactory);

        expect($context->getUrl())->toBe("/stores/{$storeId}/assertions/{$modelId}");
    });

    test('handles special characters in IDs', function (): void {
        $request = new ReadAssertionsRequest(
            store: 'store-with-special_chars.123',
            model: 'model-with-special_chars.456',
        );

        $context = $request->getRequest($this->streamFactory);

        expect($context->getUrl())->toBe('/stores/store-with-special_chars.123/assertions/model-with-special_chars.456');
    });

    test('preserves exact parameter values', function (): void {
        $request = new ReadAssertionsRequest(
            store: '  store-with-spaces  ',
            model: '  model-with-spaces  ',
        );

        expect($request->getStore())->toBe('  store-with-spaces  ');
        expect($request->getModel())->toBe('  model-with-spaces  ');

        $context = $request->getRequest($this->streamFactory);
        expect($context->getUrl())->toBe('/stores/  store-with-spaces  /assertions/  model-with-spaces  ');
    });

    test('throws when store is empty', function (): void {
        new ReadAssertionsRequest(store: '', model: 'model');
    })->throws(ClientException::class);

    test('throws when model is empty', function (): void {
        new ReadAssertionsRequest(store: 'store', model: '');
    })->throws(ClientException::class);
});
