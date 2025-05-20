<?php

declare(strict_types=1);

namespace OpenFGA;

use DateTimeImmutable;
use InvalidArgumentException;
use OpenFGA\Authentication\{AccessToken, AccessTokenInterface, AuthenticationInterface, ClientCredentialAuthentication, AuthenticationMode};
use OpenFGA\Models\{AuthorizationModelInterface, StoreInterface, TupleKeyInterface};
use OpenFGA\Models\Collections\{AssertionsInterface, ConditionsInterface, TupleKeysInterface, TypeDefinitionsInterface, UserTypeFiltersInterface};
use OpenFGA\Models\Enums\{Consistency, SchemaVersion};
use OpenFGA\Network\RequestManager;
use OpenFGA\Requests\{CheckRequest, CreateAuthorizationModelRequest, CreateStoreRequest, DeleteStoreRequest, ExpandRequest, GetAuthorizationModelRequest, GetStoreRequest, ListAuthorizationModelsRequest, ListObjectsRequest, ListStoresRequest, ListTupleChangesRequest, ListUsersRequest, ReadAssertionsRequest, ReadTuplesRequest, RequestInterface, WriteAssertionsRequest, WriteTuplesRequest};
use OpenFGA\Responses\{CheckResponse, CheckResponseInterface, CreateAuthorizationModelResponse, CreateAuthorizationModelResponseInterface, CreateStoreResponse, CreateStoreResponseInterface, DeleteStoreResponse, DeleteStoreResponseInterface, ExpandResponse, ExpandResponseInterface, GetAuthorizationModelResponse, GetAuthorizationModelResponseInterface, GetStoreResponse, GetStoreResponseInterface, ListAuthorizationModelsResponse, ListAuthorizationModelsResponseInterface, ListObjectsResponse, ListObjectsResponseInterface, ListStoresResponse, ListStoresResponseInterface, ListTupleChangesResponse, ListTupleChangesResponseInterface, ListUsersResponse, ListUsersResponseInterface, ReadAssertionsResponse, ReadAssertionsResponseInterface, ReadTuplesResponse, ReadTuplesResponseInterface, WriteAssertionsResponse, WriteAssertionsResponseInterface, WriteTuplesResponse, WriteTuplesResponseInterface};
use OpenFGA\Schema\SchemaValidator;

use function sprintf;

final class Client implements ClientInterface
{
    private const MAX_PAGE_SIZE = 1000;

    public const VERSION = '0.2.0';

    private ?\Psr\Http\Message\RequestInterface $lastRequest = null;

    private ?\Psr\Http\Message\ResponseInterface $lastResponse = null;

    private ?RequestManager $requestManager = null;

    private ?SchemaValidator $validator = null;

    /**
     * @param string $url The OpenFGA API URL to connect to
     * @param AuthenticationMode $authenticationMode The authentication mode to use
     * @param null|string $clientId Optional client ID to use for OIDC authentication (AuthenticationMode::CLIENT_CREDENTIALS)
     * @param null|string $clientSecret Optional client secret to use for OIDC authentication (AuthenticationMode::CLIENT_CREDENTIALS)
     * @param null|string $issuer Optional issuer to use for OIDC authentication (AuthenticationMode::CLIENT_CREDENTIALS)
     * @param null|string $audience Optional audience to use for OIDC authentication (AuthenticationMode::CLIENT_CREDENTIALS)
     * @param null|AccessTokenInterface|string $token Optional token to use for pre-shared key authentication (AuthenticationMode::TOKEN)
     * @param null|positive-int $maxRetries Number of times to retry a request before giving up; defaults to 3, disabled if null
     * @param null|\Psr\Http\Client\ClientInterface $httpClient Optional PSR-18 HTTP client to use for requests; will use autodiscovery and use the first available if not specified
     * @param null|\Psr\Http\Message\ResponseFactoryInterface $httpResponseFactory Optional PSR-17 HTTP response factory to use for requests; will use autodiscovery and use the first available if not specified
     * @param null|\Psr\Http\Message\StreamFactoryInterface $httpStreamFactory Optional PSR-17 HTTP stream factory to use for requests; will use autodiscovery and use the first available if not specified
     * @param null|\Psr\Http\Message\RequestFactoryInterface $httpRequestFactory Optional PSR-17 HTTP request factory to use for requests; will use autodiscovery and use the first available if not specified
     */
    public function __construct(
        private readonly string $url,
        private readonly AuthenticationMode $authenticationMode = AuthenticationMode::NONE,
        private readonly ?string $clientId = null,
        private readonly ?string $clientSecret = null,
        private readonly ?string $issuer = null,
        private readonly ?string $audience = null,
        private AccessTokenInterface|string|null $token = null,
        private readonly ?int $maxRetries = 3,
        private readonly ?\Psr\Http\Client\ClientInterface $httpClient = null,
        private readonly ?\Psr\Http\Message\ResponseFactoryInterface $httpResponseFactory = null,
        private readonly ?\Psr\Http\Message\StreamFactoryInterface $httpStreamFactory = null,
        private readonly ?\Psr\Http\Message\RequestFactoryInterface $httpRequestFactory = null,
    ) {
    }

