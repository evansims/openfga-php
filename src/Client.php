<?php

declare(strict_types=1);

namespace OpenFGA;

use DateTimeImmutable;
use OpenFGA\Authentication\{AccessToken, AccessTokenInterface, ClientCredentialAuthentication};
use OpenFGA\Models\{AuthorizationModelInterface, StoreInterface, TupleKeyInterface};
use OpenFGA\Models\Collections\{AssertionsInterface, ConditionsInterface, TupleKeysInterface, TypeDefinitionsInterface, UserTypeFiltersInterface};
use OpenFGA\Models\Enums\{Consistency, SchemaVersion};
use OpenFGA\Network\RequestManager;
use OpenFGA\Requests\{CheckRequest, CreateAuthorizationModelRequest, CreateStoreRequest, DeleteStoreRequest, ExpandRequest, GetAuthorizationModelRequest, GetStoreRequest, ListAuthorizationModelsRequest, ListObjectsRequest, ListStoresRequest, ListTupleChangesRequest, ListUsersRequest, ReadAssertionsRequest, ReadTuplesRequest, RequestInterface, WriteAssertionsRequest, WriteTuplesRequest};
use OpenFGA\Responses\{CheckResponse, CheckResponseInterface, CreateAuthorizationModelResponse, CreateAuthorizationModelResponseInterface, CreateStoreResponse, CreateStoreResponseInterface, DeleteStoreResponse, DeleteStoreResponseInterface, ExpandResponse, ExpandResponseInterface, GetAuthorizationModelResponse, GetAuthorizationModelResponseInterface, GetStoreResponse, GetStoreResponseInterface, ListAuthorizationModelsResponse, ListAuthorizationModelsResponseInterface, ListObjectsResponse, ListObjectsResponseInterface, ListStoresResponse, ListStoresResponseInterface, ListTupleChangesResponse, ListTupleChangesResponseInterface, ListUsersResponse, ListUsersResponseInterface, ReadAssertionsResponse, ReadAssertionsResponseInterface, ReadTuplesResponse, ReadTuplesResponseInterface, WriteAssertionsResponse, WriteAssertionsResponseInterface, WriteTuplesResponse, WriteTuplesResponseInterface};
use OpenFGA\Schema\SchemaValidator;

use Override;

use function is_string;

enum Authentication
{
    case CLIENT_CREDENTIALS;

    case NONE;

    case TOKEN;
}

final class Client implements ClientInterface
{
    /**
     * Maximum page size for API responses.
     */
    private const MAX_PAGE_SIZE = 1000;

    /**
     * The version of the OpenFGA PHP SDK.
     */
    public const VERSION = '0.2.0';

    /**
     * The last HTTP request made by the client.
     */
    private ?\Psr\Http\Message\RequestInterface $lastRequest = null;

    /**
     * The last HTTP response received by the client.
     */
    private ?\Psr\Http\Message\ResponseInterface $lastResponse = null;

    /**
     * The request manager used to send network requests.
     */
    private ?RequestManager $requestManager = null;

    /**
     * The JSON schema validator used to validate responses.
     */
    private ?SchemaValidator $validator = null;

