<?php

declare(strict_types=1);

namespace OpenFGA;

use OpenFGA\Models\{AssertionsInterface,AuthorizationModelInterface, ConditionsInterface, SchemaVersion, StoreInterface, TupleKeyInterface, TupleKeysInterface, TypeDefinitionsInterface, UserTypeFiltersInterface};
use OpenFGA\Options\{CheckOptionsInterface, CreateAuthorizationModelOptionsInterface, CreateStoreOptionsInterface, DeleteStoreOptionsInterface, ExpandOptionsInterface, GetAuthorizationModelOptionsInterface, GetStoreOptionsInterface, ListAuthorizationModelsOptionsInterface, ListObjectsOptionsInterface, ListStoresOptionsInterface, ListTupleChangesOptionsInterface, ListUsersOptionsInterface, ReadAssertionsOptionsInterface, ReadTuplesOptionsInterface, WriteAssertionsOptionsInterface, WriteTuplesOptionsInterface};
use OpenFGA\Responses\{CheckResponseInterface, CreateAuthorizationModelResponseInterface, CreateStoreResponseInterface, DeleteStoreResponseInterface, ExpandResponseInterface, GetAuthorizationModelResponseInterface, GetStoreResponseInterface, ListAuthorizationModelsResponseInterface, ListObjectsResponseInterface, ListStoresResponseInterface, ListTupleChangesResponseInterface, ListUsersResponseInterface, ReadAssertionsResponseInterface, ReadTuplesResponseInterface, WriteAssertionsResponseInterface, WriteTuplesResponseInterface};

interface ClientInterface
{
    public function check(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
        TupleKeyInterface $tupleKey,
        ?bool $trace = null,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?CheckOptionsInterface $options = null,
    ): CheckResponseInterface;

    /**
     * Creates a new authorization model.
     *
     * This function sends a POST request to the /stores/{store_id}/authorization-models endpoint
     * to create a new authorization model. It returns a CreateAuthorizationModelResponse object.
     *
     * @param StoreInterface|string                         $store           The store.
     * @param TypeDefinitionsInterface                      $typeDefinitions The type definitions for the authorization model.
     * @param ConditionsInterface                           $conditions      The conditions for the authorization model.
     * @param SchemaVersion                                 $schemaVersion   The schema version of the authorization model. Defaults to "1.1".
     * @param null|CreateAuthorizationModelOptionsInterface $options         Optional request options.
     *
     * @return CreateAuthorizationModelResponseInterface The response indicating the write outcome.
     */
    public function createAuthorizationModel(
        StoreInterface | string $store,
        TypeDefinitionsInterface $typeDefinitions,
        ConditionsInterface $conditions,
        SchemaVersion $schemaVersion = SchemaVersion::V1_1,
        ?CreateAuthorizationModelOptionsInterface $options = null,
    ): CreateAuthorizationModelResponseInterface;

    /**
     * Creates a new store with the given name.
     *
     * This function sends a POST request to the /stores endpoint to create
     * a new store. It returns a CreateStoreResponse object containing the
     * details of the created store.
     *
     * @param string                       $name    The name of the store to be created.
     * @param ?CreateStoreOptionsInterface $options Optional request options such as page size and continuation token.
     *
     * @return CreateStoreResponseInterface The response containing the details of the created store.
     */
    public function createStore(
        string $name,
        ?CreateStoreOptionsInterface $options = null,
    ): CreateStoreResponseInterface;

    /**
     * Deletes a store with the specified ID.
     *
     * This function sends a DELETE request to the /stores/{store_id} endpoint
     * to delete a store. It returns a DeleteStoreResponse object.
     *
     * @param StoreInterface|string        $store   The store to be deleted.
     * @param ?DeleteStoreOptionsInterface $options Optional request options such as page size and continuation token.
     *
     * @return DeleteStoreResponseInterface The response indicating the deletion outcome.
     */
    public function deleteStore(
        StoreInterface | string $store,
        ?DeleteStoreOptionsInterface $options = null,
    ): DeleteStoreResponseInterface;

    public function expand(
        StoreInterface | string $store,
        TupleKeyInterface $tupleKey,
        AuthorizationModelInterface | string | null $authorizationModel = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?ExpandOptionsInterface $options = null,
    ): ExpandResponseInterface;

    /**
     * Retrieves an authorization model.
     *
     * This function sends a GET request to the /stores/{store_id}/authorization-models/{authorization_model_id} endpoint
     * to retrieve an authorization model. It returns a GetModelResponse object.
     *
     * @param StoreInterface|string                      $store              The store.
     * @param AuthorizationModelInterface|string         $authorizationModel The authorization model.
     * @param null|GetAuthorizationModelOptionsInterface $options            Optional request options.
     *
     * @return GetAuthorizationModelResponseInterface The response containing the authorization model.
     */
    public function getAuthorizationModel(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
        ?GetAuthorizationModelOptionsInterface $options = null,
    ): GetAuthorizationModelResponseInterface;

