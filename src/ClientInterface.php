<?php

declare(strict_types=1);

namespace OpenFGA;

use DateTimeImmutable;
use InvalidArgumentException;
use OpenFGA\Exceptions\ClientThrowable;
use OpenFGA\Models\{AuthorizationModelInterface, StoreInterface, TupleKeyInterface};
use OpenFGA\Models\Collections\{AssertionsInterface, ConditionsInterface, TupleKeysInterface, TypeDefinitionsInterface, UserTypeFiltersInterface};
use OpenFGA\Models\Collections\BatchCheckItemsInterface;
use OpenFGA\Models\Enums\{Consistency, SchemaVersion};
use OpenFGA\Results\{FailureInterface, SuccessInterface};
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};

/**
 * OpenFGA Client Interface for relationship-based access control operations.
 *
 * This interface defines the complete API for interacting with OpenFGA services,
 * providing methods for managing stores, authorization models, relationship tuples,
 * and performing authorization checks. The client implements the Result pattern,
 * returning Success or Failure objects instead of throwing exceptions.
 *
 * All operations support OpenFGA's core concepts including stores for data isolation,
 * authorization models for defining permission structures, and relationship tuples
 * for expressing user-object relationships.
 *
 * @see https://openfga.dev/docs/concepts OpenFGA core concepts and authorization model
 * @see https://openfga.dev/docs/getting-started Getting started with OpenFGA
 */
interface ClientInterface
{
    /**
     * Retrieves the last HTTP request made by the client.
     *
     * @throws InvalidArgumentException If request validation fails
     * @throws ClientThrowable          If no last request has been made
     *
     * @return HttpRequestInterface The last request
     *
     * @example Accessing the last request for debugging
     * $result = $client->check(
     *     store: 'store-id',
     *     model: 'model-id',
     *     tupleKey: new TupleKey('user:anne', 'viewer', 'document:budget')
     * );
     *
     * $lastRequest = $client->assertLastRequest();
     *
     * echo "Method: " . $lastRequest->getMethod();
     * echo "URL: " . $lastRequest->getUri();
     * echo "Headers: " . json_encode($lastRequest->getHeaders());
     */
    public function assertLastRequest(): HttpRequestInterface;

    /**
     * Performs multiple authorization checks in a single batch request.
     *
     * This method allows checking multiple user-object relationships simultaneously
     * for better performance when multiple authorization decisions are needed.
     * Each check in the batch has a correlation ID to map results back to the
     * original requests.
     *
     * The batch check operation supports the same features as individual checks:
     * contextual tuples, custom contexts, and detailed error information for each check.
     *
     * @param StoreInterface|string              $store  The store to check against
     * @param AuthorizationModelInterface|string $model  The authorization model to use
     * @param BatchCheckItemsInterface           $checks The batch check items
     *
     * @throws InvalidArgumentException If request validation fails
     * @throws ClientThrowable          If the request cannot be built or sent
     *
     * @return FailureInterface|SuccessInterface The batch check results
     *
     * @example Batch checking multiple permissions efficiently
     * $checks = new BatchCheckItems([
     *     new BatchCheckItem(
     *         tupleKey: new TupleKey('user:anne', 'viewer', 'document:budget'),
     *         correlationId: 'check-anne-viewer'
     *     ),
     *     new BatchCheckItem(
     *         tupleKey: new TupleKey('user:bob', 'editor', 'document:budget'),
     *         correlationId: 'check-bob-editor'
     *     ),
     *     new BatchCheckItem(
     *         tupleKey: new TupleKey('user:charlie', 'owner', 'document:roadmap'),
     *         correlationId: 'check-charlie-owner'
     *     ),
     * ]);
     *
     * $result = $client->batchCheck(
     *     store: 'store-id',
     *     model: 'model-id',
     *     checks: $checks
     * );
     *
     * if ($result->success()) {
     *     $responses = $result->value()->getResults();
     *     foreach ($responses as $response) {
     *         echo $response->getCorrelationId() . ': ' .
     *              ($response->getAllowed() ? 'ALLOWED' : 'DENIED') . "\n";
     *     }
     * }
     *
     * @see https://openfga.dev/docs/api#/Relationship%20Queries/BatchCheck Batch check API reference
     */
    public function batchCheck(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        BatchCheckItemsInterface $checks,
    ): FailureInterface | SuccessInterface;