    /**
     * @param string                                          $url                 The OpenFGA API URL to connect to
     * @param Authentication                                  $authentication      The authentication approach to use
     * @param null|string                                     $clientId            Optional client ID to use for OIDC authentication (Authentication::CLIENT_CREDENTIALS)
     * @param null|string                                     $clientSecret        Optional client secret to use for OIDC authentication (Authentication::CLIENT_CREDENTIALS)
     * @param null|string                                     $issuer              Optional issuer to use for OIDC authentication (Authentication::CLIENT_CREDENTIALS)
     * @param null|string                                     $audience            Optional audience to use for OIDC authentication (Authentication::CLIENT_CREDENTIALS)
     * @param null|AccessTokenInterface|string                $token               Optional token to use for pre-shared key authentication (Authentication::TOKEN)
     * @param null|positive-int                               $maxRetries          Number of times to retry a request before giving up; defaults to 3, disabled if null
     * @param null|\Psr\Http\Client\ClientInterface           $httpClient          Optional PSR-18 HTTP client to use for requests; will use autodiscovery and use the first available if not specified
     * @param null|\Psr\Http\Message\ResponseFactoryInterface $httpResponseFactory Optional PSR-17 HTTP response factory to use for requests; will use autodiscovery and use the first available if not specified
     * @param null|\Psr\Http\Message\StreamFactoryInterface   $httpStreamFactory   Optional PSR-17 HTTP stream factory to use for requests; will use autodiscovery and use the first available if not specified
     * @param null|\Psr\Http\Message\RequestFactoryInterface  $httpRequestFactory  Optional PSR-17 HTTP request factory to use for requests; will use autodiscovery and use the first available if not specified
     */
    public function __construct(
        private readonly string $url,
        private readonly Authentication $authentication = Authentication::NONE,
        private readonly ?string $clientId = null,
        private readonly ?string $clientSecret = null,
        private readonly ?string $issuer = null,
        private readonly ?string $audience = null,
        private AccessTokenInterface | string | null $token = null,
        private readonly ?int $maxRetries = 3,
        private readonly ?\Psr\Http\Client\ClientInterface $httpClient = null,
        private readonly ?\Psr\Http\Message\ResponseFactoryInterface $httpResponseFactory = null,
        private readonly ?\Psr\Http\Message\StreamFactoryInterface $httpStreamFactory = null,
        private readonly ?\Psr\Http\Message\RequestFactoryInterface $httpRequestFactory = null,
    ) {
    }