    public function check(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
        TupleKeyInterface $tupleKey,
        ?bool $trace = null,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): CheckResponseInterface {
        $request = new CheckRequest(
            store: self::getStoreId($store),
            authorizationModel: self::getAuthorizationModelId($authorizationModel),
            tupleKey: $tupleKey,
            trace: $trace,
            context: $context,
            contextualTuples: $contextualTuples,
            consistency: $consistency,
        );

        return CheckResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

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

    public function createStore(
        string $name,
    ): CreateStoreResponseInterface {
        $name = trim($name);

        $request = new CreateStoreRequest(
            name: $name,
        );

        return CreateStoreResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function deleteStore(
        StoreInterface | string $store,
    ): DeleteStoreResponseInterface {
        $request = new DeleteStoreRequest(
            store: self::getStoreId($store),
        );

        return DeleteStoreResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function expand(
        StoreInterface | string $store,
        TupleKeyInterface $tupleKey,
        AuthorizationModelInterface | string | null $authorizationModel = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): ExpandResponseInterface {
        $request = new ExpandRequest(
            tupleKey: $tupleKey,
            contextualTuples: $contextualTuples,
            store: self::getStoreId($store),
            authorizationModel: (null !== $authorizationModel) ? self::getAuthorizationModelId($authorizationModel) : null,
            consistency: $consistency,
        );

        return ExpandResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function getAuthorizationModel(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
    ): GetAuthorizationModelResponseInterface {
        $request = new GetAuthorizationModelRequest(
            store: self::getStoreId($store),
            authorizationModel: self::getAuthorizationModelId($authorizationModel),
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
    ): GetStoreResponseInterface {
        $request = new GetStoreRequest(
            store: self::getStoreId($store),
        );

        return GetStoreResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function listAuthorizationModels(
        StoreInterface | string $store,
        ?string $continuationToken = null,
        ?int $pageSize = null,
    ): ListAuthorizationModelsResponseInterface {
        $this->validatePageSize($pageSize);

        $request = new ListAuthorizationModelsRequest(
            store: self::getStoreId($store),
            continuationToken: $continuationToken,
            pageSize: $pageSize,
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
        ?Consistency $consistency = null,
    ): ListObjectsResponseInterface {
        $request = new ListObjectsRequest(
            type: $type,
            relation: $relation,
            user: $user,
            context: $context,
            contextualTuples: $contextualTuples,
            store: self::getStoreId($store),
            authorizationModel: self::getAuthorizationModelId($authorizationModel),
            consistency: $consistency,
        );

        return ListObjectsResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function listStores(
        ?string $continuationToken = null,
        ?int $pageSize = null,
    ): ListStoresResponseInterface {
        $this->validatePageSize($pageSize);

        $request = new ListStoresRequest(
            continuationToken: $continuationToken,
            pageSize: $pageSize,
        );

        return ListStoresResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function listTupleChanges(
        StoreInterface | string $store,
        ?string $continuationToken = null,
        ?int $pageSize = null,
        ?string $type = null,
        ?DateTimeImmutable $startTime = null,
    ): ListTupleChangesResponseInterface {
        $this->validatePageSize($pageSize);

        $request = new ListTupleChangesRequest(
            store: self::getStoreId($store),
            continuationToken: $continuationToken,
            pageSize: $pageSize,
            type: $type,
            startTime: $startTime,
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
        ?Consistency $consistency = null,
    ): ListUsersResponseInterface {
        $request = new ListUsersRequest(
            object: $object,
            relation: $relation,
            userFilters: $userFilters,
            context: $context,
            contextualTuples: $contextualTuples,
            store: self::getStoreId($store),
            authorizationModel: self::getAuthorizationModelId($authorizationModel),
            consistency: $consistency,
        );

        return ListUsersResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function readAssertions(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
    ): ReadAssertionsResponseInterface {
        $request = new ReadAssertionsRequest(
            store: self::getStoreId($store),
            authorizationModel: self::getAuthorizationModelId($authorizationModel),
        );

        return ReadAssertionsResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function readTuples(
        StoreInterface | string $store,
        TupleKeyInterface $tupleKey,
        ?string $continuationToken = null,
        ?int $pageSize = null,
        ?Consistency $consistency = null,
    ): ReadTuplesResponseInterface {
        $this->validatePageSize($pageSize);

        $request = new ReadTuplesRequest(
            tupleKey: $tupleKey,
            store: self::getStoreId($store),
            continuationToken: $continuationToken,
            pageSize: $pageSize,
            consistency: $consistency,
        );

        return ReadTuplesResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function writeAssertions(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
        AssertionsInterface $assertions,
    ): WriteAssertionsResponseInterface {
        $request = new WriteAssertionsRequest(
            assertions: $assertions,
            store: self::getStoreId($store),
            authorizationModel: self::getAuthorizationModelId($authorizationModel),
        );

        return WriteAssertionsResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    public function writeTuples(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $authorizationModel,
        ?TupleKeysInterface $writes = null,
        ?TupleKeysInterface $deletes = null,
    ): WriteTuplesResponseInterface {
        $request = new WriteTuplesRequest(
            writes: $writes,
            deletes: $deletes,
            store: self::getStoreId($store),
            authorizationModel: self::getAuthorizationModelId($authorizationModel),
        );

        return WriteTuplesResponse::fromResponse($this->sendRequest($request), $this->getValidator());
    }

    private function getAuthenticationHeader(): ?string
    {
        // No authentication
        if ($this->authenticationMode === AuthenticationMode::NONE) {
            return null;
        }

        // Pre-shared key authentication state present
        if (is_string($this->token)) {
            if ($this->authenticationMode === AuthenticationMode::TOKEN) {
                return $this->token;
            }

            $this->token = null;
        }

        // Client Credentials / OIDC authentication state present
        if ($this->token instanceof AccessTokenInterface) {
            if ($this->authenticationMode === AuthenticationMode::CLIENT_CREDENTIALS && ! $this->token->isExpired()) {
                return (string) $this->token;
            }

            $this->token = null;
        }

        // Client Credentials / OIDC authentication configured
        if ($this->authenticationMode === AuthenticationMode::CLIENT_CREDENTIALS) {
            $clientId = is_string($this->clientId) && trim($this->clientId) !== '' ? trim($this->clientId) : null;
            $clientSecret = is_string($this->clientSecret) && trim($this->clientSecret) !== '' ? trim($this->clientSecret) : null;
            $issuer = is_string($this->issuer) && trim($this->issuer) !== '' ? trim($this->issuer) : null;
            $audience = is_string($this->audience) && trim($this->audience) !== '' ? trim($this->audience) : null;

            if ($clientId !== null && $clientSecret !== null && $issuer !== null && $audience !== null) {
                $auth = new ClientCredentialAuthentication($clientId, $clientSecret, $audience, $issuer);
                $this->token = AccessToken::fromResponse($this->sendRequest($auth));
                return (string) $this->token;
            }
        }

        return null;
    }

    private function getValidator(): SchemaValidator
    {
        return $this->validator ??= new SchemaValidator();
    }

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

    private function validatePageSize(?int $pageSize): void
    {
        if (null === $pageSize) {
            return;
        }

        if ($pageSize < 1) {
            throw new InvalidArgumentException('Page size must be a positive integer');
        }

        if ($pageSize > self::MAX_PAGE_SIZE) {
            throw new InvalidArgumentException(sprintf('Invalid page size %d: must be between 1 and %d', $pageSize, self::MAX_PAGE_SIZE));
        }
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
