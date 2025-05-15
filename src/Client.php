<?php

declare(strict_types=1);

namespace OpenFGA;

use OpenFGA\Authentication\{AuthenticationInterface, ClientCredentialAuthentication, NullCredentialAuthentication};
use OpenFGA\Credentials\{ClientCredentialInterface, CredentialInterface};
use OpenFGA\Models\{AssertionsInterface, AuthorizationModelInterface, ConditionsInterface, SchemaVersion, StoreInterface, TupleKeyInterface, TupleKeysInterface, TypeDefinitionsInterface, UserTypeFiltersInterface};
use OpenFGA\Network\RequestManager;
use OpenFGA\Options\{CheckOptionsInterface, CreateAuthorizationModelOptionsInterface, CreateStoreOptionsInterface, DeleteStoreOptionsInterface, ExpandOptionsInterface, GetAuthorizationModelOptionsInterface, GetStoreOptionsInterface, ListAuthorizationModelsOptionsInterface, ListObjectsOptionsInterface, ListStoresOptionsInterface, ListTupleChangesOptionsInterface, ListUsersOptionsInterface, ReadAssertionsOptionsInterface, ReadTuplesOptionsInterface, WriteAssertionsOptionsInterface, WriteTuplesOptionsInterface};
use OpenFGA\Requests\{CheckRequest, CreateAuthorizationModelRequest, CreateStoreRequest, DeleteStoreRequest, ExpandRequest, GetAuthorizationModelRequest, GetStoreRequest, ListAuthorizationModelsRequest, ListObjectsRequest, ListStoresRequest, ListTupleChangesRequest, ListUsersRequest, ReadAssertionsRequest, ReadTuplesRequest, RequestInterface, WriteAssertionsRequest, WriteTuplesRequest};
use OpenFGA\Responses\{CheckResponse, CheckResponseInterface, CreateAuthorizationModelResponse, CreateAuthorizationModelResponseInterface, CreateStoreResponse, CreateStoreResponseInterface, DeleteStoreResponse, DeleteStoreResponseInterface, ExpandResponse, ExpandResponseInterface, GetAuthorizationModelResponse, GetAuthorizationModelResponseInterface, GetStoreResponse, GetStoreResponseInterface, ListAuthorizationModelsResponse, ListAuthorizationModelsResponseInterface, ListObjectsResponse, ListObjectsResponseInterface, ListStoresResponse, ListStoresResponseInterface, ListTupleChangesResponse, ListTupleChangesResponseInterface, ListUsersResponse, ListUsersResponseInterface, ReadAssertionsResponse, ReadAssertionsResponseInterface, ReadTuplesResponse, ReadTuplesResponseInterface, WriteAssertionsResponse, WriteAssertionsResponseInterface, WriteTuplesResponse, WriteTuplesResponseInterface};
use OpenFGA\Schema\SchemaValidator;

final class Client implements ClientInterface
{
    public const string VERSION = '0.2.0';

    private ?AuthenticationInterface $authentication = null;

    private ?\Psr\Http\Message\RequestInterface $lastRequest = null;

    private ?\Psr\Http\Message\ResponseInterface $lastResponse = null;

    private ?RequestManager $requestManager = null;

    private ?SchemaValidator $validator = null;

    public function __construct(
        private string $url,
        private ?CredentialInterface $credential = null,
        private ?\Psr\Http\Client\ClientInterface $httpClient = null,
        private ?\Psr\Http\Message\ResponseFactoryInterface $httpResponseFactory = null,
        private ?\Psr\Http\Message\StreamFactoryInterface $httpStreamFactory = null,
        private ?\Psr\Http\Message\RequestFactoryInterface $httpRequestFactory = null,
    ) {
    }

