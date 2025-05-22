<?php

declare(strict_types=1);

namespace OpenFGA;

use DateTimeImmutable;
use InvalidArgumentException;
use OpenFGA\Models\{AssertionInterface, AuthorizationModelInterface, ConditionInterface, StoreInterface, TupleKeyInterface, TypeDefinitionInterface, UserTypeFilterInterface};

use OpenFGA\Models\Collections\{AssertionsInterface, ConditionsInterface, TupleKeysInterface, TypeDefinitionsInterface, UserTypeFiltersInterface};
use OpenFGA\Models\Enums\{Consistency, SchemaVersion};
use OpenFGA\Responses\{CheckResponseInterface, CreateAuthorizationModelResponseInterface, CreateStoreResponseInterface, DeleteStoreResponseInterface, ExpandResponseInterface, GetAuthorizationModelResponseInterface, GetStoreResponseInterface, ListAuthorizationModelsResponseInterface, ListObjectsResponseInterface, ListStoresResponseInterface, ListTupleChangesResponseInterface, ListUsersResponseInterface, ReadAssertionsResponseInterface, ReadTuplesResponseInterface, WriteAssertionsResponseInterface, WriteTuplesResponseInterface};
use OpenFGA\Results\ResultInterface;
use Throwable;

interface ClientInterface
{
    /**
     * Checks if a user has a specific relationship with an object.
     *
     * @param StoreInterface|string                      $store            The store to check against
     * @param AuthorizationModelInterface|string         $model            The authorization model to use
     * @param TupleKeyInterface                          $tupleKey         The relationship to check
     * @param null|bool                                  $trace            Whether to include a trace in the response
     * @param null|object                                $context          Additional context for the check
     * @param null|TupleKeysInterface<TupleKeyInterface> $contextualTuples Additional tuples for contextual evaluation
     * @param null|Consistency                           $consistency      Override the default consistency level
     *
     * @return ResultInterface<CheckResponseInterface, Throwable> The result of the check request
     */
    public function check(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        TupleKeyInterface $tupleKey,
        ?bool $trace = null,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): ResultInterface;

    /**
     * Creates a new authorization model with the given type definitions and conditions.
     *
     * @param StoreInterface|string                             $store           The store to create the model in
     * @param TypeDefinitionsInterface<TypeDefinitionInterface> $typeDefinitions The type definitions for the model
     * @param ConditionsInterface<ConditionInterface>           $conditions      The conditions for the model
     * @param SchemaVersion                                     $schemaVersion   The schema version to use (default: 1.1)
     *
     * @return ResultInterface<CreateAuthorizationModelResponseInterface, Throwable> The result of the authorization model creation request
     */
    public function createAuthorizationModel(
        StoreInterface | string $store,
        TypeDefinitionsInterface $typeDefinitions,
        ConditionsInterface $conditions,
        SchemaVersion $schemaVersion = SchemaVersion::V1_1,
    ): ResultInterface;

    /**
     * Creates a new store with the given name.
     *
     * @param string $name The name for the new store
     *
     * @return ResultInterface<CreateStoreResponseInterface, Throwable> The result of the store creation request
     */
    public function createStore(
        string $name,
    ): ResultInterface;

    /**
     * Deletes a store.
     *
     * @param StoreInterface|string $store The store to delete
     *
     * @return ResultInterface<DeleteStoreResponseInterface, Throwable> The result of the store deletion request
     */
    public function deleteStore(
        StoreInterface | string $store,
    ): ResultInterface;

    /**
     * Parses a DSL string and returns an AuthorizationModel.
     *
     * @param string $dsl The DSL string to parse
     *
     * @return ResultInterface<AuthorizationModelInterface, Throwable> The result of the DSL transformation request
     */
    public function dsl(string $dsl): ResultInterface;

    /**
     * Expands a relationship tuple to show all users that have the relationship.
     *
     * @param StoreInterface|string                      $store            The store containing the tuple
     * @param TupleKeyInterface                          $tupleKey         The tuple to expand
     * @param null|AuthorizationModelInterface|string    $model            The authorization model to use
     * @param null|TupleKeysInterface<TupleKeyInterface> $contextualTuples Additional tuples for contextual evaluation
     * @param null|Consistency                           $consistency      Override the default consistency level
     *
     * @return ResultInterface<ExpandResponseInterface, Throwable> The result of the expansion request
     */
    public function expand(
        StoreInterface | string $store,
        TupleKeyInterface $tupleKey,
        AuthorizationModelInterface | string | null $model = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): ResultInterface;

    /**
     * Retrieves an authorization model by ID.
     *
     * @param StoreInterface|string              $store The store containing the model
     * @param AuthorizationModelInterface|string $model The model to retrieve
     *
     * @return ResultInterface<GetAuthorizationModelResponseInterface, Throwable> The result of the authorization model retrieval request
     */
    public function getAuthorizationModel(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
    ): ResultInterface;

    /**
     * Retrieves the last HTTP request made by the client.
     *
     * @return ?\Psr\Http\Message\RequestInterface The last request, or null if no request has been made
     */
    public function getLastRequest(): ?\Psr\Http\Message\RequestInterface;

    /**
     * Retrieves the last HTTP response received by the client.
     *
     * @return ?\Psr\Http\Message\ResponseInterface The last response, or null if no response has been received
     */
    public function getLastResponse(): ?\Psr\Http\Message\ResponseInterface;

    /**
     * Retrieves store details by ID.
     *
     * @param StoreInterface|string $store The store to retrieve
     *
     * @return ResultInterface<GetStoreResponseInterface, Throwable> The result of the store retrieval request
     */
    public function getStore(
        StoreInterface | string $store,
    ): ResultInterface;