    /**
     * Checks if a user has a specific relationship with an object.
     *
     * Performs an authorization check to determine if a user has a particular
     * relationship with an object based on the configured authorization model.
     * This is the core operation for making authorization decisions in OpenFGA.
     *
     * @param  StoreInterface|string              $store            The store to check against
     * @param  AuthorizationModelInterface|string $model            The authorization model to use
     * @param  TupleKeyInterface                  $tupleKey         The relationship to check
     * @param  bool|null                          $trace            Whether to include a trace in the response
     * @param  object|null                        $context          Additional context for the check
     * @param  TupleKeysInterface|null            $contextualTuples Additional tuples for contextual evaluation
     * @param  Consistency|null                   $consistency      Override the default consistency level
     * @return FailureInterface|SuccessInterface  Success contains CheckResponseInterface, Failure contains Throwable
     *
     * @example Basic permission check
     * $result = $client->check(
     *     store: 'store-id',
     *     model: 'model-id',
     *     tupleKey: new TupleKey('user:anne', 'reader', 'document:budget')
     * );
     *
     * if ($result->success()) {
     *     $allowed = $result->value()->getAllowed();
     *     if ($allowed) {
     *         // User has permission
     *     }
     * }
     * @example Check with contextual tuples
     * $contextualTuples = new TupleKeys([
     *     new TupleKey('user:anne', 'member', 'team:finance')
     * ]);
     *
     * $result = $client->check(
     *     store: 'store-id',
     *     model: 'model-id',
     *     tupleKey: new TupleKey('user:anne', 'reader', 'document:budget'),
     *     contextualTuples: $contextualTuples
     * );
     *
     * @see https://openfga.dev/docs/getting-started/perform-check Performing authorization checks
     */
    public function check(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        TupleKeyInterface $tupleKey,
        ?bool $trace = null,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): FailureInterface | SuccessInterface;

    /**
     * Creates a new authorization model with the given type definitions and conditions.
     *
     * Authorization models define the permission structure for your application,
     * including object types, relationships, and how permissions are computed.
     * Models are immutable once created and identified by a unique ID.
     *
     * @param  StoreInterface|string             $store           The store to create the model in
     * @param  TypeDefinitionsInterface          $typeDefinitions The type definitions for the model
     * @param  ConditionsInterface|null          $conditions      The conditions for the model
     * @param  SchemaVersion                     $schemaVersion   The schema version to use (default: 1.1)
     * @return FailureInterface|SuccessInterface Success contains CreateAuthorizationModelResponseInterface, Failure contains Throwable
     *
     * @example Creating a document authorization model with DSL (recommended)
     * // Using DSL is usually easier than manually building type definitions
     * $dsl = '
     *     model
     *       schema 1.1
     *
     *     type user
     *
     *     type document
     *       relations
     *         define owner: [user]
     *         define editor: [user] or owner
     *         define viewer: [user] or editor
     * ';
     *
     * $authModel = $client->dsl($dsl)->unwrap();
     * $result = $client->createAuthorizationModel(
     *     store: 'store-id',
     *     typeDefinitions: $authModel->getTypeDefinitions()
     * );
     *
     * if ($result->success()) {
     *     $modelId = $result->value()->getAuthorizationModelId();
     *     echo "Created model: {$modelId}";
     * }
     *
     * @see https://openfga.dev/docs/getting-started/configure-model Configuring authorization models
     * @see https://openfga.dev/docs/getting-started/immutable-models Understanding model immutability
     */
    public function createAuthorizationModel(
        StoreInterface | string $store,
        TypeDefinitionsInterface $typeDefinitions,
        ?ConditionsInterface $conditions = null,
        SchemaVersion $schemaVersion = SchemaVersion::V1_1,
    ): FailureInterface | SuccessInterface;

