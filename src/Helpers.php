<?php

declare(strict_types=1);

namespace OpenFGA;

use Closure;
use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientException, ClientThrowable};
use OpenFGA\Models\{AuthorizationModelInterface, ConditionInterface, StoreInterface, TupleKey, TupleKeyInterface};
use OpenFGA\Models\Collections\{TupleKeys, TupleKeysInterface};
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Responses\WriteTuplesResponse;
use OpenFGA\Results\{Failure, ResultInterface, Success};
use ReflectionException;
use Throwable;

// ==============================================================================
// Models Helpers
// ==============================================================================

/**
 * Helper for creating a TupleKey representing a relationship between a user and object.
 *
 * TupleKeys are fundamental to OpenFGA, representing specific relationships between
 * users and objects. This helper provides a convenient way to create these relationships
 * with optional conditional logic.
 *
 * @param string                  $user      The user identifier (for example, 'user:alice', 'team:administrator')
 * @param string                  $relation  The relationship type (for example, 'viewer', 'owner', 'member')
 * @param string                  $object    The object identifier (for example, 'document:1', 'folder:shared')
 * @param ConditionInterface|null $condition Optional condition for contextual permissions
 *
 * @throws ClientException          If the user or object identifier format is invalid
 * @throws InvalidArgumentException If translation parameters are invalid
 * @throws ReflectionException      If exception location capture fails
 *
 * @return TupleKey The created tuple key
 *
 * @see https://openfga.dev/docs/getting-started/update-tuples Working with relationship tuples
 */
function tuple(string $user, string $relation, string $object, ?ConditionInterface $condition = null): TupleKey
{
    return new TupleKey($user, $relation, $object, $condition);
}

/**
 * Helper for creating a TupleKeys collection from multiple TupleKey objects.
 *
 * This helper simplifies the creation of TupleKeys collections for batch operations
 * like writing multiple relationships or providing contextual tuples for authorization
 * checks.
 *
 * @param TupleKey ...$tuples Variable number of TupleKey objects to include in the collection
 *
 * @throws InvalidArgumentException If tuple validation fails
 * @throws ReflectionException      If exception location capture fails
 * @throws ClientThrowable          If collection creation fails
 *
 * @return TupleKeys The created collection of tuple keys
 *
 * @see https://openfga.dev/docs/getting-started/update-tuples Working with relationship tuples
 */
function tuples(TupleKey ...$tuples): TupleKeys
{
    return new TupleKeys($tuples);
}

/**
 * Helper for performing batch tuple operations using a client.
 *
 * This helper provides a convenient way to perform large-scale tuple operations
 * that automatically handle chunking and provide detailed results about the
 * success or failure of each chunk. It's designed to work with the non-transaction
 * mode similar to the OpenFGA Go SDK.
 *
 * @param ClientInterface                    $client              The OpenFGA client to use
 * @param StoreInterface|string              $store               The store to operate on
 * @param AuthorizationModelInterface|string $model               The authorization model to use
 * @param TupleKeysInterface|null            $writes              Collection of tuples to write
 * @param TupleKeysInterface|null            $deletes             Collection of tuples to delete
 * @param int                                $maxParallelRequests Maximum concurrent requests (default: 1)
 * @param int                                $maxTuplesPerChunk   Maximum tuples per chunk (default: 100)
 * @param int                                $maxRetries          Maximum retry attempts for failed chunks (default: 0)
 * @param float                              $retryDelaySeconds   Delay between retries in seconds (default: 1.0)
 * @param bool                               $stopOnFirstError    Stop processing remaining chunks on first error (default: false)
 *
 * @throws Throwable If the batch operation fails completely
 *
 * @return WriteTuplesResponse The result of the batch operation
 *
 * @example Processing a large batch of tuple operations
 * $writes = tuples(
 *     tuple('user:anne', 'viewer', 'document:1'),
 *     tuple('user:bob', 'viewer', 'document:2'),
 *     // ... hundreds more tuples
 * );
 *
 * $result = batch(
 *     client: $client,
 *     store: 'store-id',
 *     model: 'model-id',
 *     writes: $writes
 * );
 *
 * echo "Success rate: " . ($result->getSuccessRate() * 100) . "%\n";
 */
function batch(
    ClientInterface $client,
    StoreInterface | string $store,
    AuthorizationModelInterface | string $model,
    ?TupleKeysInterface $writes = null,
    ?TupleKeysInterface $deletes = null,
    int $maxParallelRequests = 1,
    int $maxTuplesPerChunk = 100,
    int $maxRetries = 0,
    float $retryDelaySeconds = 1.0,
    bool $stopOnFirstError = false,
): WriteTuplesResponse {
    /** @var WriteTuplesResponse */
    return $client->writeTuples(
        store: $store,
        model: $model,
        writes: $writes,
        deletes: $deletes,
        transactional: false,
        maxParallelRequests: $maxParallelRequests,
        maxTuplesPerChunk: $maxTuplesPerChunk,
        maxRetries: $maxRetries,
        retryDelaySeconds: $retryDelaySeconds,
        stopOnFirstError: $stopOnFirstError,
    )->unwrap();
}

