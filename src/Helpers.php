<?php

declare(strict_types=1);

namespace OpenFGA;

use Closure;
use Generator;
use InvalidArgumentException;
use OpenFGA\Context\Context;
use OpenFGA\Exceptions\{ClientError, ClientException, ClientThrowable};
use OpenFGA\Models\{AuthorizationModelInterface, BatchCheckItem, BatchCheckItemInterface, ConditionInterface, StoreInterface, TupleKey, TupleKeyInterface, UserObjectInterface, UserTypeFilter, UserTypeFilterInterface};
use OpenFGA\Models\Collections\{TupleKeys, TupleKeysInterface, UserTypeFilters, UserTypeFiltersInterface};
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Responses\{WriteTuplesResponse, WriteTuplesResponseInterface};
use OpenFGA\Results\{Failure, FailureInterface, ResultInterface, Success, SuccessInterface};
use OpenFGA\{Translation\Translator};
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
 * Parameters can be omitted when using the context() helper, which provides
 * ambient values for client, store, and model.
 *
 * @param ?ClientInterface                        $client              The OpenFGA client (optional if in context)
 * @param StoreInterface|string|null              $store               The store to operate on (optional if in context)
 * @param AuthorizationModelInterface|string|null $model               The authorization model to use (optional if in context)
 * @param TupleKeysInterface|null                 $writes              Collection of tuples to write
 * @param TupleKeysInterface|null                 $deletes             Collection of tuples to delete
 * @param int                                     $maxParallelRequests Maximum concurrent requests (default: 1)
 * @param int                                     $maxTuplesPerChunk   Maximum tuples per chunk (default: 100)
 * @param int                                     $maxRetries          Maximum retry attempts for failed chunks (default: 0)
 * @param float                                   $retryDelaySeconds   Delay between retries in seconds (default: 1.0)
 * @param bool                                    $stopOnFirstError    Stop processing remaining chunks on first error (default: false)
 *
 * @throws ClientException If required parameters are missing and not available in context
 * @throws Throwable       If the batch operation fails completely
 *
 * @return WriteTuplesResponseInterface The result of the batch operation
 *
 * @example Processing a large batch of tuple operations with explicit parameters
 * $writeTuples = tuples(
 *     tuple('user:anne', 'viewer', 'document:1'),
 *     tuple('user:bob', 'viewer', 'document:2'),
 *     // ... hundreds more tuples
 * );
 *
 * $result = writes($client, $store, $model, writes: $writeTuples);
 * echo "Success rate: " . ($result->getSuccessRate() * 100) . "%\n";
 * @example Using with context (no explicit client/store/model needed)
 * context(function() {
 *     $writeTuples = tuples(
 *         tuple('user:anne', 'viewer', 'document:1'),
 *         tuple('user:bob', 'viewer', 'document:2')
 *     );
 *     return writes(writes: $writeTuples);
 * }, client: $client, store: $store, model: $model);
 *
 * @see https://openfga.dev/docs/getting-started/update-tuples Working with relationship tuples
 */