    #[Override]
    public function check(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        TupleKeyInterface $tupleKey,
        ?bool $trace = null,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): CheckResponseInterface {
        $request = new CheckRequest(
            store: self::getStoreId($store),
            model: self::getModelId($model),
            tupleKey: $tupleKey,
            trace: $trace,
            context: $context,
            contextualTuples: $contextualTuples,
            consistency: $consistency,
        );

        return CheckResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    #[Override]
    public function createAuthorizationModel(
        StoreInterface | string $store,
        TypeDefinitionsInterface $typeDefinitions,
        ConditionsInterface $conditions,
        SchemaVersion $schemaVersion = SchemaVersion::V1_1,
    ): CreateAuthorizationModelResponseInterface {
        $request = new CreateAuthorizationModelRequest(
            typeDefinitions: $typeDefinitions,
            conditions: $conditions,
            schemaVersion: $schemaVersion,
            store: self::getStoreId($store),
        );

        return CreateAuthorizationModelResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    #[Override]
    public function createStore(
        string $name,
    ): CreateStoreResponseInterface {
        $name = trim($name);

        $request = new CreateStoreRequest(
            name: $name,
        );

        return CreateStoreResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    #[Override]
    public function deleteStore(
        StoreInterface | string $store,
    ): DeleteStoreResponseInterface {
        $request = new DeleteStoreRequest(
            store: self::getStoreId($store),
        );

        return DeleteStoreResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    #[Override]
    public function expand(
        StoreInterface | string $store,
        TupleKeyInterface $tupleKey,
        AuthorizationModelInterface | string | null $model = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): ExpandResponseInterface {
        $request = new ExpandRequest(
            tupleKey: $tupleKey,
            contextualTuples: $contextualTuples,
            store: self::getStoreId($store),
            model: (null !== $model) ? self::getModelId($model) : null,
            consistency: $consistency,
        );

        return ExpandResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    #[Override]
    public function getAuthorizationModel(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
    ): GetAuthorizationModelResponseInterface {
        $request = new GetAuthorizationModelRequest(
            store: self::getStoreId($store),
            model: self::getModelId($model),
        );

        return GetAuthorizationModelResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    #[Override]
    public function getLastRequest(): ?\Psr\Http\Message\RequestInterface
    {
        return $this->lastRequest;
    }

    #[Override]
    public function getLastResponse(): ?\Psr\Http\Message\ResponseInterface
    {
        return $this->lastResponse;
    }

    #[Override]
    public function getStore(
        StoreInterface | string $store,
    ): GetStoreResponseInterface {
        $request = new GetStoreRequest(
            store: self::getStoreId($store),
        );

        return GetStoreResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    #[Override]
    public function listAuthorizationModels(
        StoreInterface | string $store,
        ?string $continuationToken = null,
        ?int $pageSize = null,
    ): ListAuthorizationModelsResponseInterface {
        $pageSize = max(1, min($pageSize, self::MAX_PAGE_SIZE));

        $request = new ListAuthorizationModelsRequest(
            store: self::getStoreId($store),
            continuationToken: $continuationToken,
            pageSize: $pageSize,
        );

        return ListAuthorizationModelsResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    #[Override]
    public function listObjects(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        string $type,
        string $relation,
        string $user,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): ListObjectsResponseInterface {
        $request = new ListObjectsRequest(
            type: $type,
            relation: $relation,
            user: $user,
            context: $context,
            contextualTuples: $contextualTuples,
            store: self::getStoreId($store),
            model: self::getModelId($model),
            consistency: $consistency,
        );

        return ListObjectsResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    #[Override]
    public function listStores(
        ?string $continuationToken = null,
        ?int $pageSize = null,
    ): ListStoresResponseInterface {
        $pageSize = max(1, min($pageSize, self::MAX_PAGE_SIZE));

        $request = new ListStoresRequest(
            continuationToken: $continuationToken,
            pageSize: $pageSize,
        );

        return ListStoresResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    #[Override]
    public function listTupleChanges(
        StoreInterface | string $store,
        ?string $continuationToken = null,
        ?int $pageSize = null,
        ?string $type = null,
        ?DateTimeImmutable $startTime = null,
    ): ListTupleChangesResponseInterface {
        $pageSize = max(1, min($pageSize, self::MAX_PAGE_SIZE));

        $request = new ListTupleChangesRequest(
            store: self::getStoreId($store),
            continuationToken: $continuationToken,
            pageSize: $pageSize,
            type: $type,
            startTime: $startTime,
        );

        return ListTupleChangesResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    #[Override]
    public function listUsers(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        string $object,
        string $relation,
        UserTypeFiltersInterface $userFilters,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): ListUsersResponseInterface {
        $request = new ListUsersRequest(
            object: $object,
            relation: $relation,
            userFilters: $userFilters,
            context: $context,
            contextualTuples: $contextualTuples,
            store: self::getStoreId($store),
            model: self::getModelId($model),
            consistency: $consistency,
        );

        return ListUsersResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    #[Override]
    public function readAssertions(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
    ): ReadAssertionsResponseInterface {
        $request = new ReadAssertionsRequest(
            store: self::getStoreId($store),
            model: self::getModelId($model),
        );

        return ReadAssertionsResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    #[Override]
    public function readTuples(
        StoreInterface | string $store,
        TupleKeyInterface $tupleKey,
        ?string $continuationToken = null,
        ?int $pageSize = null,
        ?Consistency $consistency = null,
    ): ReadTuplesResponseInterface {
        $pageSize = max(1, min($pageSize, self::MAX_PAGE_SIZE));

        $request = new ReadTuplesRequest(
            tupleKey: $tupleKey,
            store: self::getStoreId($store),
            continuationToken: $continuationToken,
            pageSize: $pageSize,
            consistency: $consistency,
        );

        return ReadTuplesResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    #[Override]
    public function writeAssertions(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        AssertionsInterface $assertions,
    ): WriteAssertionsResponseInterface {
        $request = new WriteAssertionsRequest(
            assertions: $assertions,
            store: self::getStoreId($store),
            model: self::getModelId($model),
        );

        return WriteAssertionsResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    #[Override]
    public function writeTuples(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        ?TupleKeysInterface $writes = null,
        ?TupleKeysInterface $deletes = null,
    ): WriteTuplesResponseInterface {
        $request = new WriteTuplesRequest(
            writes: $writes,
            deletes: $deletes,
            store: self::getStoreId($store),
            model: self::getModelId($model),
        );

        return WriteTuplesResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    /**
     * Return the authentication header string.
     *
     * If the authentication mode is NONE, or if the pre-shared key / client credentials
     * authentication state is not present or has expired, return null.
     *
     * If the pre-shared key authentication state is present, return the token string.
     *
     * If the client credentials / OIDC authentication state is present, return the token
     * string if it is not expired. Otherwise, attempt to authenticate using the client
     * credentials and return the resulting token string.
     *
     * If client credentials / OIDC authentication is configured, attempt to authenticate
     * using the client credentials and return the resulting token string if successful.
     *
     * Return null if no authentication is configured or if the authentication attempt
     * failed.
     */
    private function getAuthenticationHeader(): ?string
    {
        // No authentication
        if (Authentication::NONE === $this->authentication) {
            return null;
        }

        // Pre-shared key authentication state present
        if (is_string($this->token)) {
            if (Authentication::TOKEN === $this->authentication) {
                return $this->token;
            }

            $this->token = null;
        }

        // Client Credentials / OIDC authentication state present
        if ($this->token instanceof AccessTokenInterface) {
            if (Authentication::CLIENT_CREDENTIALS === $this->authentication && ! $this->token->isExpired()) {
                return (string) $this->token;
            }

            $this->token = null;
        }

        // Client Credentials / OIDC authentication configured
        if (Authentication::CLIENT_CREDENTIALS === $this->authentication) {
            $clientId = is_string($this->clientId) && '' !== trim($this->clientId) ? trim($this->clientId) : null;
            $clientSecret = is_string($this->clientSecret) && '' !== trim($this->clientSecret) ? trim($this->clientSecret) : null;
            $issuer = is_string($this->issuer) && '' !== trim($this->issuer) ? trim($this->issuer) : null;
            $audience = is_string($this->audience) && '' !== trim($this->audience) ? trim($this->audience) : null;

            if (null !== $clientId && null !== $clientSecret && null !== $issuer && null !== $audience) {
                $auth = new ClientCredentialAuthentication($clientId, $clientSecret, $audience, $issuer);
                $this->token = AccessToken::fromResponse($this->sendRequest($auth));

                return (string) $this->token;
            }
        }

        return null;
    }

    /**
     * Gets the SchemaValidator singleton used to validate response data.
     */
    private function getValidator(): SchemaValidator
    {
        return $this->validator ??= new SchemaValidator();
    }

    /**
     * Sends a request to the OpenFGA API using the configured HTTP client and authentication.
     *
     * @param RequestInterface $request The request to send.
     *
     * @return \Psr\Http\Message\ResponseInterface The response from the API.
     */
    private function sendRequest(RequestInterface $request): \Psr\Http\Message\ResponseInterface
    {
        $maxRetries = max(1, min($this->maxRetries, 10));

        $this->requestManager ??= new RequestManager(
            url: $this->url,
            maxRetries: $maxRetries,
            authorizationHeader: $this->getAuthenticationHeader(),
            httpClient: $this->httpClient,
            httpStreamFactory: $this->httpStreamFactory,
            httpRequestFactory: $this->httpRequestFactory,
            httpResponseFactory: $this->httpResponseFactory,
        );

        $this->lastRequest = $this->requestManager->request($request);
        $this->lastResponse = $this->requestManager->send($this->lastRequest);

        return $this->lastResponse;
    }

    /**
     * Get the authorization model ID from a given authorization model.
     *
     * If an instance of AuthorizationModelInterface is provided, the ID will be
     * retrieved from the object using the getId() method. Otherwise, the value
     * will be used as the authorization model ID.
     *
     * @param AuthorizationModelInterface|string $model The authorization model to get the ID from.
     *
     * @return string The authorization model ID.
     */
    private static function getModelId(AuthorizationModelInterface | string $model): string
    {
        if ($model instanceof AuthorizationModelInterface) {
            return $model->getId();
        }

        return $model;
    }

    /**
     * Get the store ID from the given store.
     *
     * If the given store is an instance of StoreInterface, the ID will be retrieved
     * from the object using the getId() method. Otherwise, the given value will be
     * used as the store ID.
     *
     * @param StoreInterface|string $store The store to get the ID from.
     *
     * @return string The store ID.
     */
    private static function getStoreId(StoreInterface | string $store): string
    {
        if ($store instanceof StoreInterface) {
            return $store->getId();
        }

        return $store;
    }
}
