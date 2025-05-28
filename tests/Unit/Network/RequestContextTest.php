<?php

declare(strict_types=1);

namespace Tests\Unit\Network;

use OpenFGA\Network\RequestContext;
use OpenFGA\Network\RequestMethod;
use Psr\Http\Message\StreamInterface;
use Mockery;

describe('RequestContext', function (): void {
    test('constructs with required parameters', function (): void {
        $context = new RequestContext(
            method: RequestMethod::GET,
            url: '/stores',
        );

        expect($context->getMethod())->toBe(RequestMethod::GET);
        expect($context->getUrl())->toBe('/stores');
        expect($context->getBody())->toBeNull();
        expect($context->getHeaders())->toBe([]);
        expect($context->useApiUrl())->toBeTrue();
    });

    test('constructs with all parameters', function (): void {
        $body = Mockery::mock(StreamInterface::class);
        $headers = ['Content-Type' => 'application/json', 'X-Custom' => 'value'];

        $context = new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/123',
            body: $body,
            headers: $headers,
            useApiUrl: false,
        );

        expect($context->getMethod())->toBe(RequestMethod::POST);
        expect($context->getUrl())->toBe('/stores/123');
        expect($context->getBody())->toBe($body);
        expect($context->getHeaders())->toBe($headers);
        expect($context->useApiUrl())->toBeFalse();
    });

    test('handles different HTTP methods', function (): void {
        $methods = [
            RequestMethod::GET,
            RequestMethod::POST,
            RequestMethod::PUT,
            RequestMethod::DELETE,
        ];

        foreach ($methods as $method) {
            $context = new RequestContext(
                method: $method,
                url: '/test',
            );

            expect($context->getMethod())->toBe($method);
        }
    });

    test('handles various URL formats', function (): void {
        $urls = [
            '/stores',
            '/stores/123',
            '/stores/123/authorization-models',
            'https://external.com/webhook',
            'http://localhost:8080/test',
            '/path/with/trailing/slash/',
            'relative/path',
            '',
        ];

        foreach ($urls as $url) {
            $context = new RequestContext(
                method: RequestMethod::GET,
                url: $url,
            );

            expect($context->getUrl())->toBe($url);
        }
    });

    test('preserves header case and values', function (): void {
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer token123',
            'X-Custom-Header' => 'custom value',
            'x-lowercase' => 'value',
            'X-UPPERCASE' => 'VALUE',
        ];

        $context = new RequestContext(
            method: RequestMethod::POST,
            url: '/test',
            headers: $headers,
        );

        expect($context->getHeaders())->toBe($headers);
    });

    test('handles empty headers array', function (): void {
        $context = new RequestContext(
            method: RequestMethod::GET,
            url: '/test',
            headers: [],
        );

        expect($context->getHeaders())->toBe([]);
    });

    test('useApiUrl defaults to true', function (): void {
        $context = new RequestContext(
            method: RequestMethod::GET,
            url: '/test',
        );

        expect($context->useApiUrl())->toBeTrue();
    });

    test('useApiUrl can be set to false', function (): void {
        $context = new RequestContext(
            method: RequestMethod::GET,
            url: 'https://external.com/webhook',
            useApiUrl: false,
        );

        expect($context->useApiUrl())->toBeFalse();
    });

    test('body can be null', function (): void {
        $context = new RequestContext(
            method: RequestMethod::GET,
            url: '/test',
            body: null,
        );

        expect($context->getBody())->toBeNull();
    });

    test('body accepts StreamInterface', function (): void {
        $body = Mockery::mock(StreamInterface::class);

        $context = new RequestContext(
            method: RequestMethod::POST,
            url: '/test',
            body: $body,
        );

        expect($context->getBody())->toBe($body);
    });

    test('represents typical GET request', function (): void {
        $context = new RequestContext(
            method: RequestMethod::GET,
            url: '/stores/01GXSA8YR785C4FYS3C0RTG7B1/authorization-models',
        );

        expect($context->getMethod())->toBe(RequestMethod::GET);
        expect($context->getUrl())->toBe('/stores/01GXSA8YR785C4FYS3C0RTG7B1/authorization-models');
        expect($context->getBody())->toBeNull();
        expect($context->useApiUrl())->toBeTrue();
    });

    test('represents typical POST request with body', function (): void {
        $body = Mockery::mock(StreamInterface::class);

        $context = new RequestContext(
            method: RequestMethod::POST,
            url: '/stores',
            body: $body,
            headers: [
                'Content-Type' => 'application/json',
            ],
        );

        expect($context->getMethod())->toBe(RequestMethod::POST);
        expect($context->getUrl())->toBe('/stores');
        expect($context->getBody())->toBe($body);
        expect($context->getHeaders())->toHaveKey('Content-Type');
        expect($context->useApiUrl())->toBeTrue();
    });

    test('handles webhook or external URL', function (): void {
        $context = new RequestContext(
            method: RequestMethod::POST,
            url: 'https://webhook.site/unique-id',
            body: null,
            headers: ['X-Event-Type' => 'store.created'],
            useApiUrl: false,
        );

        expect($context->getMethod())->toBe(RequestMethod::POST);
        expect($context->getUrl())->toBe('https://webhook.site/unique-id');
        expect($context->getHeaders())->toBe(['X-Event-Type' => 'store.created']);
        expect($context->useApiUrl())->toBeFalse();
    });
});
