<?php

declare(strict_types=1);

namespace OpenFGA;

use DateTimeImmutable;
use InvalidArgumentException;
use OpenFGA\Exceptions\ClientThrowable;
use OpenFGA\Models\{AssertionInterface, AuthorizationModel, AuthorizationModelInterface, ConditionInterface, StoreInterface, TupleKeyInterface, TypeDefinitionInterface, UserTypeFilterInterface};
use OpenFGA\Models\Collections\{AssertionsInterface, ConditionsInterface, TupleKeysInterface, TypeDefinitionsInterface, UserTypeFiltersInterface};
use OpenFGA\Models\Collections\BatchCheckItemsInterface;
use OpenFGA\Models\Enums\{Consistency, SchemaVersion};
use OpenFGA\Results\{Failure, FailureInterface, Success, SuccessInterface};
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
     * @param  StoreInterface|string                      $store            The store to check against
     * @param  AuthorizationModelInterface|string         $model            The authorization model to use
     * @param  TupleKeyInterface                          $tupleKey         The relationship to check
     * @param  bool|null                                  $trace            Whether to include a trace in the response
     * @param  object|null                                $context          Additional context for the check
     * @param  TupleKeysInterface<TupleKeyInterface>|null $contextualTuples Additional tuples for contextual evaluation
     * @param  Consistency|null                           $consistency      Override the default consistency level
     * @return FailureInterface|SuccessInterface          Success contains CheckResponseInterface, Failure contains Throwable
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
     * @param  StoreInterface|string                             $store           The store to create the model in
     * @param  TypeDefinitionsInterface<TypeDefinitionInterface> $typeDefinitions The type definitions for the model
     * @param  ConditionsInterface<ConditionInterface>|null      $conditions      The conditions for the model
     * @param  SchemaVersion                                     $schemaVersion   The schema version to use (default: 1.1)
     * @return FailureInterface|SuccessInterface                 Success contains CreateAuthorizationModelResponseInterface, Failure contains Throwable
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
     * @see https://openfga.dev/docs/authorization-concepts OpenFGA authorization concepts
     */
    public function dsl(string $dsl): FailureInterface | SuccessInterface;

    /**
     * Expands a relationship tuple to show all users that have the relationship.
     *
     * @param  StoreInterface|string                      $store            The store containing the tuple
     * @param  TupleKeyInterface                          $tupleKey         The tuple to expand
     * @param  AuthorizationModelInterface|string|null    $model            The authorization model to use
     * @param  TupleKeysInterface<TupleKeyInterface>|null $contextualTuples Additional tuples for contextual evaluation
     * @param  Consistency|null                           $consistency      Override the default consistency level
     * @return FailureInterface|SuccessInterface          Success contains ExpandResponseInterface, Failure contains Throwable
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
     * @param positive-int|null     $pageSize          Maximum number of models to return
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
     * @param  StoreInterface|string                      $store            The store to query
     * @param  AuthorizationModelInterface|string         $model            The authorization model to use
     * @param  string                                     $type             The type of objects to list
     * @param  string                                     $relation         The relationship to check
     * @param  string                                     $user             The user to check relationships for
     * @param  object|null                                $context          Additional context for evaluation
     * @param  TupleKeysInterface<TupleKeyInterface>|null $contextualTuples Additional tuples for contextual evaluation
     * @param  Consistency|null                           $consistency      Override the default consistency level
     * @return FailureInterface|SuccessInterface          Success contains ListObjectsResponseInterface, Failure contains Throwable
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
     * @param  StoreInterface|string                             $store            The store to query
     * @param  AuthorizationModelInterface|string                $model            The authorization model to use
     * @param  string                                            $object           The object to check relationships for
     * @param  string                                            $relation         The relationship to check
     * @param  UserTypeFiltersInterface<UserTypeFilterInterface> $userFilters      Filters for user types to include
     * @param  object|null                                       $context          Additional context for evaluation
     * @param  TupleKeysInterface<TupleKeyInterface>|null        $contextualTuples Additional tuples for contextual evaluation
     * @param  Consistency|null                                  $consistency      Override the default consistency level
     * @return FailureInterface|SuccessInterface                 Success contains ListUsersResponseInterface, Failure contains Throwable
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
     * @param  StoreInterface|string                      $store            The store to query
     * @param  AuthorizationModelInterface|string         $model            The authorization model to use
     * @param  string                                     $type             The object type to find
     * @param  string                                     $relation         The relationship to check
     * @param  string                                     $user             The user to check relationships for
     * @param  object|null                                $context          Additional context for evaluation
     * @param  TupleKeysInterface<TupleKeyInterface>|null $contextualTuples Additional tuples for contextual evaluation
     * @param  Consistency|null                           $consistency      Override the default consistency level
     * @return FailureInterface|SuccessInterface          Success contains Generator<StreamedListObjectsResponseInterface>, Failure contains Throwable
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
     * @param  StoreInterface|string                   $store      The store containing the model
     * @param  AuthorizationModelInterface|string      $model      The model to update assertions for
     * @param  AssertionsInterface<AssertionInterface> $assertions The assertions to upsert
     * @return FailureInterface|SuccessInterface       Success contains WriteAssertionsResponseInterface, Failure contains Throwable
     */
    public function writeAssertions(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        AssertionsInterface $assertions,
    ): FailureInterface | SuccessInterface;

    /**
     * Writes or deletes relationship tuples in a store.
     *
     * @param  StoreInterface|string                      $store   The store to modify
     * @param  AuthorizationModelInterface|string         $model   The authorization model to use
     * @param  TupleKeysInterface<TupleKeyInterface>|null $writes  Tuples to write (create or update)
     * @param  TupleKeysInterface<TupleKeyInterface>|null $deletes Tuples to delete
     * @return FailureInterface|SuccessInterface          Success contains WriteTuplesResponseInterface, Failure contains Throwable
     */
    public function writeTuples(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        ?TupleKeysInterface $writes = null,
        ?TupleKeysInterface $deletes = null,
    ): FailureInterface | SuccessInterface;
}
