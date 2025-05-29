<?php

declare(strict_types=1);

use OpenFGA\Exceptions\SerializationException;
use OpenFGA\Responses\Response;
use Psr\Http\Message\{RequestInterface, ResponseInterface, StreamInterface};

// Create a concrete test class since Response is abstract
final class ResponseTest extends Response
{
    public static function parseResponsePublic(ResponseInterface $response, RequestInterface $request): array
    {
        return parent::parseResponse($response, $request);
    }
}

describe('Response', function (): void {
    beforeEach(function (): void {
        $this->stream = $this->createMock(StreamInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
        $this->request = $this->createMock(RequestInterface::class);

        $this->response->method('getBody')->willReturn($this->stream);
    });

    test('parseResponse handles valid JSON', function (): void {
        $jsonData = ['key' => 'value', 'number' => 123, 'array' => [1, 2, 3]];
        $this->stream->method('__toString')->willReturn(json_encode($jsonData));

        $result = ResponseTest::parseResponsePublic($this->response, $this->request);

        expect($result)->toBe($jsonData);
    });

    test('parseResponse handles empty JSON object', function (): void {
        $this->stream->method('__toString')->willReturn('{}');

        $result = ResponseTest::parseResponsePublic($this->response, $this->request);

        expect($result)->toBe([]);
    });

    test('parseResponse handles empty JSON array', function (): void {
        $this->stream->method('__toString')->willReturn('[]');

        $result = ResponseTest::parseResponsePublic($this->response, $this->request);

        expect($result)->toBe([]);
    });

    test('parseResponse handles complex nested JSON', function (): void {
        $complexData = [
            'user' => [
                'id' => 123,
                'name' => 'John Doe',
                'permissions' => ['read', 'write'],
                'metadata' => [
                    'created_at' => '2024-01-01T10:00:00Z',
                    'tags' => ['admin', 'user'],
                ],
            ],
            'count' => 42,
        ];

        $this->stream->method('__toString')->willReturn(json_encode($complexData));

        $result = ResponseTest::parseResponsePublic($this->response, $this->request);

        expect($result)->toBe($complexData);
    });

    test('parseResponse handles JSON with null values', function (): void {
        $dataWithNulls = [
            'name' => 'Test',
            'description' => null,
            'optional_field' => null,
            'active' => true,
        ];

        $this->stream->method('__toString')->willReturn(json_encode($dataWithNulls));

        $result = ResponseTest::parseResponsePublic($this->response, $this->request);

        expect($result)->toBe($dataWithNulls);
    });

    test('parseResponse handles empty string response', function (): void {
        $this->stream->method('__toString')->willReturn('');

        $this->expectException(SerializationException::class);
        ResponseTest::parseResponsePublic($this->response, $this->request);
    });

    test('parseResponse handles invalid JSON', function (): void {
        $this->stream->method('__toString')->willReturn('{"invalid": json}');

        $this->expectException(SerializationException::class);
        ResponseTest::parseResponsePublic($this->response, $this->request);
    });

    test('parseResponse handles malformed JSON', function (): void {
        $this->stream->method('__toString')->willReturn('{"unclosed": "object"');

        $this->expectException(SerializationException::class);
        ResponseTest::parseResponsePublic($this->response, $this->request);
    });

    test('parseResponse handles JSON with trailing comma', function (): void {
        $this->stream->method('__toString')->willReturn('{"key": "value",}');

        $this->expectException(SerializationException::class);
        ResponseTest::parseResponsePublic($this->response, $this->request);
    });

    test('parseResponse handles non-array JSON types', function (): void {
        // JSON string
        $this->stream->method('__toString')->willReturn('"just a string"');

        $result = ResponseTest::parseResponsePublic($this->response, $this->request);
        expect($result)->toBe([]);

        // JSON number
        $this->stream->method('__toString')->willReturn('42');

        $result = ResponseTest::parseResponsePublic($this->response, $this->request);
        expect($result)->toBe([]);

        // JSON boolean
        $this->stream->method('__toString')->willReturn('true');

        $result = ResponseTest::parseResponsePublic($this->response, $this->request);
        expect($result)->toBe([]);

        // JSON null
        $this->stream->method('__toString')->willReturn('null');

        $result = ResponseTest::parseResponsePublic($this->response, $this->request);
        expect($result)->toBe([]);
    });

    test('parseResponse handles UTF-8 encoded JSON', function (): void {
        $unicodeData = [
            'message' => 'Hello 世界',
            'emoji' => '🚀',
            'special_chars' => 'àáâãäåæçèéêë',
        ];

        $this->stream->method('__toString')->willReturn(json_encode($unicodeData, JSON_UNESCAPED_UNICODE));

        $result = ResponseTest::parseResponsePublic($this->response, $this->request);

        expect($result)->toBe($unicodeData);
    });

    test('parseResponse handles large JSON response', function (): void {
        $largeData = [];
        for ($i = 0; $i < 1000; ++$i) {
            $largeData["item_{$i}"] = [
                'id' => $i,
                'name' => "Item {$i}",
                'data' => str_repeat('x', 100),
            ];
        }

        $this->stream->method('__toString')->willReturn(json_encode($largeData));

        $result = ResponseTest::parseResponsePublic($this->response, $this->request);

        expect($result)->toHaveCount(1000);
        expect($result['item_0'])->toBe(['id' => 0, 'name' => 'Item 0', 'data' => str_repeat('x', 100)]);
        expect($result['item_999'])->toBe(['id' => 999, 'name' => 'Item 999', 'data' => str_repeat('x', 100)]);
    });
});