function writes(
    ?ClientInterface $client = null,
    StoreInterface | string | null $store = null,
    AuthorizationModelInterface | string | null $model = null,
    ?TupleKeysInterface $writes = null,
    ?TupleKeysInterface $deletes = null,
    int $maxParallelRequests = 1,
    int $maxTuplesPerChunk = 100,
    int $maxRetries = 0,
    float $retryDelaySeconds = 1.0,
    bool $stopOnFirstError = false,
): WriteTuplesResponseInterface {
    // Fall back to context if parameters not provided
    $client ??= Context::getClient() ?? throw new ClientException(ClientError::Configuration);
    $store ??= Context::getStore() ?? throw new ClientException(ClientError::Validation, context: ['message' => trans(Messages::REQUEST_STORE_ID_EMPTY)]);
    $model ??= Context::getModel() ?? throw new ClientException(ClientError::Validation, context: ['message' => trans(Messages::REQUEST_MODEL_ID_EMPTY)]);

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
 * Parameters can be omitted when using the context() helper, which provides
 * ambient values for client.
 *
 * @param string           $name   The name of the store to create
 * @param ?ClientInterface $client An OpenFGA client instance (optional if in context)
 *
 * @throws ClientException If required parameters are missing and not available in context
 * @throws Throwable       If the store creation fails
 *
 * @return string The unique ID of the created store
 *
 * @example Creating a store with explicit client
 * $storeId = store('my-app-store', $client);
 * @example Using with context (no explicit client needed)
 * context(function() {
 *     return store('my-app-store');
 * }, client: $client);
 *
 * @see https://openfga.dev/docs/getting-started/create-store Creating and managing stores
 */
function store(string $name, ?ClientInterface $client = null): string
{
    // Fall back to context if parameters not provided
    $client ??= Context::getClient() ?? throw new ClientException(ClientError::Configuration);

    /** @var Responses\CreateStoreResponseInterface $response */
    $response = $client->createStore(name: $name)
        ->unwrap();

    return $response->getId();
}

/**
 * Define an authorization model using the OpenFGA DSL.
 *
 * Parameters can be omitted when using the context() helper, which provides
 * ambient values for client.
 *
 * @param string           $dsl    The authorization model DSL
 * @param ?ClientInterface $client An OpenFGA client (optional if in context)
 *
 * @throws ClientException If required parameters are missing and not available in context
 * @throws Throwable       If the authorization model creation fails
 *
 * @return AuthorizationModelInterface The authorization model
 *
 * @example Creating DSL model with explicit client
 * $model = dsl($dslString, $client);
 * @example Using with context (no explicit client needed)
 * context(function() use ($dslString) {
 *     return dsl($dslString);
 * }, client: $client);
 *
 * @see https://openfga.dev/docs/configuration-language
 */
function dsl(string $dsl, ?ClientInterface $client = null): AuthorizationModelInterface
{
    // Fall back to context if parameters not provided
    $client ??= Context::getClient() ?? throw new ClientException(ClientError::Configuration);

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
 * Parameters can be omitted when using the context() helper, which provides
 * ambient values for client, store, and model.
 *
 * @param TupleKeyInterface|TupleKeysInterface    $tuples        The tuple(s) to write
 * @param ?ClientInterface                        $client        The OpenFGA client (optional if in context)
 * @param StoreInterface|string|null              $store         The store to write to (optional if in context)
 * @param AuthorizationModelInterface|string|null $model         The authorization model to use (optional if in context)
 * @param bool                                    $transactional Whether to use transactional mode (default: true)
 *
 * @throws ClientException If required parameters are missing and not available in context
 * @throws Throwable       If the write operation fails
 *
 * @example Writing a single tuple with explicit parameters
 * write(tuple('user:anne', 'reader', 'document:budget'), $client, $store, $model);
 * @example Writing multiple tuples transactionally
 * write(tuples(
 *     tuple('user:anne', 'reader', 'document:budget'),
 *     tuple('user:anne', 'reader', 'document:forecast')
 * ), $client, $store, $model);
 * @example Using with context (no explicit client/store/model needed)
 * context(function() {
 *     write(tuple('user:bob', 'editor', 'document:proposal'));
 * }, client: $client, store: $store, model: $model);
 * @example Simple non-transactional write for resilience
 * write($tuples, $client, $store, $model, transactional: false);
 *
 * @see https://openfga.dev/docs/getting-started/update-tuples Working with relationship tuples
 */
function write(
    TupleKeyInterface | TupleKeysInterface $tuples,
    ?ClientInterface $client = null,
    StoreInterface | string | null $store = null,
    AuthorizationModelInterface | string | null $model = null,
    bool $transactional = true,
): void {
    // Fall back to context if parameters not provided
    $client ??= Context::getClient() ?? throw new ClientException(ClientError::Configuration);
    $store ??= Context::getStore() ?? throw new ClientException(ClientError::Validation, context: ['message' => trans(Messages::REQUEST_STORE_ID_EMPTY)]);
    $model ??= Context::getModel() ?? throw new ClientException(ClientError::Validation, context: ['message' => trans(Messages::REQUEST_MODEL_ID_EMPTY)]);

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
 * Parameters can be omitted when using the context() helper, which provides
 * ambient values for client, store, and model.
 *
 * @param TupleKeyInterface|TupleKeysInterface    $tuples        The tuple(s) to delete
 * @param ?ClientInterface                        $client        The OpenFGA client (optional if in context)
 * @param StoreInterface|string|null              $store         The store to delete from (optional if in context)
 * @param AuthorizationModelInterface|string|null $model         The authorization model to use (optional if in context)
 * @param bool                                    $transactional Whether to use transactional mode (default: true)
 *
 * @throws ClientException If required parameters are missing and not available in context
 * @throws Throwable       If the delete operation fails
 *
 * @example Deleting a single tuple with explicit parameters
 * delete(tuple('user:anne', 'reader', 'document:budget'), $client, $store, $model);
 * @example Deleting multiple tuples transactionally
 * delete(tuples(
 *     tuple('user:anne', 'reader', 'document:budget'),
 *     tuple('user:bob', 'editor', 'document:forecast')
 * ), $client, $store, $model);
 * @example Using with context (no explicit client/store/model needed)
 * context(function() {
 *     delete(tuple('user:bob', 'editor', 'document:proposal'));
 * }, client: $client, store: $store, model: $model);
 * @example Simple non-transactional delete for resilience
 * delete($tuples, $client, $store, $model, transactional: false);
 *
 * @see https://openfga.dev/docs/getting-started/update-tuples Working with relationship tuples
 */
function delete(
    TupleKeyInterface | TupleKeysInterface $tuples,
    ?ClientInterface $client = null,
    StoreInterface | string | null $store = null,
    AuthorizationModelInterface | string | null $model = null,
    bool $transactional = true,
): void {
    // Fall back to context if parameters not provided
    $client ??= Context::getClient() ?? throw new ClientException(ClientError::Configuration);
    $store ??= Context::getStore() ?? throw new ClientException(ClientError::Validation, context: ['message' => trans(Messages::REQUEST_STORE_ID_EMPTY)]);
    $model ??= Context::getModel() ?? throw new ClientException(ClientError::Validation, context: ['message' => trans(Messages::REQUEST_MODEL_ID_EMPTY)]);

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
 * Read relationship tuples with automatic pagination.
 *
 * This helper provides a simplified way to read tuples from OpenFGA with automatic
 * pagination to collect all results. It continues fetching pages until all tuples
 * matching the query are retrieved, making it easy to work with large datasets
 * without manual pagination handling.
 *
 * Parameters can be omitted when using the context() helper, which provides
 * ambient values for client and store.
 *
 * @param ?ClientInterface           $client      The OpenFGA client (optional if in context)
 * @param StoreInterface|string|null $store       The store to read from (optional if in context)
 * @param ?TupleKeyInterface         $tupleKey    Optional tuple key to filter results (null reads all tuples)
 * @param int                        $pageSize    Number of tuples per page (default: 50, max: 1000)
 * @param ?Consistency               $consistency Optional consistency level for the query
 *
 * @throws ClientException If required parameters are missing and not available in context
 * @throws Throwable       If the read operation fails
 *
 * @return array<TupleKeyInterface> Array of all tuples matching the query
 *
 * @example Reading all tuples with explicit parameters
 * $allTuples = read($client, $store);
 * foreach ($allTuples as $tuple) {
 *     echo "{$tuple->getUser()} {$tuple->getRelation()} {$tuple->getObject()}\n";
 * }
 * @example Reading tuples with filtering
 * $userTuples = read($client, $store, tuple('user:anne', '', ''));
 * @example Using with context (no explicit client/store needed)
 * context(function() {
 *     $allTuples = read();
 *     return count($allTuples);
 * }, client: $client, store: $store);
 * @example Reading with specific page size and consistency
 * $tuples = read($client, $store, pageSize: 100, consistency: Consistency::HigherConsistency);
 *
 * @see https://openfga.dev/docs/getting-started/reading-tuples Reading relationship tuples
 */
function read(
    ?ClientInterface $client = null,
    StoreInterface | string | null $store = null,
    ?TupleKeyInterface $tupleKey = null,
    int $pageSize = 50,
    ?Consistency $consistency = null,
): array {
    // Ensure pageSize is positive
    if (0 >= $pageSize) {
        $pageSize = 50;
    }
    // Fall back to context if parameters not provided
    $client ??= Context::getClient() ?? throw new ClientException(ClientError::Configuration);
    $store ??= Context::getStore() ?? throw new ClientException(ClientError::Validation, context: ['message' => trans(Messages::REQUEST_STORE_ID_EMPTY)]);

    $allTuples = [];
    $continuationToken = null;

    do {
        /** @var Responses\ReadTuplesResponseInterface $response */
        $response = $client->readTuples(
            store: $store,
            tupleKey: $tupleKey,
            pageSize: $pageSize,
            continuationToken: $continuationToken,
            consistency: $consistency,
        )->unwrap();

        // Add tuples from this page to our collection
        foreach ($response->getTuples() as $tuple) {
            $allTuples[] = $tuple->getKey();
        }

        // Get continuation token for next page
        $continuationToken = $response->getContinuationToken();
    } while (null !== $continuationToken);

    return $allTuples;
}

/**
 * Check for a relationship with guaranteed boolean result.
 *
 * This helper safely checks permissions and returns false for any error
 * condition (network failures, authentication issues, malformed requests, etc.).
 * Use the standard client->check() method if you need detailed error information.
 *
 * Parameters can be omitted when using the context() helper, which provides
 * ambient values for client, store, and model.
 *
 * You can pass either a TupleKey object or individual user, relation, and object parameters:
 *
 * @param  ?TupleKeyInterface                      $tuple            Optional pre-built tuple key to check
 * @param  ?string                                 $user             Optional user identifier (required if $tuple not provided)
 * @param  ?string                                 $relation         Optional relation (required if $tuple not provided)
 * @param  ?string                                 $object           Optional object identifier (required if $tuple not provided)
 * @param  ?ConditionInterface                     $condition        Optional condition for the tuple
 * @param  ?ClientInterface                        $client           The OpenFGA client (optional if in context)
 * @param  StoreInterface|string|null              $store            The store to use (optional if in context)
 * @param  AuthorizationModelInterface|string|null $model            The authorization model to use (optional if in context)
 * @param  ?bool                                   $trace            Whether to trace the check
 * @param  ?object                                 $context          Context to use for the check
 * @param  ?TupleKeysInterface                     $contextualTuples Contextual tuples to use for the check
 * @param  ?Consistency                            $consistency      Consistency level to use for the check
 * @return bool                                    True if explicitly allowed, false for denied or any error
 *
 * @example Using with a TupleKey object and explicit parameters
 * allowed($tupleKey, client: $client, store: $store, model: $model)
 * @example Using with individual parameters
 * allowed(user: 'user:anne', relation: 'viewer', object: 'document:budget', client: $client, store: $store, model: $model)
 * @example Using with context (no explicit client/store/model needed)
 * context(function() {
 *     return allowed(tuple: tuple('user:anne', 'viewer', 'document:budget'));
 * }, client: $client, store: $store, model: $model);
 * @example Using with all options
 * allowed(user: 'user:anne', relation: 'viewer', object: 'document:budget', trace: true, client: $client, store: $store, model: $model)
 *
 * @see https://openfga.dev/docs/getting-started/perform-check#02-calling-check-api Checking permissions
 */
function allowed(
    ?TupleKeyInterface $tuple = null,
    ?string $user = null,
    ?string $relation = null,
    ?string $object = null,
    ?ConditionInterface $condition = null,
    ?ClientInterface $client = null,
    StoreInterface | string | null $store = null,
    AuthorizationModelInterface | string | null $model = null,
    ?bool $trace = null,
    ?object $context = null,
    ?TupleKeysInterface $contextualTuples = null,
    ?Consistency $consistency = null,
): bool {
    try {
        // Fall back to context if parameters not provided
        $client ??= Context::getClient() ?? throw new ClientException(ClientError::Configuration);
        $store ??= Context::getStore() ?? throw new ClientException(ClientError::Validation, context: ['message' => trans(Messages::REQUEST_STORE_ID_EMPTY)]);
        $model ??= Context::getModel() ?? throw new ClientException(ClientError::Validation, context: ['message' => trans(Messages::REQUEST_MODEL_ID_EMPTY)]);

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
 * Parameters can be omitted when using the context() helper, which provides
 * ambient values for client and store.
 *
 * @param AuthorizationModelInterface $model  The authorization model to create
 * @param ?ClientInterface            $client An OpenFGA client (optional if in context)
 * @param StoreInterface|string|null  $store  The store to use (optional if in context)
 *
 * @throws ClientException If required parameters are missing and not available in context
 * @throws Throwable       If the model creation fails
 *
 * @return string The authorization model ID
 *
 * @example Creating a model with explicit parameters
 * $modelId = model($authModel, $client, $store);
 * @example Using with context (no explicit client/store needed)
 * context(function() use ($authModel) {
 *     return model($authModel);
 * }, client: $client, store: $store);
 *
 * @see https://openfga.dev/docs/getting-started/configure-model#step-by-step Creating an authorization model
 */
function model(AuthorizationModelInterface $model, ?ClientInterface $client = null, StoreInterface | string | null $store = null): string
{
    // Fall back to context if parameters not provided
    $client ??= Context::getClient() ?? throw new ClientException(ClientError::Configuration);
    $store ??= Context::getStore() ?? throw new ClientException(ClientError::Validation, context: ['message' => trans(Messages::REQUEST_STORE_ID_EMPTY)]);

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
 * Parameters can be omitted when using the context() helper, which provides
 * ambient values for client, store, and model.
 *
 * @param  string                                  $type             The object type to search for (e.g., 'document', 'folder')
 * @param  string                                  $relation         The relation to check (e.g., 'viewer', 'owner')
 * @param  string                                  $user             The user to check for (e.g., 'user:anne')
 * @param  ?ClientInterface                        $client           The OpenFGA client (optional if in context)
 * @param  StoreInterface|string|null              $store            The store to query (optional if in context)
 * @param  AuthorizationModelInterface|string|null $model            The authorization model to use (optional if in context)
 * @param  ?object                                 $context          Optional context for condition evaluation
 * @param  ?TupleKeysInterface                     $contextualTuples Optional contextual tuples for the query
 * @param  ?Consistency                            $consistency      Optional consistency level for the query
 * @return array<string>                           Array of object IDs the user has the specified relation to, or empty array on any error
 *
 * @example Find all documents a user can view with explicit parameters
 * $documents = objects('document', 'viewer', 'user:anne', $client, $store, $model);
 * // Returns: ['document:budget', 'document:forecast', 'document:report'] or [] on error
 * @example Find all folders a user owns with contextual tuples
 * $ownedFolders = objects('folder', 'owner', 'user:bob', $client, $store, $model,
 *     contextualTuples: tuples(
 *         tuple('user:bob', 'owner', 'folder:temp')
 *     )
 * );
 * @example Using with context (no explicit client/store/model needed)
 * context(function() {
 *     return objects('document', 'viewer', 'user:anne');
 * }, client: $client, store: $store, model: $model);
 * @example Safe to use even with network issues, invalid stores, etc.
 * $userDocs = objects('document', 'viewer', 'user:anne', $client, $store, $model);
 * // Will return [] if there are any errors (network, auth, validation, etc.)
 *
 * @see https://openfga.dev/docs/getting-started/perform-list-objects Listing objects
 */
function objects(
    string $type,
    string $relation,
    string $user,
    ?ClientInterface $client = null,
    StoreInterface | string | null $store = null,
    AuthorizationModelInterface | string | null $model = null,
    ?object $context = null,
    ?TupleKeysInterface $contextualTuples = null,
    ?Consistency $consistency = null,
): array {
    try {
        // Fall back to context if parameters not provided
        $client ??= Context::getClient() ?? throw new ClientException(ClientError::Configuration);
        $store ??= Context::getStore() ?? throw new ClientException(ClientError::Validation, context: ['message' => trans(Messages::REQUEST_STORE_ID_EMPTY)]);
        $model ??= Context::getModel() ?? throw new ClientException(ClientError::Validation, context: ['message' => trans(Messages::REQUEST_MODEL_ID_EMPTY)]);

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
 * Parameters can be omitted when using the context() helper, which provides
 * ambient values for client and store.
 *
 * @param ?ClientInterface           $client The OpenFGA client (optional if in context)
 * @param StoreInterface|string|null $store  The store to list models from (optional if in context)
 *
 * @throws ClientException If required parameters are missing and not available in context
 * @throws Throwable       If the operation fails
 *
 * @return array<AuthorizationModelInterface> Array of all authorization models in the store
 *
 * @example Get all authorization models in a store with explicit parameters
 * $allModels = models($client, $storeId);
 * foreach ($allModels as $model) {
 *     echo "Model: {$model->getId()}\n";
 * }
 * @example Using with context (no explicit client/store needed)
 * context(function() {
 *     $allModels = models();
 *     return end($allModels); // Get latest model
 * }, client: $client, store: $store);
 * @example Find the latest authorization model
 * $allModels = models($client, $store);
 * $latestModel = end($allModels); // Models are typically returned in chronological order
 *
 * @see https://openfga.dev/docs/getting-started/immutable-models#viewing-all-the-authorization-models Listing models
 */
function models(
    ?ClientInterface $client = null,
    StoreInterface | string | null $store = null,
): array {
    // Fall back to context if parameters not provided
    $client ??= Context::getClient() ?? throw new ClientException(ClientError::Configuration);
    $store ??= Context::getStore() ?? throw new ClientException(ClientError::Validation, context: ['message' => trans(Messages::REQUEST_STORE_ID_EMPTY)]);

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
 * Parameters can be omitted when using the context() helper, which provides
 * ambient values for client, store, and model.
 *
 * @param ?ClientInterface                        $client    The OpenFGA client (optional if in context)
 * @param StoreInterface|string|null              $store     The store to check against (optional if in context)
 * @param AuthorizationModelInterface|string|null $model     The authorization model to use (optional if in context)
 * @param BatchCheckItemInterface                 ...$checks Variable number of BatchCheckItem instances
 *
 * @throws ClientException          If required parameters are missing and not available in context
 * @throws ClientThrowable          If batch item validation fails
 * @throws InvalidArgumentException If check specification is invalid
 * @throws ReflectionException      If schema reflection fails
 * @throws Throwable                If the batch check operation fails
 *
 * @return array<string, bool> Map of correlation ID to allowed/denied result
 *
 * @example Simple batch check with explicit parameters
 * $results = checks(
 *     $client, $store, $model,
 *     new BatchCheckItem(tuple('user:anne', 'viewer', 'document:budget'), 'anne-check'),
 *     new BatchCheckItem(tuple('user:bob', 'editor', 'document:budget'), 'bob-check')
 * );
 * // Returns: ['anne-check' => true, 'bob-check' => false]
 * @example Using with context (no explicit client/store/model needed)
 * context(function() {
 *     return checks(
 *         null, null, null,
 *         new BatchCheckItem(tuple('user:anne', 'viewer', 'document:budget'), 'anne-check'),
 *         new BatchCheckItem(tuple('user:bob', 'editor', 'document:budget'), 'bob-check')
 *     );
 * }, client: $client, store: $store, model: $model);
 *
 * @see https://openfga.dev/docs/getting-started/perform-check#03-calling-batch-check-api Batch checking permissions
 */
function checks(
    ?ClientInterface $client = null,
    StoreInterface | string | null $store = null,
    AuthorizationModelInterface | string | null $model = null,
    BatchCheckItemInterface ...$checks,
): array {
    // Fall back to context if parameters not provided
    $client ??= Context::getClient() ?? throw new ClientException(ClientError::Configuration);
    $store ??= Context::getStore() ?? throw new ClientException(ClientError::Validation, context: ['message' => trans(Messages::REQUEST_STORE_ID_EMPTY)]);
    $model ??= Context::getModel() ?? throw new ClientException(ClientError::Validation, context: ['message' => trans(Messages::REQUEST_MODEL_ID_EMPTY)]);

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
 * Parameters can be omitted when using the context() helper, which provides
 * ambient values for client, store, and model.
 *
 * @param  string                                           $object           The object to check relationships for (e.g., 'document:budget')
 * @param  string                                           $relation         The relationship to check (e.g., 'viewer', 'editor', 'owner')
 * @param  UserTypeFilterInterface|UserTypeFiltersInterface $filters          Filters for user types to include
 * @param  ?ClientInterface                                 $client           The OpenFGA client (optional if in context)
 * @param  StoreInterface|string|null                       $store            The store to query (optional if in context)
 * @param  AuthorizationModelInterface|string|null          $model            The authorization model to use (optional if in context)
 * @param  ?object                                          $context          Optional additional context for evaluation
 * @param  ?TupleKeysInterface                              $contextualTuples Optional contextual tuples for the query
 * @param  ?Consistency                                     $consistency      Optional consistency level for the query
 * @return array<string>                                    Array of user identifiers who have the specified relationship, or empty array on error
 *
 * @example Find all users who can view a document with explicit parameters
 * $viewers = users('document:budget', 'viewer', filter('user'), $client, $store, $model);
 * // Returns: ['user:anne', 'user:bob', 'user:charlie']
 * @example Find both users and groups with edit access
 * $editors = users('document:budget', 'editor',
 *     filters(filter('user'), filter('group')),
 *     $client, $store, $model
 * );
 * // Returns: ['user:anne', 'group:engineering', 'user:david']
 * @example Using with context (no explicit client/store/model needed)
 * context(function() {
 *     return users('document:budget', 'viewer', filter('user'));
 * }, client: $client, store: $store, model: $model);
 * @example Find users with contextual tuples
 * $editors = users('document:technical-spec', 'editor', filter('user'),
 *     $client, $store, $model,
 *     contextualTuples: tuples(
 *         tuple('user:anne', 'member', 'team:engineering')
 *     )
 * );
 *
 * @see https://openfga.dev/docs/getting-started/perform-list-users Listing users
 */
function users(
    string $object,
    string $relation,
    UserTypeFiltersInterface | UserTypeFilterInterface $filters,
    ?ClientInterface $client = null,
    StoreInterface | string | null $store = null,
    AuthorizationModelInterface | string | null $model = null,
    ?object $context = null,
    ?TupleKeysInterface $contextualTuples = null,
    ?Consistency $consistency = null,
): array {
    try {
        // Fall back to context if parameters not provided
        $client ??= Context::getClient() ?? throw new ClientException(ClientError::Configuration);
        $store ??= Context::getStore() ?? throw new ClientException(ClientError::Validation, context: ['message' => trans(Messages::REQUEST_STORE_ID_EMPTY)]);
        $model ??= Context::getModel() ?? throw new ClientException(ClientError::Validation, context: ['message' => trans(Messages::REQUEST_MODEL_ID_EMPTY)]);

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

// ==============================================================================
// Context Helpers
// ==============================================================================

/**
 * Execute a callable within an ambient context.
 *
 * This helper provides a convenient way to set client, store, and model values
 * that can be used implicitly by other helper functions within the callable.
 * Contexts support inheritance - child contexts automatically inherit values
 * from their parent context unless explicitly overridden.
 *
 * @param callable                                $fn     The callable to execute within the context
 * @param ?ClientInterface                        $client Optional client for the context
 * @param StoreInterface|string|null              $store  Optional store for the context
 * @param AuthorizationModelInterface|string|null $model  Optional model for the context
 *
 * @throws Throwable Re-throws any exception from the callable
 *
 * @return mixed The result of the callable execution
 *
 * @example Basic usage with all parameters
 * $result = context(function() {
 *     // All helper functions can now omit client/store/model parameters
 *     $allowed = allowed(tuple: tuple('user:anne', 'viewer', 'doc:1'));
 *     $users = users('doc:1', 'viewer', filter('user'));
 *     write(tuple('user:bob', 'editor', 'doc:1'));
 *     return $allowed;
 * }, client: $client, store: $store, model: $model);
 * @example Nested contexts with inheritance
 * context(function() {
 *     // Uses outer context's client and store
 *     $users1 = users('doc:1', 'viewer', filter('user')); // Uses model1
 *
 *     context(function() {
 *         // Inherits client/store, but uses different model
 *         $users2 = users('doc:2', 'editor', filter('user')); // Uses model2
 *     }, model: $model2);
 *
 * }, client: $client, store: $store, model: $model1);
 * @example Partial context override
 * context(function() {
 *     // Set base client and store
 *     context(function() {
 *         // Override just the store for this operation
 *         $allowed = allowed(tuple: tuple('user:anne', 'admin', 'store:settings'));
 *     }, store: $adminStore);
 * }, client: $client, store: $userStore, model: $model);
 */
/**
 * @param callable(): mixed $fn
 *
 * @throws Throwable
 */
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
