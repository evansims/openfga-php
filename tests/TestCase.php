<?php

declare(strict_types=1);

namespace OpenFGA\Tests;

use OpenFGA\Tests\Utilities\{ClientUtilities, HttpUtilities};
use PHPUnit\Framework\TestCase as BaseTestCase;
use PsrMock\Psr18\Contracts\ClientContract;
use OpenFGA\ClientInterface;
use Psr\Http\Message\ResponseFactoryInterface;

abstract class TestCase extends BaseTestCase
{
    protected ClientContract $http;
    protected ResponseFactoryInterface $httpResponseFactory;
    protected ClientInterface $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->http = HttpUtilities::getHttpClient();
        $this->httpResponseFactory = HttpUtilities::getHttpResponseFactory();
        $this->client = ClientUtilities::getClient(httpClient: $this->http);
    }

    protected function mockHttpResponse(
        int $statusCode,
        array | null $body = null,
        array $headers = ['Content-Type' => 'application/json'],
    ): void {
        $this->http->addResponseWildcard(HttpUtilities::createHttpResponse($this->httpResponseFactory, $statusCode, $body, $headers));
    }

    protected function assertLastRequest(
        string $expectedMethod,
        string $expectedPath,
        array | string | null $expectedBody = null,
        array $expectedHeaders = ['Content-Type' => 'application/json'],
    ): void {
        $request = ClientUtilities::getHttpRequest($this->client);

        $this->assertNotNull($request, 'No request was made.');

        $this->assertEquals($expectedMethod, $request->getMethod());

        $uri = $request->getUri();
        $this->assertEquals($this->client->getConfiguration()->apiUrl, $uri->getScheme() . '://' . $uri->getHost());
        $this->assertStringStartsWith($expectedPath, $uri->getPath(), "Request path mismatch.");

        if ($expectedBody !== null) {
            $requestBody = json_decode((string) $request->getBody(), true);
            $this->assertEquals($expectedBody, $requestBody);
        }
    }

    protected function assertLastRequestQueryContains(
        string $key,
        string $value,
    ): void {
        $request = ClientUtilities::getHttpRequest($this->client);

        $this->assertNotNull($request, 'No request was made.');

        parse_str($request->getUri()->getQuery(), $queryParams);
        $this->assertArrayHasKey($key, $queryParams, "Query parameter '{$key}' not found.");
        $this->assertEquals($value, $queryParams[$key], "Query parameter '{$key}' value mismatch.");
    }
}