    /**
     * Lists authorization models in a store with pagination.
     *
     * @param StoreInterface|string $store             The store to list models from
     * @param null|string           $continuationToken Token for pagination
     * @param null|positive-int     $pageSize          Maximum number of models to return
     *
     * @throws InvalidArgumentException If pageSize is not a positive integer
     *
     * @return ResultInterface<ListAuthorizationModelsResponseInterface, Throwable> The result of the authorization model listing request
     */
    public function listAuthorizationModels(
        StoreInterface | string $store,
        ?string $continuationToken = null,
        ?int $pageSize = null,
    ): ResultInterface;

    /**
     * Lists objects that have a specific relationship with a user.
     *
     * @param StoreInterface|string                      $store            The store to query
     * @param AuthorizationModelInterface|string         $model            The authorization model to use
     * @param string                                     $type             The type of objects to list
     * @param string                                     $relation         The relationship to check
     * @param string                                     $user             The user to check relationships for
     * @param null|object                                $context          Additional context for evaluation
     * @param null|TupleKeysInterface<TupleKeyInterface> $contextualTuples Additional tuples for contextual evaluation
     * @param null|Consistency                           $consistency      Override the default consistency level
     *
     * @return ResultInterface<ListObjectsResponseInterface, Throwable> The result of the object listing request
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
    ): ResultInterface;

    /**
     * Lists all stores with pagination.
     *
     * @param null|string       $continuationToken Token for pagination
     * @param null|positive-int $pageSize          Maximum number of stores to return
     *
     * @throws InvalidArgumentException If pageSize is not a positive integer
     *
     * @return ResultInterface<ListStoresResponseInterface, Throwable> The result of the store listing request
     */
    public function listStores(
        ?string $continuationToken = null,
        ?int $pageSize = null,
    ): ResultInterface;

    /**
     * Lists changes to relationship tuples in a store.
     *
     * @param StoreInterface|string  $store             The store to list changes for
     * @param null|string            $continuationToken Token for pagination
     * @param null|positive-int      $pageSize          Maximum number of changes to return
     * @param null|string            $type              Filter changes by type
     * @param null|DateTimeImmutable $startTime         Only include changes at or after this time (inclusive)
     *
     * @throws InvalidArgumentException If pageSize is not a positive integer
     *
     * @return ResultInterface<ListTupleChangesResponseInterface, Throwable> The result of the tuple change listing request
     */
    public function listTupleChanges(
        StoreInterface | string $store,
        ?string $continuationToken = null,
        ?int $pageSize = null,
        ?string $type = null,
        ?DateTimeImmutable $startTime = null,
    ): ResultInterface;

    /**
     * Lists users that have a specific relationship with an object.
     *
     * @param StoreInterface|string                             $store            The store to query
     * @param AuthorizationModelInterface|string                $model            The authorization model to use
     * @param string                                            $object           The object to check relationships for
     * @param string                                            $relation         The relationship to check
     * @param UserTypeFiltersInterface<UserTypeFilterInterface> $userFilters      Filters for user types to include
     * @param null|object                                       $context          Additional context for evaluation
     * @param null|TupleKeysInterface<TupleKeyInterface>        $contextualTuples Additional tuples for contextual evaluation
     * @param null|Consistency                                  $consistency      Override the default consistency level
     *
     * @return ResultInterface<ListUsersResponseInterface, Throwable> The result of the user listing request
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
    ): ResultInterface;

    /**
     * Retrieves assertions for an authorization model.
     *
     * @param StoreInterface|string              $store The store containing the model
     * @param AuthorizationModelInterface|string $model The model to get assertions for
     *
     * @return ResultInterface<ReadAssertionsResponseInterface, Throwable> The result of the assertions read request
     */
    public function readAssertions(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
    ): ResultInterface;

    /**
     * Reads relationship tuples from a store with optional filtering and pagination.
     *
     * @param StoreInterface|string $store             The store to read from
     * @param TupleKeyInterface     $tupleKey          Filter tuples by this key (return all if null)
     * @param null|string           $continuationToken Token for pagination
     * @param null|positive-int     $pageSize          Maximum number of tuples to return
     * @param null|Consistency      $consistency       Override the default consistency level
     *
     * @throws InvalidArgumentException If pageSize is not a positive integer
     *
     * @return ResultInterface<ReadTuplesResponseInterface, Throwable> The result of the tuple read request
     */
    public function readTuples(
        StoreInterface | string $store,
        TupleKeyInterface $tupleKey,
        ?string $continuationToken = null,
        ?int $pageSize = null,
        ?Consistency $consistency = null,
    ): ResultInterface;

    /**
     * Creates or updates assertions for an authorization model.
     *
     * @param StoreInterface|string                   $store      The store containing the model
     * @param AuthorizationModelInterface|string      $model      The model to update assertions for
     * @param AssertionsInterface<AssertionInterface> $assertions The assertions to upsert
     *
     * @return ResultInterface<WriteAssertionsResponseInterface, Throwable> The result of the assertion write request
     */
    public function writeAssertions(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        AssertionsInterface $assertions,
    ): ResultInterface;

    /**
     * Writes or deletes relationship tuples in a store.
     *
     * @param StoreInterface|string                      $store   The store to modify
     * @param AuthorizationModelInterface|string         $model   The authorization model to use
     * @param null|TupleKeysInterface<TupleKeyInterface> $writes  Tuples to write (create or update)
     * @param null|TupleKeysInterface<TupleKeyInterface> $deletes Tuples to delete
     *
     * @return ResultInterface<WriteTuplesResponseInterface, Throwable> The result of the tuple write request
     */
    public function writeTuples(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        ?TupleKeysInterface $writes = null,
        ?TupleKeysInterface $deletes = null,
    ): ResultInterface;
}