    /**
     * Creates a new store with the given name.
     *
     * Stores provide data isolation for different applications or environments.
     * Each store maintains its own authorization models, relationship tuples,
     * and provides complete separation from other stores.
     *
     * @param  string                            $name The name for the new store
     * @return FailureInterface|SuccessInterface Success contains CreateStoreResponseInterface, Failure contains Throwable
     *
     * @see https://openfga.dev/docs/getting-started/create-store Creating and managing stores
     */
    public function createStore(
        string $name,
    ): FailureInterface | SuccessInterface;

    /**
     * Deletes a store.
     *
     * @param  StoreInterface|string             $store The store to delete
     * @return FailureInterface|SuccessInterface Success contains DeleteStoreResponseInterface, Failure contains Throwable
     */
    public function deleteStore(
        StoreInterface | string $store,
    ): FailureInterface | SuccessInterface;

    /**
     * Parses a DSL string and returns an AuthorizationModel.
     *
     * The Domain Specific Language (DSL) provides a human-readable way to define
     * authorization models using intuitive syntax for relationships and permissions.
     * This method converts DSL text into a structured authorization model object.
     *
     * @param string $dsl The DSL string to parse
     *
     * @throws Exceptions\SerializationException If the DSL syntax is invalid
     *
     * @return FailureInterface|SuccessInterface Success contains AuthorizationModelInterface, Failure contains Throwable
     *
     * @example Parse a complete authorization model from DSL
     * $dsl = '
     *     model
     *       schema 1.1
     *
     *     type user
     *
     *     type organization
     *       relations
     *         define member: [user]
     *
     *     type document
     *       relations
     *         define owner: [user]
     *         define editor: [user, organization#member] or owner
     *         define viewer: [user, organization#member] or editor
     * ';
     *
     * $result = $client->dsl($dsl);
     *
     * if ($result->success()) {
     *     $authModel = $result->value();
     *     echo "Parsed model with " . count($authModel->getTypeDefinitions()) . " types";
     * }
     *
     * @see https://openfga.dev/docs/authorization-concepts OpenFGA authorization concepts
     */
    public function dsl(string $dsl): FailureInterface | SuccessInterface;

    /**
     * Expands a relationship tuple to show all users that have the relationship.
     *
     * @param  StoreInterface|string                   $store            The store containing the tuple
     * @param  TupleKeyInterface                       $tupleKey         The tuple to expand
     * @param  AuthorizationModelInterface|string|null $model            The authorization model to use
     * @param  TupleKeysInterface|null                 $contextualTuples Additional tuples for contextual evaluation
     * @param  Consistency|null                        $consistency      Override the default consistency level
     * @return FailureInterface|SuccessInterface       Success contains ExpandResponseInterface, Failure contains Throwable
     */
    public function expand(
        StoreInterface | string $store,
        TupleKeyInterface $tupleKey,
        AuthorizationModelInterface | string | null $model = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): FailureInterface | SuccessInterface;

    /**
     * Retrieves an authorization model by ID.
     *
     * @param  StoreInterface|string              $store The store containing the model
     * @param  AuthorizationModelInterface|string $model The model to retrieve
     * @return FailureInterface|SuccessInterface  Success contains GetAuthorizationModelResponseInterface, Failure contains Throwable
     */
    public function getAuthorizationModel(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
    ): FailureInterface | SuccessInterface;

    /**
     * Retrieves the last HTTP request made by the client.
     *
     * @return ?HttpRequestInterface The last request, or null if no request has been made
     */
    public function getLastRequest(): ?HttpRequestInterface;

    /**
     * Retrieves the last HTTP response received by the client.
     *
     * @return ?HttpResponseInterface The last response, or null if no response has been received
     */
    public function getLastResponse(): ?HttpResponseInterface;

    /**
     * Retrieves store details by ID.
     *
     * @param  StoreInterface|string             $store The store to retrieve
     * @return FailureInterface|SuccessInterface Success contains GetStoreResponseInterface, Failure contains Throwable
     */
    public function getStore(
        StoreInterface | string $store,
    ): FailureInterface | SuccessInterface;

