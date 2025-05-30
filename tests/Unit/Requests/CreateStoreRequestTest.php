<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Requests;

use OpenFGA\Exceptions\ClientException;
use OpenFGA\Messages;
use OpenFGA\Network\RequestMethod;
use OpenFGA\Requests\CreateStoreRequest;
use Psr\Http\Message\{StreamFactoryInterface, StreamInterface};

describe('CreateStoreRequest', function (): void {
    test('can be instantiated', function (): void {
        $request = new CreateStoreRequest(name: 'test-store');

        expect($request)->toBeInstanceOf(CreateStoreRequest::class);
        expect($request->getName())->toBe('test-store');
    });

    test('generates correct request context', function (): void {
        $stream = test()->createMock(StreamInterface::class);

        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $streamFactory->expects(test()->once())
            ->method('createStream')
            ->with(json_encode(['name' => 'my-new-store']))
            ->willReturn($stream);

        $request = new CreateStoreRequest(name: 'my-new-store');
        $context = $request->getRequest($streamFactory);

        expect($context->getMethod())->toBe(RequestMethod::POST);
        expect($context->getUrl())->toBe('/stores/');
        expect($context->getBody())->toBe($stream);
        expect($context->getHeaders())->toBe(['Content-Type' => 'application/json']);
    });

    test('handles store names with special characters', function (): void {
        $storeName = 'test-store-with-special-chars!@#$%^&*()';
        $stream = test()->createMock(StreamInterface::class);

        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $streamFactory->expects(test()->once())
            ->method('createStream')
            ->with(json_encode(['name' => $storeName]))
            ->willReturn($stream);

        $request = new CreateStoreRequest(name: $storeName);
        $context = $request->getRequest($streamFactory);

        expect($request->getName())->toBe($storeName);
        expect($context->getMethod())->toBe(RequestMethod::POST);
        expect($context->getUrl())->toBe('/stores/');
        expect($context->getBody())->toBe($stream);
    });

    test('throws when store name is empty', function (): void {
        new CreateStoreRequest(name: '');
    })->throws(ClientException::class, trans(Messages::REQUEST_STORE_NAME_EMPTY));
});
