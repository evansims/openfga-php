<?php

declare(strict_types=1);

namespace OpenFGA;

use DateTimeImmutable;
use InvalidArgumentException;

use OpenFGA\Models\{AssertionsInterface, AuthorizationModelInterface, ConditionsInterface, Consistency, SchemaVersion, StoreInterface, TupleKeyInterface, TupleKeysInterface, TypeDefinitionsInterface, UserTypeFiltersInterface};
use OpenFGA\Responses\{CheckResponseInterface, CreateAuthorizationModelResponseInterface, CreateStoreResponseInterface, DeleteStoreResponseInterface, ExpandResponseInterface, GetAuthorizationModelResponseInterface, GetStoreResponseInterface, ListAuthorizationModelsResponseInterface, ListObjectsResponseInterface, ListStoresResponseInterface, ListTupleChangesResponseInterface, ListUsersResponseInterface, ReadAssertionsResponseInterface, ReadTuplesResponseInterface, WriteAssertionsResponseInterface, WriteTuplesResponseInterface};

interface ClientInterface
{
    /**
     * Checks if a user has a specific relationship with an object.
     *
     * @param StoreInterface|string              $store              The store to check against
     * @param AuthorizationModelInterface|string $authorizationModel The authorization model to use
     * @param TupleKeyInterface                  $tupleKey           The relationship to check
     * @param null|bool                          $trace              Whether to include a trace in the response
     * @param null|object                        $context            Additional context for the check
     * @param null|TupleKeysInterface            $contextualTuples   Additional tuples for contextual evaluation
     * @param null|Consistency                   $consistency        Override the default consistency level
     *
     * @return CheckResponseInterface The result of the check
     */
    public function check(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
        TupleKeyInterface $tupleKey,
        ?bool $trace = null,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): CheckResponseInterface;

    /**
     * Creates a new authorization model with the given type definitions and conditions.
     *
     * @param StoreInterface|string    $store           The store to create the model in
     * @param TypeDefinitionsInterface $typeDefinitions The type definitions for the model
     * @param ConditionsInterface      $conditions      The conditions for the model
     * @param SchemaVersion            $schemaVersion   The schema version to use (default: 1.1)
     *
     * @return CreateAuthorizationModelResponseInterface The created authorization model
     */
    public function createAuthorizationModel(
        StoreInterface | string $store,
        TypeDefinitionsInterface $typeDefinitions,
        ConditionsInterface $conditions,
        SchemaVersion $schemaVersion = SchemaVersion::V1_1,
    ): CreateAuthorizationModelResponseInterface;

    /**
     * Creates a new store with the given name.
     *
     * @param string $name The name for the new store
     *
     * @return CreateStoreResponseInterface The created store details
     */
    public function createStore(
        string $name,
    ): CreateStoreResponseInterface;

    /**
     * Deletes a store.
     *
     * @param StoreInterface|string $store The store to delete
     *
     * @return DeleteStoreResponseInterface The deletion result
     */
    public function deleteStore(
        StoreInterface | string $store,
    ): DeleteStoreResponseInterface;

    /**
     * Expands a relationship tuple to show all users that have the relationship.
     *
     * @param StoreInterface|string                   $store              The store containing the tuple
     * @param TupleKeyInterface                       $tupleKey           The tuple to expand
     * @param null|AuthorizationModelInterface|string $authorizationModel The authorization model to use
     * @param null|TupleKeysInterface                 $contextualTuples   Additional tuples for contextual evaluation
     * @param null|Consistency                        $consistency        Override the default consistency level
     *
     * @return ExpandResponseInterface The expanded relationship information
     */
    public function expand(
        StoreInterface | string $store,
        TupleKeyInterface $tupleKey,
        AuthorizationModelInterface | string | null $authorizationModel = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): ExpandResponseInterface;

    /**
     * Retrieves an authorization model by ID.
     *
     * @param StoreInterface|string              $store              The store containing the model
     * @param AuthorizationModelInterface|string $authorizationModel The model to retrieve
     *
     * @return GetAuthorizationModelResponseInterface The authorization model
     */
    public function getAuthorizationModel(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
    ): GetAuthorizationModelResponseInterface;

    /**
     * Retrieves store details by ID.
     *
     * @param StoreInterface|string $store The store to retrieve
     *
     * @return GetStoreResponseInterface The store details
     */
    public function getStore(
        StoreInterface | string $store,
    ): GetStoreResponseInterface;

    /**
     * Lists authorization models in a store with pagination.
     *
     * @param StoreInterface|string $store             The store to list models from
     * @param null|string           $continuationToken Token for pagination
     * @param null|positive-int     $pageSize          Maximum number of models to return
     *
     * @throws InvalidArgumentException If pageSize is not a positive integer
     *
     * @return ListAuthorizationModelsResponseInterface The list of authorization models
     */
    public function listAuthorizationModels(
        StoreInterface | string $store,
        ?string $continuationToken = null,
        ?int $pageSize = null,
    ): ListAuthorizationModelsResponseInterface;