/**
 * Create a store and return its ID.
 *
 * Convenience helper for creating a new OpenFGA store. Stores provide data isolation
 * and are the top-level container for authorization models and relationship tuples.
 *
 * @param ClientInterface $client An OpenFGA client instance
 * @param string          $name   The name of the store to create
 *
 * @throws Throwable If the store creation fails
 *
 * @return string The unique ID of the created store
 *
 * @see https://openfga.dev/docs/getting-started/create-store Creating and managing stores
 */
function store(ClientInterface $client, string $name): string
{
    /** @var Responses\CreateStoreResponseInterface $response */
    $response = $client->createStore(name: $name)
        ->unwrap();

    return $response->getId();
}

/**
 * Create an authorization model.
 *
 * @param ClientInterface $client an OpenFGA client
 * @param string          $dsl    the authorization model DSL
 *
 * @throws Throwable if the authorization model creation fails
 *
 * @return AuthorizationModelInterface the authorization model
 */
function dsl(ClientInterface $client, string $dsl): AuthorizationModelInterface
{
    /** @var AuthorizationModelInterface */
    return $client->dsl($dsl)
        ->unwrap();
}

// ==============================================================================
// Results Helpers
// ==============================================================================

/**
 * Helper for executing a Closure safely or unwrapping a Result.
 *
 * This helper provides a unified way to work with both closures and Result objects,
 * implementing the Result pattern used throughout the OpenFGA SDK. When passed a
 * closure, it safely executes it and wraps the result. When passed a Result, it
 * unwraps successful values or throws failures.
 *
 * @param Closure(): mixed|ResultInterface $context The closure to execute or Result to unwrap
 *
 * @throws Throwable if a `Failure` ResultInterface is passed and needs to be unwrapped
 *
 * @return mixed The result value or wrapped Result
 */
function result(ResultInterface | Closure $context): mixed
{
    if ($context instanceof Closure) {
        try {
            /** @var mixed $out */
            $out = $context();

            return $out instanceof ResultInterface ? $out : new Success($out);
        } catch (Throwable $t) {
            return new Failure($t);
        }
    }

    if ($context->failed()) {
        throw $context->err();
    }

    return $context->val();
}

/**
 * Helper for unwrapping a `Success` or returning a default value.
 *
 * @param ResultInterface             $result
 * @param callable(mixed): mixed|null $fn
 *
 * @throws Throwable If result is a failure and no fallback is provided
 */
function unwrap(ResultInterface $result, ?callable $fn = null): mixed
{
    return $result->unwrap($fn);
}

/**
 * Helper for executing a callback on a `Success`.
 *
 * @param ResultInterface            $result
 * @param callable(mixed): void|null $fn
 *
 * @throws Throwable Any exception thrown by the callback or result access
 */
function success(ResultInterface $result, ?callable $fn = null): bool
{
    if ($result->succeeded()) {
        if (null !== $fn) {
            $fn($result->val());
        }

        return true;
    }

    return false;
}

/**
 * Helper for executing a callback on a `Failure`.
 *
 * @param ResultInterface                $result
 * @param callable(Throwable): void|null $fn
 *
 * @throws Throwable Any exception thrown by the callback or result access
 */
function failure(ResultInterface $result, ?callable $fn = null): bool
{
    if ($result->failed()) {
        if (null !== $fn) {
            $fn($result->err());
        }

        return true;
    }

    return false;
}

/**
 * Helper for creating a `Success`.
 *
 * @param mixed $value
 */
function ok(mixed $value): Success
{
    return new Success($value);
}

/**
 * Helper for creating a `Failure`.
 *
 * @param Throwable $error
 */
function err(Throwable $error): Failure
{
    return new Failure($error);
}

// ==============================================================================
// Requests Helpers
// ==============================================================================

/**
 * Write relationship tuples in the simplest way possible.
 *
 * This helper provides the most straightforward way to write tuples to OpenFGA.
 * By default, it uses transactional mode (all-or-nothing). For bulk operations
 * with partial success handling, use the `batch()` helper instead.
 *
 * @param ClientInterface                      $client        The OpenFGA client
 * @param StoreInterface|string                $store         The store to write to
 * @param AuthorizationModelInterface|string   $model         The authorization model to use
 * @param TupleKeyInterface|TupleKeysInterface $tuples        The tuple(s) to write
 * @param bool                                 $transactional Whether to use transactional mode (default: true)
 *
 * @throws Throwable If the write operation fails
 *
 * @example Writing a single tuple
 * write($client, $store, $model, tuple('user:anne', 'reader', 'document:budget'));
 * @example Writing multiple tuples transactionally
 * write($client, $store, $model, tuples(
 *     tuple('user:anne', 'reader', 'document:budget'),
 *     tuple('user:anne', 'reader', 'document:forecast')
 * ));
 * @example Simple non-transactional write for resilience
 * write($client, $store, $model, $tuples, transactional: false);
 */