    /**
     * Lists authorization models in a store with pagination.
     *
     * @param StoreInterface|string $store             The store to list models from
     * @param string|null           $continuationToken Token for pagination
     * @param int|null              $pageSize          Maximum number of models to return (must be positive)
     *
     * @throws InvalidArgumentException If pageSize is not a positive integer
     *
     * @return FailureInterface|SuccessInterface Success contains ListAuthorizationModelsResponseInterface, Failure contains Throwable
     */
    public function listAuthorizationModels(
        StoreInterface | string $store,
        ?string $continuationToken = null,
        ?int $pageSize = null,
    ): FailureInterface | SuccessInterface;

    /**
     * Lists objects that have a specific relationship with a user.
     *
     * @param  StoreInterface|string              $store            The store to query
     * @param  AuthorizationModelInterface|string $model            The authorization model to use
     * @param  string                             $type             The type of objects to list
     * @param  string                             $relation         The relationship to check
     * @param  string                             $user             The user to check relationships for
     * @param  object|null                        $context          Additional context for evaluation
     * @param  TupleKeysInterface|null            $contextualTuples Additional tuples for contextual evaluation
     * @param  Consistency|null                   $consistency      Override the default consistency level
     * @return FailureInterface|SuccessInterface  Success contains ListObjectsResponseInterface, Failure contains Throwable
     *
     * @example List all documents a user can view
     * $result = $client->listObjects(
     *     store: 'store-id',
     *     model: 'model-id',
     *     type: 'document',
     *     relation: 'viewer',
     *     user: 'user:anne'
     * );
     *
     * if ($result->success()) {
     *     $objects = $result->value()->getObjects();
     *     echo "Anne can view " . count($objects) . " documents:\n";
     *     foreach ($objects as $object) {
     *         echo "- {$object}\n";
     *     }
     * }
     * @example List objects with contextual evaluation
     * // Check what documents anne can edit, considering her team membership
     * $contextualTuples = new TupleKeys([
     *     new TupleKey('user:anne', 'member', 'team:engineering')
     * ]);
     *
     * $result = $client->listObjects(
     *     store: 'store-id',
     *     model: 'model-id',
     *     type: 'document',
     *     relation: 'editor',
     *     user: 'user:anne',
     *     contextualTuples: $contextualTuples
     * );
     */
    public function listObjects(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        string $type,
        string $relation,
        string $user,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): FailureInterface | SuccessInterface;

    /**
     * Lists all stores with pagination.
     *
     * @param string|null       $continuationToken Token for pagination
     * @param positive-int|null $pageSize          Maximum number of stores to return
     *
     * @throws InvalidArgumentException If pageSize is not a positive integer
     *
     * @return FailureInterface|SuccessInterface Success contains ListStoresResponseInterface, Failure contains Throwable
     */
    public function listStores(
        ?string $continuationToken = null,
        ?int $pageSize = null,
    ): FailureInterface | SuccessInterface;

    /**
     * Lists changes to relationship tuples in a store.
     *
     * @param StoreInterface|string  $store             The store to list changes for
     * @param string|null            $continuationToken Token for pagination
     * @param positive-int|null      $pageSize          Maximum number of changes to return
     * @param string|null            $type              Filter changes by type
     * @param DateTimeImmutable|null $startTime         Only include changes at or after this time (inclusive)
     *
     * @throws InvalidArgumentException If pageSize is not a positive integer
     *
     * @return FailureInterface|SuccessInterface Success contains ListTupleChangesResponseInterface, Failure contains Throwable
     */
    public function listTupleChanges(
        StoreInterface | string $store,
        ?string $continuationToken = null,
        ?int $pageSize = null,
        ?string $type = null,
        ?DateTimeImmutable $startTime = null,
    ): FailureInterface | SuccessInterface;