    public function check(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
        TupleKeyInterface $tupleKey,
        ?bool $trace = null,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?CheckOptionsInterface $options = null,
    ): CheckResponseInterface {
        $request = new CheckRequest(
            store: self::getStoreId($store),
            authorizationModel: self::getAuthorizationModelId($authorizationModel),
            tupleKey: $tupleKey,
            trace: $trace,
            context: $context,
            contextualTuples: $contextualTuples,
            options: $options,
        );

        return CheckResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function createAuthorizationModel(
        StoreInterface | string $store,
        TypeDefinitionsInterface $typeDefinitions,
        ConditionsInterface $conditions,
        SchemaVersion $schemaVersion = SchemaVersion::V1_1,
        ?CreateAuthorizationModelOptionsInterface $options = null,
    ): CreateAuthorizationModelResponseInterface {
        $request = new CreateAuthorizationModelRequest(
            typeDefinitions: $typeDefinitions,
            conditions: $conditions,
            schemaVersion: $schemaVersion,
            store: self::getStoreId($store),
            options: $options,
        );

        return CreateAuthorizationModelResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function createStore(
        string $name,
        ?CreateStoreOptionsInterface $options = null,
    ): CreateStoreResponseInterface {
        $name = trim($name);

        $request = new CreateStoreRequest(
            name: $name,
            options: $options,
        );

        return CreateStoreResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function deleteStore(
        StoreInterface | string $store,
        ?DeleteStoreOptionsInterface $options = null,
    ): DeleteStoreResponseInterface {
        $request = new DeleteStoreRequest(
            store: self::getStoreId($store),
            options: $options,
        );

        return DeleteStoreResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function expand(
        StoreInterface | string $store,
        TupleKeyInterface $tupleKey,
        AuthorizationModelInterface | string | null $authorizationModel = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?ExpandOptionsInterface $options = null,
    ): ExpandResponseInterface {
        $request = new ExpandRequest(
            tupleKey: $tupleKey,
            contextualTuples: $contextualTuples,
            store: self::getStoreId($store),
            authorizationModel: (null !== $authorizationModel) ? self::getAuthorizationModelId($authorizationModel) : null,
            options: $options,
        );

        return ExpandResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function getAuthorizationModel(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
        ?GetAuthorizationModelOptionsInterface $options = null,
    ): GetAuthorizationModelResponseInterface {
        $request = new GetAuthorizationModelRequest(
            store: self::getStoreId($store),
            authorizationModel: self::getAuthorizationModelId($authorizationModel),
            options: $options,
        );

        return GetAuthorizationModelResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function getLastRequest(): ?\Psr\Http\Message\RequestInterface
    {
        return $this->lastRequest;
    }

    public function getLastResponse(): ?\Psr\Http\Message\ResponseInterface
    {
        return $this->lastResponse;
    }

    public function getStore(
        StoreInterface | string $store,
        ?GetStoreOptionsInterface $options = null,
    ): GetStoreResponseInterface {
        $request = new GetStoreRequest(
            store: self::getStoreId($store),
            options: $options,
        );

        return GetStoreResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function listAuthorizationModels(
        StoreInterface | string $store,
        ?ListAuthorizationModelsOptionsInterface $options = null,
    ): ListAuthorizationModelsResponseInterface {
        $request = new ListAuthorizationModelsRequest(
            store: self::getStoreId($store),
            options: $options,
        );

        return ListAuthorizationModelsResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function listObjects(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
        string $type,
        string $relation,
        string $user,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?ListObjectsOptionsInterface $options = null,
    ): ListObjectsResponseInterface {
        $request = new ListObjectsRequest(
            type: $type,
            relation: $relation,
            user: $user,
            context: $context,
            contextualTuples: $contextualTuples,
            store: self::getStoreId($store),
            authorizationModel: self::getAuthorizationModelId($authorizationModel),
            options: $options,
        );

        return ListObjectsResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function listStores(
        ?ListStoresOptionsInterface $options = null,
    ): ListStoresResponseInterface {
        $request = new ListStoresRequest(
            options: $options,
        );

        return ListStoresResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function listTupleChanges(
        StoreInterface | string $store,
        ?ListTupleChangesOptionsInterface $options = null,
    ): ListTupleChangesResponseInterface {
        $request = new ListTupleChangesRequest(
            store: self::getStoreId($store),
            options: $options,
        );

        return ListTupleChangesResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function listUsers(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
        string $object,
        string $relation,
        UserTypeFiltersInterface $userFilters,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?ListUsersOptionsInterface $options = null,
    ): ListUsersResponseInterface {
        $request = new ListUsersRequest(
            object: $object,
            relation: $relation,
            userFilters: $userFilters,
            context: $context,
            contextualTuples: $contextualTuples,
            store: self::getStoreId($store),
            authorizationModel: self::getAuthorizationModelId($authorizationModel),
            options: $options,
        );

        return ListUsersResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function readAssertions(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
        ?ReadAssertionsOptionsInterface $options = null,
    ): ReadAssertionsResponseInterface {
        $request = new ReadAssertionsRequest(
            store: self::getStoreId($store),
            authorizationModel: self::getAuthorizationModelId($authorizationModel),
            options: $options,
        );

        return ReadAssertionsResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function readTuples(
        StoreInterface | string $store,
        ?TupleKeyInterface $tupleKey = null,
        ?ReadTuplesOptionsInterface $options = null,
    ): ReadTuplesResponseInterface {
        $request = new ReadTuplesRequest(
            tupleKey: $tupleKey,
            store: self::getStoreId($store),
            options: $options,
        );

        return ReadTuplesResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function writeAssertions(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
        AssertionsInterface $assertions,
        ?WriteAssertionsOptionsInterface $options = null,
    ): WriteAssertionsResponseInterface {
        $request = new WriteAssertionsRequest(
            assertions: $assertions,
            store: self::getStoreId($store),
            authorizationModel: self::getAuthorizationModelId($authorizationModel),
            options: $options,
        );

        return WriteAssertionsResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function writeTuples(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
        ?TupleKeysInterface $writes = null,
        ?TupleKeysInterface $deletes = null,
        ?WriteTuplesOptionsInterface $options = null,
    ): WriteTuplesResponseInterface {
        $request = new WriteTuplesRequest(
            writes: $writes,
            deletes: $deletes,
            store: self::getStoreId($store),
            authorizationModel: self::getAuthorizationModelId($authorizationModel),
            options: $options,
        );

        return WriteTuplesResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    private function getAuthentication(): AuthenticationInterface
    {
        if (null === $this->authentication) {
            $credential = $this->getConfiguration()->getCredential();

            if ($credential instanceof ClientCredentialInterface) {
                $this->authentication = new ClientCredentialAuthentication($this);
            } else {
                $this->authentication = new NullCredentialAuthentication($this);
            }
        }

        return $this->authentication;
    }

    private function getConfiguration(): ConfigurationInterface
    {
        return $this->configuration;
    }

    private function getValidator(): SchemaValidator
    {
        return $this->validator ??= new SchemaValidator();
    }

    private function sendRequest(RequestInterface $request): \Psr\Http\Message\ResponseInterface
    {
        $this->requestManager ??= new RequestManager(
            url: $this->url,
            authorizationHeader: $this->getAuthentication()->getAuthorizationHeader(),
            httpClient: $this->httpClient,
            httpStreamFactory: $this->httpStreamFactory,
            httpRequestFactory: $this->httpRequestFactory,
            httpResponseFactory: $this->httpResponseFactory,
        );

        $this->lastRequest = $this->requestManager->request($request);
        $this->lastResponse = $this->requestManager->send($this->lastRequest);

        return $this->lastResponse;
    }

    private static function getAuthorizationModelId(AuthorizationModelInterface | string $authorizationModel): string
    {
        if ($authorizationModel instanceof AuthorizationModelInterface) {
            return $authorizationModel->getId();
        }

        return $authorizationModel;
    }

    private static function getStoreId(StoreInterface | string $store): string
    {
        if ($store instanceof StoreInterface) {
            return $store->getId();
        }

        return $store;
    }
}
