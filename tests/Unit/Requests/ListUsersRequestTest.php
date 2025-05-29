<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Requests;

use InvalidArgumentException;
use OpenFGA\Models\Collections\{TupleKeys, UserTypeFilters};
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\{TupleKey, UserTypeFilter};
use OpenFGA\Network\{RequestMethod};
use OpenFGA\Requests\{ListUsersRequest, ListUsersRequestInterface};
use Psr\Http\Message\{StreamFactoryInterface, StreamInterface};

describe('ListUsersRequest', function (): void {
    beforeEach(function (): void {
        $this->stream = $this->createMock(StreamInterface::class);
        $this->streamFactory = $this->createMock(StreamFactoryInterface::class);
        $this->streamFactory->method('createStream')->willReturn($this->stream);
    });

    test('implements ListUsersRequestInterface', function (): void {
        $userFilters = new UserTypeFilters();
        $request = new ListUsersRequest('store', 'model', 'object', 'relation', $userFilters);
        expect($request)->toBeInstanceOf(ListUsersRequestInterface::class);
    });

    test('constructs with required parameters', function (): void {
        $userFilters = new UserTypeFilters(
            new UserTypeFilter('user'),
            new UserTypeFilter('group', 'members'),
        );

        $request = new ListUsersRequest(
            store: 'test-store',
            model: 'model-id-123',
            object: 'document:budget.pdf',
            relation: 'viewer',
            userFilters: $userFilters,
        );

        expect($request->getStore())->toBe('test-store');
        expect($request->getModel())->toBe('model-id-123');
        expect($request->getObject())->toBe('document:budget.pdf');
        expect($request->getRelation())->toBe('viewer');
        expect($request->getUserFilters())->toBe($userFilters);
        expect($request->getContext())->toBeNull();
        expect($request->getContextualTuples())->toBeNull();
        expect($request->getConsistency())->toBeNull();
    });

    test('constructs with all parameters', function (): void {
        $userFilters = new UserTypeFilters(new UserTypeFilter('user'));
        $context = (object) ['ip_address' => '192.168.1.1'];
        $contextualTuples = new TupleKeys(
            new TupleKey('user:anne', 'member', 'group:engineering'),
            new TupleKey('group:engineering', 'viewer', 'document:budget.pdf'),
        );

        $request = new ListUsersRequest(
            store: 'test-store',
            model: 'model-id',
            object: 'document:123',
            relation: 'editor',
            userFilters: $userFilters,
            context: $context,
            contextualTuples: $contextualTuples,
            consistency: Consistency::HIGHER_CONSISTENCY,
        );

        expect($request->getStore())->toBe('test-store');
        expect($request->getModel())->toBe('model-id');
        expect($request->getObject())->toBe('document:123');
        expect($request->getRelation())->toBe('editor');
        expect($request->getUserFilters())->toBe($userFilters);
        expect($request->getContext())->toBe($context);
        expect($request->getContextualTuples())->toBe($contextualTuples);
        expect($request->getConsistency())->toBe(Consistency::HIGHER_CONSISTENCY);
    });

    test('getRequest returns RequestContext with minimal body', function (): void {
        $userFilters = new UserTypeFilters(new UserTypeFilter('user'));

        $request = new ListUsersRequest(
            store: 'test-store',
            model: 'model-123',
            object: 'doc:1',
            relation: 'viewer',
            userFilters: $userFilters,
        );

        $context = $request->getRequest($this->streamFactory);

        expect($context->getMethod())->toBe(RequestMethod::POST);
        expect($context->getUrl())->toBe('/stores/test-store/list-users');
        expect($context->getBody())->toBe($this->stream);
        expect($context->useApiUrl())->toBeTrue();

        $capturedBody = null;
        $this->streamFactory->expects($this->once())
            ->method('createStream')
            ->with($this->callback(function ($body) use (&$capturedBody) {
                $capturedBody = json_decode($body, true);

                return true;
            }));

        $request->getRequest($this->streamFactory);

        expect($capturedBody)->toHaveKeys(['authorization_model_id', 'object', 'relation', 'user_filters']);
        expect($capturedBody['authorization_model_id'])->toBe('model-123');
        expect($capturedBody['object'])->toBe('doc:1');
        expect($capturedBody['relation'])->toBe('viewer');
        expect($capturedBody['user_filters'])->toBe([['type' => 'user']]);
    });

    test('getRequest returns RequestContext with full body', function (): void {
        $userFilters = new UserTypeFilters(
            new UserTypeFilter('user'),
            new UserTypeFilter('group', 'members'),
        );
        $context = (object) ['location' => 'US'];
        $contextualTuples = new TupleKeys(
            new TupleKey('user:bob', 'editor', 'document:123'),
        );

        $request = new ListUsersRequest(
            store: 'store-id',
            model: 'model-xyz',
            object: 'resource:456',
            relation: 'owner',
            userFilters: $userFilters,
            context: $context,
            contextualTuples: $contextualTuples,
            consistency: Consistency::HIGHER_CONSISTENCY,
        );

        $capturedBody = null;
        $this->streamFactory->expects($this->once())
            ->method('createStream')
            ->with($this->callback(function ($body) use (&$capturedBody) {
                $capturedBody = json_decode($body, true);

                return true;
            }));

        $context = $request->getRequest($this->streamFactory);

        expect($capturedBody)->toHaveKeys([
            'authorization_model_id',
            'object',
            'relation',
            'user_filters',
            'context',
            'contextual_tuples',
            'consistency',
        ]);
        expect($capturedBody['authorization_model_id'])->toBe('model-xyz');
        expect($capturedBody['object'])->toBe('resource:456');
        expect($capturedBody['relation'])->toBe('owner');
        expect($capturedBody['user_filters'])->toBe([
            ['type' => 'user'],
            ['type' => 'group', 'relation' => 'members'],
        ]);
        expect($capturedBody['context'])->toBe(['location' => 'US']);
        expect($capturedBody['contextual_tuples'])->toBe([
            ['user' => 'user:bob', 'relation' => 'editor', 'object' => 'document:123'],
        ]);
        expect($capturedBody['consistency'])->toBe('HIGHER_CONSISTENCY');
    });

    test('handles empty user filters', function (): void {
        $userFilters = new UserTypeFilters();

        $request = new ListUsersRequest(
            store: 'store',
            model: 'model',
            object: 'object',
            relation: 'relation',
            userFilters: $userFilters,
        );

        $capturedBody = null;
        $this->streamFactory->expects($this->once())
            ->method('createStream')
            ->with($this->callback(function ($body) use (&$capturedBody) {
                $capturedBody = json_decode($body, true);

                return true;
            }));

        $request->getRequest($this->streamFactory);

        expect($capturedBody['user_filters'])->toBe([]);
    });

    test('handles complex context objects', function (): void {
        $userFilters = new UserTypeFilters(new UserTypeFilter('user'));
        $context = (object) [
            'nested' => (object) [
                'deep' => ['array' => true],
                'value' => 123,
            ],
            'list' => [1, 2, 3],
        ];

        $request = new ListUsersRequest(
            store: 'store',
            model: 'model',
            object: 'object',
            relation: 'relation',
            userFilters: $userFilters,
            context: $context,
        );

        $capturedBody = null;
        $this->streamFactory->expects($this->once())
            ->method('createStream')
            ->with($this->callback(function ($body) use (&$capturedBody) {
                $capturedBody = json_decode($body, true);

                return true;
            }));

        $request->getRequest($this->streamFactory);

        expect($capturedBody['context'])->toBe([
            'nested' => [
                'deep' => ['array' => true],
                'value' => 123,
            ],
            'list' => [1, 2, 3],
        ]);
    });

    test('throws when store is empty', function (): void {
        $this->expectException(InvalidArgumentException::class);
        new ListUsersRequest(store: '', model: 'model', object: 'object', relation: 'relation', userFilters: new UserTypeFilters());
    });

    test('throws when model is empty', function (): void {
        $this->expectException(InvalidArgumentException::class);
        new ListUsersRequest(store: 'store', model: '', object: 'object', relation: 'relation', userFilters: new UserTypeFilters());
    });

    test('throws when object is empty', function (): void {
        $this->expectException(InvalidArgumentException::class);
        new ListUsersRequest(store: 'store', model: 'model', object: '', relation: 'relation', userFilters: new UserTypeFilters());
    });

    test('throws when relation is empty', function (): void {
        $this->expectException(InvalidArgumentException::class);
        new ListUsersRequest(store: 'store', model: 'model', object: 'object', relation: '', userFilters: new UserTypeFilters());
    });
});