function write(
    ClientInterface $client,
    StoreInterface | string $store,
    AuthorizationModelInterface | string $model,
    TupleKeyInterface | TupleKeysInterface $tuples,
    bool $transactional = true,
): void {
    if ($tuples instanceof TupleKeyInterface) {
        $tuples = new TupleKeys([$tuples]);
    }

    $client->writeTuples(
        store: $store,
        model: $model,
        writes: $tuples,
        transactional: $transactional,
    )->unwrap();
}

/**
 * Delete relationship tuples in the simplest way possible.
 *
 * This helper provides the most straightforward way to delete tuples from OpenFGA.
 * By default, it uses transactional mode (all-or-nothing). For bulk operations
 * with partial success handling, use the `batch()` helper instead.
 *
 * @param ClientInterface                      $client        The OpenFGA client
 * @param StoreInterface|string                $store         The store to delete from
 * @param AuthorizationModelInterface|string   $model         The authorization model to use
 * @param TupleKeyInterface|TupleKeysInterface $tuples        The tuple(s) to delete
 * @param bool                                 $transactional Whether to use transactional mode (default: true)
 *
 * @throws Throwable If the delete operation fails
 *
 * @example Deleting a single tuple
 * delete($client, $store, $model, tuple('user:anne', 'reader', 'document:budget'));
 * @example Deleting multiple tuples transactionally
 * delete($client, $store, $model, tuples(
 *     tuple('user:anne', 'reader', 'document:budget'),
 *     tuple('user:bob', 'editor', 'document:forecast')
 * ));
 * @example Simple non-transactional delete for resilience
 * delete($client, $store, $model, $tuples, transactional: false);
 */
function delete(
    ClientInterface $client,
    StoreInterface | string $store,
    AuthorizationModelInterface | string $model,
    TupleKeyInterface | TupleKeysInterface $tuples,
    bool $transactional = true,
): void {
    if ($tuples instanceof TupleKeyInterface) {
        $tuples = new TupleKeys([$tuples]);
    }

    $client->writeTuples(
        store: $store,
        model: $model,
        deletes: $tuples,
        transactional: $transactional,
    )->unwrap();
}

/**
 * Check for a relationship.
 *
 * @param ClientInterface                    $client           an OpenFGA client
 * @param StoreInterface|string              $store            the store to use
 * @param AuthorizationModelInterface|string $model            the authorization model to use
 * @param TupleKeyInterface                  $tuple            the tuple to check
 * @param ?bool                              $trace            whether to trace the check
 * @param ?object                            $context          the context to use for the check
 * @param ?TupleKeysInterface                $contextualTuples the contextual tuples to use for the check
 * @param ?Consistency                       $consistency      the consistency to use for the check
 *
 * @throws Throwable if the check fails
 *
 * @return bool true if the tuple is allowed, false otherwise
 */
function allowed(ClientInterface $client, StoreInterface | string $store, AuthorizationModelInterface | string $model, TupleKeyInterface $tuple, ?bool $trace = null, ?object $context = null, ?TupleKeysInterface $contextualTuples = null, ?Consistency $consistency = null): bool
{
    /** @var Responses\CheckResponseInterface $response */
    $response = $client->check(store: $store, model: $model, tupleKey: $tuple, trace: $trace, context: $context, contextualTuples: $contextualTuples, consistency: $consistency)
        ->unwrap();

    return $response->getAllowed() ?? false;
}

/**
 * Create an authorization model.
 *
 * @param ClientInterface             $client an OpenFGA client
 * @param StoreInterface|string       $store  the store to use
 * @param AuthorizationModelInterface $model  the authorization model to use
 *
 * @throws Throwable if the model creation fails
 *
 * @return string the authorization model ID
 */
function model(ClientInterface $client, StoreInterface | string $store, AuthorizationModelInterface $model): string
{
    /** @var Responses\CreateAuthorizationModelResponseInterface $response */
    $response = $client->createAuthorizationModel(
        store: $store,
        typeDefinitions: $model->getTypeDefinitions(),
        conditions: $model->getConditions(),
        schemaVersion: $model->getSchemaVersion(),
    )
        ->unwrap();

    return $response->getModel();
}
