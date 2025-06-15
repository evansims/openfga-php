<?php

declare(strict_types=1);

namespace OpenFGA;

use Closure;
use Generator;
use InvalidArgumentException;
use OpenFGA\Context\Context;
use OpenFGA\Exceptions\{ClientException, ClientThrowable};
use OpenFGA\Models\{AuthorizationModelInterface, BatchCheckItem, BatchCheckItemInterface, ConditionInterface, StoreInterface, TupleKey, TupleKeyInterface, UserObjectInterface, UserTypeFilter, UserTypeFilterInterface};
use OpenFGA\Models\Collections\{TupleKeys, TupleKeysInterface, UserTypeFilters, UserTypeFiltersInterface};
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Responses\{WriteTuplesResponse, WriteTuplesResponseInterface};
use OpenFGA\Results\{Failure, FailureInterface, ResultInterface, Success, SuccessInterface};
use OpenFGA\Translation\Translator;
use ReflectionException;
use Throwable;

use function is_string;

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
 * @return TupleKeyInterface The created tuple key
 *
 * @see https://openfga.dev/docs/getting-started/update-tuples Working with relationship tuples
 */
function tuple(string $user, string $relation, string $object, ?ConditionInterface $condition = null): TupleKeyInterface
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
 * @return TupleKeysInterface The created collection of tuple keys
 *
 * @see https://openfga.dev/docs/getting-started/update-tuples Working with relationship tuples
 */
function tuples(TupleKey ...$tuples): TupleKeysInterface
{
    return new TupleKeys($tuples);
}

/**
 * Helper for creating a BatchCheckItem for batch authorization checks.
 *
 * This helper simplifies the creation of BatchCheckItem instances for use with
 * the batchCheck API endpoint. It provides a convenient way to create individual
 * check items with optional context and contextual tuples. When no correlation ID
 * is provided, one is automatically generated based on the tuple key.
 *
 * You can pass either a TupleKey object or individual user, relation, and object parameters:
 *
 * @param ?string             $correlation      Optional unique identifier for this check (max 36 chars, alphanumeric + hyphens)
 * @param ?TupleKeyInterface  $tuple            Optional pre-built tuple key to check
 * @param ?string             $user             Optional user identifier (required if $tuple not provided)
 * @param ?string             $relation         Optional relation (required if $tuple not provided)
 * @param ?string             $object           Optional object identifier (required if $tuple not provided)
 * @param ?ConditionInterface $condition        Optional condition for the tuple
 * @param ?TupleKeysInterface $contextualTuples Optional contextual tuples for this check
 * @param ?object             $context          Optional context object for this check
 *
 * @throws ClientThrowable          If the correlation ID is invalid
 * @throws InvalidArgumentException If neither tuple nor user/relation/object parameters are provided
 * @throws ReflectionException      If exception location capture fails
 *
 * @return BatchCheckItemInterface The created batch check item
 *
 * @example Using with a TupleKey object
 * check('correlation-id', $tupleKey, contextualTuples: $contextualTuples, context: $context)
 * @example Using with individual parameters
 * check('correlation-id', user: 'user:anne', relation: 'viewer', object: 'document:budget')
 * @example Using with auto-generated correlation
 * check(user: 'user:anne', relation: 'viewer', object: 'document:budget')
 *
 * @see https://openfga.dev/docs/getting-started/perform-check#02-calling-check-api Checking permissions
 */
function check(
    ?string $correlation = null,
    ?TupleKeyInterface $tuple = null,
    ?string $user = null,
    ?string $relation = null,
    ?string $object = null,
    ?ConditionInterface $condition = null,
    ?TupleKeysInterface $contextualTuples = null,
    ?object $context = null,
): BatchCheckItemInterface {
    // Determine the tuple to use
    if ($tuple instanceof TupleKeyInterface) {
        $tupleKey = $tuple;
    } else {
        // Build TupleKey from individual parameters
        if (null === $user || null === $relation || null === $object) {
            throw new InvalidArgumentException('Either $tuple must be provided, or all of $user, $relation, and $object must be provided');
        }

        $tupleKey = new TupleKey($user, $relation, $object, $condition);
    }

    // Auto-generate correlation ID if not provided
    if (null === $correlation) {
        $correlation = substr(hash('sha256', $tupleKey->getUser() . ':' . $tupleKey->getRelation() . ':' . $tupleKey->getObject()), 0, 36);
    }

    return new BatchCheckItem($tupleKey, $correlation, $contextualTuples, $context);
}

