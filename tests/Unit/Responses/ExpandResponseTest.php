<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Responses;

use OpenFGA\Exceptions\{NetworkException, SerializationException};
use OpenFGA\Models\{Node, UsersetTree};
use OpenFGA\Responses\{ExpandResponse, ExpandResponseInterface};
use OpenFGA\Schemas\{SchemaInterface, SchemaValidator};
use OpenFGA\Tests\Support\Responses\SimpleResponse;
use Psr\Http\Message\RequestInterface;

describe('ExpandResponse', function (): void {
    test('implements ExpandResponseInterface', function (): void {
        $response = new ExpandResponse;
        expect($response)->toBeInstanceOf(ExpandResponseInterface::class);
    });

    test('constructs with null tree', function (): void {
        $response = new ExpandResponse;
        expect($response->getTree())->toBeNull();
    });

    test('constructs with UsersetTree', function (): void {
        $root = new Node(name: 'viewer');
        $tree = new UsersetTree($root);

        $response = new ExpandResponse($tree);
        expect($response->getTree())->toBe($tree);
    });

    test('schema returns expected structure', function (): void {
        $schema = ExpandResponse::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(ExpandResponse::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(1);
        expect($properties['tree']->name)->toBe('tree');
        expect($properties['tree']->type)->toBe('object');
        expect($properties['tree']->required)->toBeFalse();
    });

    test('schema is cached', function (): void {
        $schema1 = ExpandResponse::schema();
        $schema2 = ExpandResponse::schema();

        expect($schema1)->toBe($schema2);
    });

    test('handles complex tree structure', function (): void {
        $root = new Node(name: 'document:budget#viewer');
        $tree = new UsersetTree($root);

        $response = new ExpandResponse($tree);

        expect($response->getTree())->toBe($tree);
        expect($response->getTree()->getRoot())->toBe($root);
    });

    test('fromResponse handles error responses with non-200 status', function (): void {
        $httpResponse = new SimpleResponse(400, json_encode(['code' => 'invalid_request', 'message' => 'Bad request']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        ExpandResponse::fromResponse($httpResponse, $request, $validator);
    })->throws(NetworkException::class);

    test('fromResponse handles 401 unauthorized', function (): void {
        $httpResponse = new SimpleResponse(401, json_encode(['code' => 'unauthenticated', 'message' => 'Invalid credentials']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        ExpandResponse::fromResponse($httpResponse, $request, $validator);
    })->throws(NetworkException::class);

    test('fromResponse handles 403 forbidden', function (): void {
        $httpResponse = new SimpleResponse(403, json_encode(['code' => 'forbidden', 'message' => 'Access denied']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        ExpandResponse::fromResponse($httpResponse, $request, $validator);
    })->throws(NetworkException::class);

    test('fromResponse handles 500 internal server error', function (): void {
        $httpResponse = new SimpleResponse(500, json_encode(['code' => 'internal_error', 'message' => 'Server error']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        ExpandResponse::fromResponse($httpResponse, $request, $validator);
    })->throws(NetworkException::class);

    test('fromResponse handles network timeout', function (): void {
        $httpResponse = new SimpleResponse(504, json_encode(['code' => 'timeout', 'message' => 'Gateway timeout']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        ExpandResponse::fromResponse($httpResponse, $request, $validator);
    })->throws(NetworkException::class);

    test('fromResponse handles successful response with valid tree data', function (): void {
        $responseData = [
            'tree' => [
                'root' => [
                    'name' => 'document:budget#viewer',
                ],
            ],
        ];

        $httpResponse = new SimpleResponse(200, json_encode($responseData));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        $response = ExpandResponse::fromResponse($httpResponse, $request, $validator);

        expect($response)->toBeInstanceOf(ExpandResponse::class);
        expect($response->getTree())->toBeInstanceOf(UsersetTree::class);
    });

    test('fromResponse handles successful response with empty tree', function (): void {
        $responseData = ['tree' => null];

        $httpResponse = new SimpleResponse(200, json_encode($responseData));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        $response = ExpandResponse::fromResponse($httpResponse, $request, $validator);

        expect($response)->toBeInstanceOf(ExpandResponse::class);
        expect($response->getTree())->toBeNull();
    });

    test('fromResponse registers all required schemas for validation', function (): void {
        $responseData = ['tree' => null];

        $httpResponse = new SimpleResponse(200, json_encode($responseData));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        // Verify schemas are registered during processing
        $response = ExpandResponse::fromResponse($httpResponse, $request, $validator);

        expect($response)->toBeInstanceOf(ExpandResponse::class);
        // Schema registration happens internally and is tested by successful validation
    });

    test('fromResponse handles malformed JSON response', function (): void {
        $httpResponse = new SimpleResponse(200, 'invalid json');
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        ExpandResponse::fromResponse($httpResponse, $request, $validator);
    })->throws(SerializationException::class);

    test('getTree returns exactly what was set in constructor', function (): void {
        $root = new Node(name: 'viewer');
        $tree = new UsersetTree($root);
        $response = new ExpandResponse($tree);

        expect($response->getTree())->toBe($tree);
        expect($response->getTree())->not()->toBeNull();
    });

    test('schema property has correct UsersetTree class reference', function (): void {
        $schema = ExpandResponse::schema();
        $properties = $schema->getProperties();

        expect($properties['tree']->className)->toBe(UsersetTree::class);
        expect($properties['tree']->getClassName())->toBe(UsersetTree::class);
    });
});