    /**
     * Retrieves a store by its ID.
     *
     * Sends a GET request to the /stores/{store_id} endpoint and returns a GetStoreResponse
     * object that contains the store details.
     *
     * @param StoreInterface|string     $store   The store to be retrieved.
     * @param ?GetStoreOptionsInterface $options Optional request options such as page size and continuation token.
     *
     * @return GetStoreResponseInterface The response containing the store details.
     */
    public function getStore(
        StoreInterface | string $store,
        ?GetStoreOptionsInterface $options = null,
    ): GetStoreResponseInterface;

    /**
     * Lists authorization models.
     *
     * This function sends a GET request to the /stores/{store_id}/authorization-models endpoint
     * to retrieve authorization models. It returns a ListAuthorizationModelsResponse object.
     *
     * @param StoreInterface|string                        $store   The store.
     * @param null|ListAuthorizationModelsOptionsInterface $options Optional request options.
     *
     * @return ListAuthorizationModelsResponseInterface The response containing the authorization models.
     */
    public function listAuthorizationModels(
        StoreInterface | string $store,
        ?ListAuthorizationModelsOptionsInterface $options = null,
    ): ListAuthorizationModelsResponseInterface;

    public function listObjects(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
        string $type,
        string $relation,
        string $user,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?ListObjectsOptionsInterface $options = null,
    ): ListObjectsResponseInterface;

    /**
     * Retrieves a list of all stores.
     *
     * Sends a GET request to the /stores endpoint and returns a ListStoresResponse
     * object that contains the list of stores and a continuation token for pagination.
     *
     * @param ?ListStoresOptionsInterface $options Optional request options such as page size and continuation token.
     *
     * @return ListStoresResponseInterface The response containing the list of stores and pagination details.
     */
    public function listStores(
        ?ListStoresOptionsInterface $options = null,
    ): ListStoresResponseInterface;

    /**
     * Lists changes to relationship tuples for a given store.
     *
     * This function sends a GET request to the /stores/{storeId}/changes endpoint to list
     * changes to relationship tuples for a given store. It returns a ListChangesResponse object
     * containing the list of changes.
     *
     * @param StoreInterface|string                 $store   The store ID to list changes for.
     * @param null|ListTupleChangesOptionsInterface $options The options for the list changes request. If null, the default options are used.
     *
     * @return ListTupleChangesResponseInterface The response to the list changes request.
     */
    public function listTupleChanges(
        StoreInterface | string $store,
        ?ListTupleChangesOptionsInterface $options = null,
    ): ListTupleChangesResponseInterface;

    public function listUsers(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
        string $object,
        string $relation,
        UserTypeFiltersInterface $userFilters,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?ListUsersOptionsInterface $options = null,
    ): ListUsersResponseInterface;

    /**
     * Retrieves assertions for the specified authorization model.
     *
     * This function sends a GET request to the /stores/{store_id}/assertions/{authorization_model_id} endpoint
     * to retrieve assertions for a given authorization model ID. It returns a ReadAssertionsResponse object.
     *
     * @param StoreInterface|string               $store              The store.
     * @param AuthorizationModelInterface|string  $authorizationModel The authorization model ID.
     * @param null|ReadAssertionsOptionsInterface $options            Optional request options such as page size and continuation token.
     *
     * @return ReadAssertionsResponseInterface The response containing the assertions.
     */
    public function readAssertions(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
        ?ReadAssertionsOptionsInterface $options = null,
    ): ReadAssertionsResponseInterface;

    public function readTuples(
        StoreInterface | string $store,
        ?TupleKeyInterface $tupleKey = null,
        ?ReadTuplesOptionsInterface $options = null,
    ): ReadTuplesResponseInterface;

    /**
     * Upserts assertions for the specified authorization model.
     *
     * This function sends a PUT request to the /stores/{store_id}/assertions/{authorization_model_id} endpoint
     * to upsert assertions for a given authorization model ID. It returns a WriteAssertionsResponse object.
     *
     * @param StoreInterface|string                $store              The store.
     * @param AuthorizationModelInterface|string   $authorizationModel The authorization model.
     * @param AssertionsInterface                  $assertions         The assertions to write.
     * @param null|WriteAssertionsOptionsInterface $options            Optional request options.
     *
     * @return WriteAssertionsResponseInterface The response indicating the write outcome.
     */
    public function writeAssertions(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
        AssertionsInterface $assertions,
        ?WriteAssertionsOptionsInterface $options = null,
    ): WriteAssertionsResponseInterface;

    public function writeTuples(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
        ?TupleKeysInterface $writes = null,
        ?TupleKeysInterface $deletes = null,
        ?WriteTuplesOptionsInterface $options = null,
    ): WriteTuplesResponseInterface;
}