    /**
     * Lists objects that have a specific relationship with a user.
     *
     * @param StoreInterface|string              $store              The store to query
     * @param AuthorizationModelInterface|string $authorizationModel The authorization model to use
     * @param string                             $type               The type of objects to list
     * @param string                             $relation           The relationship to check
     * @param string                             $user               The user to check relationships for
     * @param null|object                        $context            Additional context for evaluation
     * @param null|TupleKeysInterface            $contextualTuples   Additional tuples for contextual evaluation
     * @param null|Consistency                   $consistency        Override the default consistency level
     *
     * @return ListObjectsResponseInterface The list of related objects
     */
    public function listObjects(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
        string $type,
        string $relation,
        string $user,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): ListObjectsResponseInterface;

    /**
     * Lists all stores with pagination.
     *
     * @param null|string       $continuationToken Token for pagination
     * @param null|positive-int $pageSize          Maximum number of stores to return
     *
     * @throws InvalidArgumentException If pageSize is not a positive integer
     *
     * @return ListStoresResponseInterface The list of stores
     */
    public function listStores(
        ?string $continuationToken = null,
        ?int $pageSize = null,
    ): ListStoresResponseInterface;

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
     * @return ListTupleChangesResponseInterface The list of tuple changes
     */
    public function listTupleChanges(
        StoreInterface | string $store,
        ?string $continuationToken = null,
        ?int $pageSize = null,
        ?string $type = null,
        ?DateTimeImmutable $startTime = null,
    ): ListTupleChangesResponseInterface;

    /**
     * Lists users that have a specific relationship with an object.
     *
     * @param StoreInterface|string              $store              The store to query
     * @param AuthorizationModelInterface|string $authorizationModel The authorization model to use
     * @param string                             $object             The object to check relationships for
     * @param string                             $relation           The relationship to check
     * @param UserTypeFiltersInterface           $userFilters        Filters for user types to include
     * @param null|object                        $context            Additional context for evaluation
     * @param null|TupleKeysInterface            $contextualTuples   Additional tuples for contextual evaluation
     * @param null|Consistency                   $consistency        Override the default consistency level
     *
     * @return ListUsersResponseInterface The list of related users
     */
    public function listUsers(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
        string $object,
        string $relation,
        UserTypeFiltersInterface $userFilters,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): ListUsersResponseInterface;

    /**
     * Retrieves assertions for an authorization model.
     *
     * @param StoreInterface|string              $store              The store containing the model
     * @param AuthorizationModelInterface|string $authorizationModel The model to get assertions for
     *
     * @return ReadAssertionsResponseInterface The model's assertions
     */
    public function readAssertions(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
    ): ReadAssertionsResponseInterface;

    /**
     * Reads relationship tuples from a store with optional filtering and pagination.
     *
     * @param StoreInterface|string  $store             The store to read from
     * @param null|TupleKeyInterface $tupleKey          Filter tuples by this key (return all if null)
     * @param null|string            $continuationToken Token for pagination
     * @param null|positive-int      $pageSize          Maximum number of tuples to return
     * @param null|Consistency       $consistency       Override the default consistency level
     *
     * @throws InvalidArgumentException If pageSize is not a positive integer
     *
     * @return ReadTuplesResponseInterface The matching relationship tuples
     */
    public function readTuples(
        StoreInterface | string $store,
        ?TupleKeyInterface $tupleKey = null,
        ?string $continuationToken = null,
        ?int $pageSize = null,
        ?Consistency $consistency = null,
    ): ReadTuplesResponseInterface;

    /**
     * Creates or updates assertions for an authorization model.
     *
     * @param StoreInterface|string              $store              The store containing the model
     * @param AuthorizationModelInterface|string $authorizationModel The model to update assertions for
     * @param AssertionsInterface                $assertions         The assertions to upsert
     *
     * @return WriteAssertionsResponseInterface The result of the operation
     */
    public function writeAssertions(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
        AssertionsInterface $assertions,
    ): WriteAssertionsResponseInterface;

    /**
     * Writes or deletes relationship tuples in a store.
     *
     * @param StoreInterface|string              $store              The store to modify
     * @param AuthorizationModelInterface|string $authorizationModel The authorization model to use
     * @param null|TupleKeysInterface            $writes             Tuples to write (create or update)
     * @param null|TupleKeysInterface            $deletes            Tuples to delete
     *
     * @return WriteTuplesResponseInterface The result of the operation
     */
    public function writeTuples(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
        ?TupleKeysInterface $writes = null,
        ?TupleKeysInterface $deletes = null,
    ): WriteTuplesResponseInterface;
}