    /**
     * Lists users that have a specific relationship with an object.
     *
     * @param  StoreInterface|string              $store            The store to query
     * @param  AuthorizationModelInterface|string $model            The authorization model to use
     * @param  string                             $object           The object to check relationships for
     * @param  string                             $relation         The relationship to check
     * @param  UserTypeFiltersInterface           $userFilters      Filters for user types to include
     * @param  object|null                        $context          Additional context for evaluation
     * @param  TupleKeysInterface|null            $contextualTuples Additional tuples for contextual evaluation
     * @param  Consistency|null                   $consistency      Override the default consistency level
     * @return FailureInterface|SuccessInterface  Success contains ListUsersResponseInterface, Failure contains Throwable
     *
     * @example List all users who can view a document
     * $userFilters = new UserTypeFilters([
     *     new UserTypeFilter('user') // Only include direct users, not groups
     * ]);
     *
     * $result = $client->listUsers(
     *     store: 'store-id',
     *     model: 'model-id',
     *     object: 'document:budget',
     *     relation: 'viewer',
     *     userFilters: $userFilters
     * );
     *
     * if ($result->success()) {
     *     $users = $result->value()->getUsers();
     *     echo "Users who can view the budget document:\n";
     *     foreach ($users as $user) {
     *         echo "- {$user}\n";
     *     }
     * }
     * @example Find both users and groups with access
     * $userFilters = new UserTypeFilters([
     *     new UserTypeFilter('user'),
     *     new UserTypeFilter('group')
     * ]);
     *
     * $result = $client->listUsers(
     *     store: 'store-id',
     *     model: 'model-id',
     *     object: 'document:sensitive',
     *     relation: 'editor',
     *     userFilters: $userFilters
     * );
     */
    public function listUsers(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        string $object,
        string $relation,
        UserTypeFiltersInterface $userFilters,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): FailureInterface | SuccessInterface;

    /**
     * Retrieves assertions for an authorization model.
     *
     * @param  StoreInterface|string              $store The store containing the model
     * @param  AuthorizationModelInterface|string $model The model to get assertions for
     * @return FailureInterface|SuccessInterface  Success contains ReadAssertionsResponseInterface, Failure contains Throwable
     */
    public function readAssertions(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
    ): FailureInterface | SuccessInterface;

    /**
     * Reads relationship tuples from a store with optional filtering and pagination.
     *
     * @param StoreInterface|string $store             The store to read from
     * @param TupleKeyInterface     $tupleKey          Filter tuples by this key (return all if null)
     * @param string|null           $continuationToken Token for pagination
     * @param positive-int|null     $pageSize          Maximum number of tuples to return
     * @param Consistency|null      $consistency       Override the default consistency level
     *
     * @throws InvalidArgumentException If pageSize is not a positive integer
     *
     * @return FailureInterface|SuccessInterface Success contains ReadTuplesResponseInterface, Failure contains Throwable
     */
    public function readTuples(
        StoreInterface | string $store,
        TupleKeyInterface $tupleKey,
        ?string $continuationToken = null,
        ?int $pageSize = null,
        ?Consistency $consistency = null,
    ): FailureInterface | SuccessInterface;

    /**
     * Streams objects that a user has a specific relationship with.
     *
     * Returns all objects of a given type that the specified user has a relationship
     * with, using a streaming response for memory-efficient processing of large result sets.
     * This is ideal for handling thousands of objects without loading them all into memory.
     *
     * @param  StoreInterface|string              $store            The store to query
     * @param  AuthorizationModelInterface|string $model            The authorization model to use
     * @param  string                             $type             The object type to find
     * @param  string                             $relation         The relationship to check
     * @param  string                             $user             The user to check relationships for
     * @param  object|null                        $context          Additional context for evaluation
     * @param  TupleKeysInterface|null            $contextualTuples Additional tuples for contextual evaluation
     * @param  Consistency|null                   $consistency      Override the default consistency level
     * @return FailureInterface|SuccessInterface  Success contains Generator<StreamedListObjectsResponseInterface>, Failure contains Throwable
     */
    public function streamedListObjects(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        string $type,
        string $relation,
        string $user,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): FailureInterface | SuccessInterface;

    /**
     * Creates or updates assertions for an authorization model.
     *
     * @param  StoreInterface|string              $store      The store containing the model
     * @param  AuthorizationModelInterface|string $model      The model to update assertions for
     * @param  AssertionsInterface                $assertions The assertions to upsert
     * @return FailureInterface|SuccessInterface  Success contains WriteAssertionsResponseInterface, Failure contains Throwable
     */
    public function writeAssertions(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        AssertionsInterface $assertions,
    ): FailureInterface | SuccessInterface;