/**
 * Helper for performing batch tuple write operations using a client.
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
 * @return WriteTuplesResponseInterface The result of the batch operation
 *
 * @example Processing a large batch of tuple operations
 * $writeTuples = tuples(
 *     tuple('user:anne', 'viewer', 'document:1'),
 *     tuple('user:bob', 'viewer', 'document:2'),
 *     // ... hundreds more tuples
 * );
 *
 * $result = writes(
 *     client: $client,
 *     store: 'store-id',
 *     model: 'model-id',
 *     writes: $writeTuples
 * );
 *
 * echo "Success rate: " . ($result->getSuccessRate() * 100) . "%\n";
 *
 * @see https://openfga.dev/docs/getting-started/update-tuples Working with relationship tuples
 */
function writes(
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
): WriteTuplesResponseInterface {
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
 * Define an authorization model using the OpenFGA DSL.
 *
 * @param ClientInterface $client an OpenFGA client
 * @param string          $dsl    the authorization model DSL
 *
 * @throws Throwable if the authorization model creation fails
 *
 * @return AuthorizationModelInterface the authorization model
 *
 * @see https://openfga.dev/docs/configuration-language
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
function ok(mixed $value): SuccessInterface
{
    return new Success($value);
}

/**
 * Helper for creating a `Failure`.
 *
 * @param Throwable $error
 */
function err(Throwable $error): FailureInterface
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
 * with partial success handling, use the `writes()` helper instead.
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
 *
 * @see https://openfga.dev/docs/getting-started/update-tuples Working with relationship tuples
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
 * with partial success handling, use the `writes()` helper instead.
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
 *
 * @see https://openfga.dev/docs/getting-started/update-tuples Working with relationship tuples
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
 * Check for a relationship with guaranteed boolean result.
 *
 * This helper safely checks permissions and returns false for any error
 * condition (network failures, authentication issues, malformed requests, etc.).
 * Use the standard client->check() method if you need detailed error information.
 *
 * You can pass either a TupleKey object or individual user, relation, and object parameters:
 *
 * @param  ClientInterface                    $client           The OpenFGA client
 * @param  StoreInterface|string              $store            The store to use
 * @param  AuthorizationModelInterface|string $model            The authorization model to use
 * @param  ?TupleKeyInterface                 $tuple            Optional pre-built tuple key to check
 * @param  ?string                            $user             Optional user identifier (required if $tuple not provided)
 * @param  ?string                            $relation         Optional relation (required if $tuple not provided)
 * @param  ?string                            $object           Optional object identifier (required if $tuple not provided)
 * @param  ?ConditionInterface                $condition        Optional condition for the tuple
 * @param  ?bool                              $trace            Whether to trace the check
 * @param  ?object                            $context          Context to use for the check
 * @param  ?TupleKeysInterface                $contextualTuples Contextual tuples to use for the check
 * @param  ?Consistency                       $consistency      Consistency level to use for the check
 * @return bool                               True if explicitly allowed, false for denied or any error
 *
 * @example Using with a TupleKey object
 * allowed($client, $store, $model, $tupleKey)
 * @example Using with individual parameters
 * allowed($client, $store, $model, user: 'user:anne', relation: 'viewer', object: 'document:budget')
 * @example Using with all options
 * allowed($client, $store, $model, user: 'user:anne', relation: 'viewer', object: 'document:budget', trace: true)
 *
 * @see https://openfga.dev/docs/getting-started/perform-check#02-calling-check-api Checking permissions
 */
function allowed(
    ClientInterface $client,
    StoreInterface | string $store,
    AuthorizationModelInterface | string $model,
    ?TupleKeyInterface $tuple = null,
    ?string $user = null,
    ?string $relation = null,
    ?string $object = null,
    ?ConditionInterface $condition = null,
    ?bool $trace = null,
    ?object $context = null,
    ?TupleKeysInterface $contextualTuples = null,
    ?Consistency $consistency = null,
): bool {
    try {
        // Determine the tuple to use
        if ($tuple instanceof TupleKeyInterface) {
            $tupleKey = $tuple;
        } else {
            // Build TupleKey from individual parameters
            if (null === $user || null === $relation || null === $object) {
                throw new InvalidArgumentException('Either $tuple must be provided, or all of $user, $relation, and $object must be provided');
            }

            $tupleKey = new TupleKey($user, $relation, $object, $condition);
        }

        /** @var Responses\CheckResponseInterface $response */
        $response = $client->check(
            store: $store,
            model: $model,
            tupleKey: $tupleKey,
            trace: $trace,
            context: $context,
            contextualTuples: $contextualTuples,
            consistency: $consistency,
        )->unwrap();

        return $response->getAllowed() ?? false;
    } catch (Throwable) {
        // Return false for any error (network, auth, validation, etc.)
        return false;
    }
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
 *
 * @see https://openfga.dev/docs/getting-started/configure-model#step-by-step Creating an authorization model
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

/**
 * Stream all objects a user has a specific relation to with guaranteed array result.
 *
 * This helper provides a simplified way to use the streamedListObjects method,
 * which efficiently retrieves all objects of a given type that a user has access to
 * through a specific relationship. Unlike the regular listObjects method, this streams
 * results without pagination limitations and safely returns an empty array for any
 * error condition (network failures, authentication issues, malformed requests, etc.).
 *
 * @param  ClientInterface                    $client           The OpenFGA client
 * @param  StoreInterface|string              $store            The store to query
 * @param  AuthorizationModelInterface|string $model            The authorization model to use
 * @param  string                             $type             The object type to search for (e.g., 'document', 'folder')
 * @param  string                             $relation         The relation to check (e.g., 'viewer', 'owner')
 * @param  string                             $user             The user to check for (e.g., 'user:anne')
 * @param  ?object                            $context          Optional context for condition evaluation
 * @param  ?TupleKeysInterface                $contextualTuples Optional contextual tuples for the query
 * @param  ?Consistency                       $consistency      Optional consistency level for the query
 * @return array<string>                      Array of object IDs the user has the specified relation to, or empty array on any error
 *
 * @example Find all documents a user can view
 * $documents = objects($client, $store, $model, 'document', 'viewer', 'user:anne');
 * // Returns: ['document:budget', 'document:forecast', 'document:report'] or [] on error
 * @example Find all folders a user owns with contextual tuples
 * $ownedFolders = objects(
 *     $client,
 *     $store,
 *     $model,
 *     'folder',
 *     'owner',
 *     'user:bob',
 *     contextualTuples: tuples(
 *         tuple('user:bob', 'owner', 'folder:temp')
 *     )
 * );
 * @example Safe to use even with network issues, invalid stores, etc.
 * $userDocs = objects($client, $storeId, $modelId, 'document', 'viewer', 'user:anne');
 * // Will return [] if there are any errors (network, auth, validation, etc.)
 *
 * @see https://openfga.dev/docs/getting-started/perform-list-objects Listing objects
 */
function objects(
    ClientInterface $client,
    StoreInterface | string $store,
    AuthorizationModelInterface | string $model,
    string $type,
    string $relation,
    string $user,
    ?object $context = null,
    ?TupleKeysInterface $contextualTuples = null,
    ?Consistency $consistency = null,
): array {
    try {
        /** @var Generator<int, Responses\StreamedListObjectsResponseInterface> $generator */
        $generator = $client->streamedListObjects(
            store: $store,
            model: $model,
            type: $type,
            relation: $relation,
            user: $user,
            context: $context,
            contextualTuples: $contextualTuples,
            consistency: $consistency,
        )->unwrap();

        $objects = [];

        foreach ($generator as $streamedResponse) {
            $objects[] = $streamedResponse->getObject();
        }

        return $objects;
    } catch (Throwable) {
        // Return empty array for any error (network, auth, validation, etc.)
        return [];
    }
}

/**
 * List all authorization models in a store with automatic pagination.
 *
 * This helper provides a simplified way to retrieve all authorization models from a store,
 * automatically handling pagination to collect all available models. Unlike the regular
 * listAuthorizationModels method, this continues fetching until all models are retrieved.
 *
 * @param ClientInterface       $client The OpenFGA client
 * @param StoreInterface|string $store  The store to list models from
 *
 * @throws Throwable If the operation fails
 *
 * @return array<AuthorizationModelInterface> Array of all authorization models in the store
 *
 * @example Get all authorization models in a store
 * $allModels = models($client, $storeId);
 * foreach ($allModels as $model) {
 *     echo "Model: {$model->getId()}\n";
 * }
 * @example Find the latest authorization model
 * $allModels = models($client, $store);
 * $latestModel = end($allModels); // Models are typically returned in chronological order
 *
 * @see https://openfga.dev/docs/getting-started/immutable-models#viewing-all-the-authorization-models Listing models
 */
function models(
    ClientInterface $client,
    StoreInterface | string $store,
): array {
    $allModels = [];
    $continuationToken = null;

    do {
        /** @var Responses\ListAuthorizationModelsResponseInterface $response */
        $response = $client->listAuthorizationModels(
            store: $store,
            continuationToken: $continuationToken,
        )->unwrap();

        // Add models from current page to collection
        foreach ($response->getModels() as $model) {
            $allModels[] = $model;
        }

        // Get continuation token for next page
        $continuationToken = $response->getContinuationToken();
    } while (null !== $continuationToken);

    return $allModels;
}

/**
 * Perform batch authorization checks with simplified syntax.
 *
 * This helper provides a simplified way to perform multiple authorization checks
 * in a single request using BatchCheckItem instances. It automatically handles
 * BatchCheckItems collection creation, making batch checks more approachable.
 *
 * @param ClientInterface                    $client    The OpenFGA client
 * @param StoreInterface|string              $store     The store to check against
 * @param AuthorizationModelInterface|string $model     The authorization model to use
 * @param BatchCheckItemInterface            ...$checks Variable number of BatchCheckItem instances
 *
 * @throws ClientThrowable          If batch item validation fails
 * @throws InvalidArgumentException If check specification is invalid
 * @throws ReflectionException      If schema reflection fails
 * @throws Throwable                If the batch check operation fails
 *
 * @return array<string, bool> Map of correlation ID to allowed/denied result
 *
 * @example Simple batch check with BatchCheckItem instances
 * $results = checks($client, $store, $model,
 *     new BatchCheckItem(tuple('user:anne', 'viewer', 'document:budget'), 'anne-check'),
 *     new BatchCheckItem(tuple('user:bob', 'editor', 'document:budget'), 'bob-check'),
 *     new BatchCheckItem(tuple('user:charlie', 'owner', 'document:budget'), 'charlie-check')
 * );
 * // Returns: ['anne-check' => true, 'bob-check' => false, 'charlie-check' => true]
 * @example Batch check with context and contextual tuples
 * $results = checks($client, $store, $model,
 *     new BatchCheckItem(
 *         tupleKey: tuple('user:anne', 'viewer', 'document:budget'),
 *         correlationId: 'anne-budget-view'
 *     ),
 *     new BatchCheckItem(
 *         tupleKey: tuple('user:bob', 'editor', 'document:budget'),
 *         correlationId: 'bob-budget-edit',
 *         context: (object)['time' => '10:00']
 *     )
 * );
 * // Returns: ['anne-budget-view' => true, 'bob-budget-edit' => false]
 *
 * @see https://openfga.dev/docs/getting-started/perform-check#03-calling-batch-check-api Batch checking permissions
 */
function checks(
    ClientInterface $client,
    StoreInterface | string $store,
    AuthorizationModelInterface | string $model,
    BatchCheckItemInterface ...$checks,
): array {
    $batchCheckItems = new Models\Collections\BatchCheckItems($checks);

    /** @var Responses\BatchCheckResponseInterface $response */
    $response = $client->batchCheck(
        store: $store,
        model: $model,
        checks: $batchCheckItems,
    )->unwrap();

    $results = [];

    foreach ($response->getResult() as $correlationId => $batchCheckSingleResult) {
        $results[$correlationId] = $batchCheckSingleResult->getAllowed() ?? false;
    }

    return $results;
}

/**
 * List all users who have a specific relationship with an object.
 *
 * This helper provides a simplified way to use the listUsers method, which finds
 * all users (and optionally groups) that have a specific relationship with an object.
 * It's the inverse of permission checking - instead of asking "can this user access
 * this object?", it asks "which users can access this object?". Results are streamed
 * automatically with pagination handled internally.
 *
 * @param  ClientInterface                                  $client           The OpenFGA client
 * @param  StoreInterface|string                            $store            The store to query
 * @param  AuthorizationModelInterface|string               $model            The authorization model to use
 * @param  string                                           $object           The object to check relationships for (e.g., 'document:budget')
 * @param  string                                           $relation         The relationship to check (e.g., 'viewer', 'editor', 'owner')
 * @param  UserTypeFilterInterface|UserTypeFiltersInterface $filters          Filters for user types to include
 * @param  ?object                                          $context          Optional additional context for evaluation
 * @param  ?TupleKeysInterface                              $contextualTuples Optional contextual tuples for the query
 * @param  ?Consistency                                     $consistency      Optional consistency level for the query
 * @return array<string>                                    Array of user identifiers who have the specified relationship, or empty array on error
 *
 * @example Find all users who can view a document
 * $viewers = users($client, $store, $model, 'document:budget', 'viewer',
 *     new UserTypeFilter('user')
 * );
 * // Returns: ['user:anne', 'user:bob', 'user:charlie']
 * @example Find both users and groups with edit access
 * $editors = users($client, $store, $model, 'document:budget', 'editor',
 *     new UserTypeFilters([
 *         new UserTypeFilter('user'),
 *         new UserTypeFilter('group')
 *     ])
 * );
 * // Returns: ['user:anne', 'group:engineering', 'user:david']
 * @example Find users with contextual tuples
 * $editors = users($client, $store, $model, 'document:technical-spec', 'editor',
 *     new UserTypeFilters([new UserTypeFilter('user')]),
 *     contextualTuples: tuples(
 *         tuple('user:anne', 'member', 'team:engineering')
 *     )
 * );
 *
 * @see https://openfga.dev/docs/getting-started/perform-list-users Listing users
 */
function users(
    ClientInterface $client,
    StoreInterface | string $store,
    AuthorizationModelInterface | string $model,
    string $object,
    string $relation,
    UserTypeFiltersInterface | UserTypeFilterInterface $filters,
    ?object $context = null,
    ?TupleKeysInterface $contextualTuples = null,
    ?Consistency $consistency = null,
): array {
    try {
        if ($filters instanceof UserTypeFilterInterface) {
            $filters = new UserTypeFilters([$filters]);
        }

        /** @var Responses\ListUsersResponseInterface $response */
        $response = $client->listUsers(
            store: $store,
            model: $model,
            object: $object,
            relation: $relation,
            userFilters: $filters,
            context: $context,
            contextualTuples: $contextualTuples,
            consistency: $consistency,
        )->unwrap();

        $users = [];

        foreach ($response->getUsers() as $user) {
            // Get the user identifier string
            $object = $user->getObject();

            if (is_string($object)) {
                $users[] = $object;
            } elseif ($object instanceof UserObjectInterface) {
                $users[] = (string) $object;
            }
            // Skip users that don't have a valid object representation
        }

        return $users;
    } catch (Throwable) {
        // Return empty array for any error (network, auth, validation, etc.)
        return [];
    }
}

/**
 * Helper for creating a UserTypeFilter for user type filtering.
 *
 * This helper provides a convenient way to create UserTypeFilter instances without
 * needing to write the full class name. UserTypeFilter is used to specify which
 * types of users should be included in authorization queries like listUsers.
 *
 * @param string      $type     The user type to filter by (e.g., 'user', 'group', 'organization')
 * @param string|null $relation Optional relation to filter by (e.g., 'member', 'admin', 'owner')
 *
 * @throws InvalidArgumentException If filter parameters are invalid
 * @throws ReflectionException      If exception location capture fails
 *
 * @return UserTypeFilterInterface The created user type filter
 *
 * @example Filter for direct users only
 * $userFilter = filter('user');
 * @example Filter for group members
 * $groupMemberFilter = filter('group', 'member');
 * @example Filter for organization admins
 * $orgAdminFilter = filter('organization', 'admin');
 * @example Using with users() helper
 * $viewers = users($client, $store, $model, 'document:budget', 'viewer', filter('user'));
 *
 * @see https://openfga.dev/docs/getting-started/perform-list-users Listing users
 */
function filter(string $type, ?string $relation = null): UserTypeFilterInterface
{
    return new UserTypeFilter($type, $relation);
}

/**
 * Helper for creating UserTypeFilters collection from multiple UserTypeFilter instances.
 *
 * This helper provides a convenient way to create UserTypeFilters collections for
 * authorization queries. It accepts a variable number of UserTypeFilter instances
 * and creates a properly typed collection that can be used with listUsers operations.
 *
 * @param UserTypeFilterInterface ...$filters Variable number of UserTypeFilter instances
 *
 * @throws InvalidArgumentException If filter validation fails
 * @throws ReflectionException      If exception location capture fails
 * @throws ClientThrowable          If collection creation fails
 *
 * @return UserTypeFiltersInterface The created collection of user type filters
 *
 * @example Simple user filter
 * $userFilters = filters(filter('user'));
 * @example Multiple user types
 * $mixedFilters = filters(
 *     filter('user'),
 *     filter('group'),
 *     filter('organization', 'admin')
 * );
 * @example Using with users() helper
 * $editors = users($client, $store, $model, 'document:budget', 'editor',
 *     filters(
 *         filter('user'),
 *         filter('group', 'member')
 *     )
 * );
 * @example Complex authorization queries
 * $contributors = users($client, $store, $model, 'project:website', 'contributor',
 *     filters(
 *         filter('user'),
 *         filter('service_account'),
 *         filter('team', 'member'),
 *         filter('organization', 'developer')
 *     )
 * );
 *
 * @see https://openfga.dev/docs/getting-started/perform-list-users Listing users
 */
function filters(UserTypeFilterInterface ...$filters): UserTypeFiltersInterface
{
    return new UserTypeFilters($filters);
}

// ==============================================================================
// Language Helpers
// ==============================================================================

/**
 * Helper for quickly obtaining a Language enum instance without extra boilerplate.
 *
 * This helper provides a convenient way to get Language enum instances either
 * by locale code (string) or by returning the default language. It eliminates
 * the need to write `Language::fromLocale()` or `Language::default()` in most cases.
 *
 * @param  string|null $locale Optional locale code (for example, 'en', 'de', 'pt_BR')
 * @return Language    The matching Language enum or default if locale not provided/found
 *
 * @example Getting default language
 * $defaultLang = lang(); // Returns Language::English
 * @example Getting language by locale
 * $portuguese = lang('pt_BR'); // Returns Language::PortugueseBrazilian
 * $german = lang('de'); // Returns Language::German
 * @example Using in client configuration
 * $client = new Client($url, language: lang('de'));
 */
function lang(?string $locale = null): Language
{
    if (null === $locale) {
        return Language::default();
    }

    return Language::fromLocale($locale) ?? Language::default();
}

/**
 * Helper for translating messages without needing to call Translator::trans() directly.
 *
 * This helper provides a convenient shortcut for translating Messages enum values
 * using the Translator class. It eliminates the need to write the full
 * `Translator::trans()` static method call and provides a more fluent API.
 *
 * @param Messages             $message    The message enum case to translate
 * @param array<string, mixed> $parameters Parameters to substitute in the message
 * @param Language|null        $language   Optional language override for translation
 *
 * @throws InvalidArgumentException If message translation parameters are invalid
 *
 * @return string The translated message
 *
 * @example Basic translation
 * $message = trans(Messages::NO_LAST_REQUEST_FOUND);
 * @example Translation with parameters
 * $message = trans(Messages::NETWORK_ERROR, ['message' => 'Connection timeout']);
 * @example Translation with specific language
 * $message = trans(Messages::AUTH_USER_MESSAGE_TOKEN_EXPIRED, [], Language::German);
 * @example Using in service error handling
 * throw new ClientException(trans(Messages::MODEL_NO_MODELS_IN_STORE, ['store_id' => $storeId]));
 */
function trans(Messages $message, array $parameters = [], ?Language $language = null): string
{
    return Translator::trans($message, $parameters, $language);
}

function context(
    callable $fn,
    ?ClientInterface $client = null,
    StoreInterface | string | null $store = null,
    AuthorizationModelInterface | string | null $model = null,
): mixed {
    return Context::with(
        fn: $fn,
        client: $client,
        store: $store,
        model: $model,
    );
}
