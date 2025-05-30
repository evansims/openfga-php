<?php

declare(strict_types=1);

namespace OpenFGA;

use Closure;
use InvalidArgumentException;
use OpenFGA\Exceptions\ClientThrowable;
use OpenFGA\Models\{AuthorizationModelInterface, ConditionInterface, StoreInterface, TupleKey, TupleKeyInterface};
use OpenFGA\Models\BatchCheckItem;
use OpenFGA\Models\Collections\BatchCheckItems;
use OpenFGA\Models\Collections\{TupleKeys, TupleKeysInterface};
use OpenFGA\Models\Enums\Consistency;
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
 * @param  string                  $user      The user identifier (for example, 'user:alice', 'team:administrator')
 * @param  string                  $relation  The relationship type (for example, 'viewer', 'owner', 'member')
 * @param  string                  $object    The object identifier (for example, 'document:1', 'folder:shared')
 * @param  ConditionInterface|null $condition Optional condition for contextual permissions
 * @return TupleKey                The created tuple key
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
 * Helper for creating a BatchCheckItem for batch authorization checks.
 *
 * BatchCheckItems represent individual authorization checks within a batch request.
 * Each item contains a tuple key to check and a unique correlation ID to map the
 * result back to this specific check.
 *
 * @param string                                     $user             The user identifier (for example, 'user:alice', 'team:administrator')
 * @param string                                     $relation         The relationship type (for example, 'viewer', 'owner', 'member')
 * @param string                                     $object           The object identifier (for example, 'document:1', 'folder:shared')
 * @param string                                     $correlationId    Unique identifier for this check (max 36 chars, alphanumeric + hyphens)
 * @param TupleKeysInterface<TupleKeyInterface>|null $contextualTuples Optional contextual tuples for this check
 * @param object|null                                $context          Optional context object for this check
 *
 * @throws InvalidArgumentException If message translation parameters are invalid
 * @throws ReflectionException      If exception location capture fails
 * @throws ClientThrowable          If the correlation ID is invalid
 *
 * @return BatchCheckItem The created batch check item
 *
 * @see https://openfga.dev/docs/api#/Relationship%20Queries/BatchCheck Batch check API reference
 */
function batchCheckItem(
    string $user,
    string $relation,
    string $object,
    string $correlationId,
    ?TupleKeysInterface $contextualTuples = null,
    ?object $context = null,
): BatchCheckItem {
    return new BatchCheckItem(
        tupleKey: tuple($user, $relation, $object),
        correlationId: $correlationId,
        contextualTuples: $contextualTuples,
        context: $context,
    );
}

/**
 * Helper for creating a BatchCheckItems collection from multiple BatchCheckItem objects.
 *
 * This helper simplifies the creation of BatchCheckItems collections for batch authorization
 * operations, allowing you to check multiple user-object relationships in a single request.
 *
 * @param BatchCheckItem ...$items Variable number of BatchCheckItem objects to include in the collection
 *
 * @throws InvalidArgumentException If item validation fails
 * @throws ReflectionException      If exception location capture fails
 * @throws ClientThrowable          If collection creation fails
 *
 * @return BatchCheckItems Collection of batch check items
 *
 * @see https://openfga.dev/docs/api#/Relationship%20Queries/BatchCheck Batch check API reference
 */
function batchCheckItems(BatchCheckItem ...$items): BatchCheckItems
{
    return new BatchCheckItems($items);
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
 * @param ClientInterface $client An OpenFGA client.
 * @param string          $dsl    The authorization model DSL.
 *
 * @throws Throwable If the authorization model creation fails.
 *
 * @return AuthorizationModelInterface The authorization model.
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
 * Write relationship tuples.
 *
 * @param ClientInterface                                         $client An OpenFGA client.
 * @param StoreInterface|string                                   $store  The store to use.
 * @param AuthorizationModelInterface|string                      $model  The authorization model to use.
 * @param TupleKeyInterface|TupleKeysInterface<TupleKeyInterface> $tuples The tuples to write.
 *
 * @throws Throwable If the write fails.
 */
function write(ClientInterface $client, StoreInterface | string $store, AuthorizationModelInterface | string $model, TupleKeyInterface | TupleKeysInterface $tuples): void
{
    if ($tuples instanceof TupleKeyInterface) {
        $tuples = new TupleKeys([$tuples]);
    }

    $client->writeTuples(store: $store, model: $model, writes: $tuples);
}

/**
 * Delete relationship tuples.
 *
 * @param ClientInterface                                         $client An OpenFGA client.
 * @param StoreInterface|string                                   $store  The store to use.
 * @param AuthorizationModelInterface|string                      $model  The authorization model to use.
 * @param TupleKeyInterface|TupleKeysInterface<TupleKeyInterface> $tuples The tuples to delete.
 *
 * @throws Throwable If the delete fails.
 */
function delete(ClientInterface $client, StoreInterface | string $store, AuthorizationModelInterface | string $model, TupleKeyInterface | TupleKeysInterface $tuples): void
{
    if ($tuples instanceof TupleKeyInterface) {
        $tuples = new TupleKeys([$tuples]);
    }

    $client->writeTuples(store: $store, model: $model, deletes: $tuples);
}

/**
 * Check for a relationship.
 *
 * @param ClientInterface                        $client           An OpenFGA client.
 * @param StoreInterface|string                  $store            The store to use.
 * @param AuthorizationModelInterface|string     $model            The authorization model to use.
 * @param TupleKeyInterface                      $tuple            The tuple to check.
 * @param ?bool                                  $trace            Whether to trace the check.
 * @param ?object                                $context          The context to use for the check.
 * @param ?TupleKeysInterface<TupleKeyInterface> $contextualTuples The contextual tuples to use for the check.
 * @param ?Consistency                           $consistency      The consistency to use for the check.
 *
 * @throws Throwable If the check fails.
 *
 * @return bool True if the tuple is allowed, false otherwise.
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
 * @param ClientInterface             $client An OpenFGA client.
 * @param StoreInterface|string       $store  The store to use.
 * @param AuthorizationModelInterface $model  The authorization model to use.
 *
 * @throws Throwable If the model creation fails.
 *
 * @return string The authorization model ID.
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