    /**
     * Writes or deletes relationship tuples in a store.
     *
     * This method supports both transactional (all-or-nothing) and non-transactional
     * (independent operations) modes. In transactional mode, all operations must
     * succeed or the entire request fails. In non-transactional mode, operations
     * are processed independently with detailed success/failure tracking.
     *
     * @param  StoreInterface|string              $store               The store to modify
     * @param  AuthorizationModelInterface|string $model               The authorization model to use
     * @param  TupleKeysInterface|null            $writes              Tuples to write (create or update)
     * @param  TupleKeysInterface|null            $deletes             Tuples to delete
     * @param  bool                               $transactional       Whether to use transactional mode (default: true)
     * @param  int                                $maxParallelRequests Maximum concurrent requests (non-transactional only, default: 1)
     * @param  int                                $maxTuplesPerChunk   Maximum tuples per chunk (non-transactional only, default: 100)
     * @param  int                                $maxRetries          Maximum retry attempts (non-transactional only, default: 0)
     * @param  float                              $retryDelaySeconds   Retry delay in seconds (non-transactional only, default: 1.0)
     * @param  bool                               $stopOnFirstError    Stop on first error (non-transactional only, default: false)
     * @return FailureInterface|SuccessInterface  Success contains WriteTuplesResponseInterface, Failure contains Throwable
     *
     * @example Transactional write (all-or-nothing)
     * // Create relationships - all succeed or all fail together
     * $writes = new TupleKeys([
     *     new TupleKey('user:anne', 'owner', 'document:budget'),
     *     new TupleKey('user:bob', 'viewer', 'document:budget'),
     *     new TupleKey('user:charlie', 'editor', 'document:roadmap'),
     * ]);
     *
     * $result = $client->writeTuples(
     *     store: 'store-id',
     *     model: 'model-id',
     *     writes: $writes
     * );
     *
     * if ($result->success()) {
     *     echo "Successfully wrote " . count($writes) . " relationships";
     * }
     * @example Non-transactional batch processing
     * // Process large datasets with parallel execution and partial success handling
     * $writes = new TupleKeys([
     *     // ... hundreds or thousands of tuples
     * ]);
     *
     * $result = $client->writeTuples(
     *     store: 'store-id',
     *     model: 'model-id',
     *     writes: $writes,
     *     transactional: false,
     *     maxParallelRequests: 5,
     *     maxTuplesPerChunk: 50,
     *     maxRetries: 2
     * );
     *
     * $result->success(function($response) {
     *     if ($response->isCompleteSuccess()) {
     *         echo "All operations succeeded\n";
     *     } elseif ($response->isPartialSuccess()) {
     *         echo "Partial success: {$response->getSuccessfulChunks()}/{$response->getTotalChunks()} chunks\n";
     *         foreach ($response->getErrors() as $error) {
     *             echo "Error: " . $error->getMessage() . "\n";
     *         }
     *     }
     * });
     * @example Updating permissions by adding and removing tuples
     * $writes = new TupleKeys([
     *     new TupleKey('user:anne', 'editor', 'document:budget'), // Promote anne to editor
     * ]);
     *
     * $deletes = new TupleKeys([
     *     new TupleKey('user:bob', 'viewer', 'document:budget'), // Remove bob's access
     * ]);
     *
     * $client->writeTuples(
     *     store: 'store-id',
     *     model: 'model-id',
     *     writes: $writes,
     *     deletes: $deletes
     * );
     */
    public function writeTuples(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        ?TupleKeysInterface $writes = null,
        ?TupleKeysInterface $deletes = null,
        bool $transactional = true,
        int $maxParallelRequests = 1,
        int $maxTuplesPerChunk = 100,
        int $maxRetries = 0,
        float $retryDelaySeconds = 1.0,
        bool $stopOnFirstError = false,
    ): FailureInterface | SuccessInterface;
}
