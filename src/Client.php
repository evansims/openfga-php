<?php

declare(strict_types=1);

namespace OpenFGA;

use DateTimeImmutable;
use OpenFGA\Authentication\{AccessToken, AccessTokenInterface, ClientCredentialAuthentication};
use OpenFGA\Language\DslTransformer;
use OpenFGA\Models\{AuthorizationModel, AuthorizationModelInterface, ObjectRelation, StoreInterface, TupleKeyInterface, TypeDefinition, Userset};
use OpenFGA\Models\Collections\{AssertionsInterface, ConditionsInterface, TupleKeysInterface, TypeDefinitionRelations, TypeDefinitions, TypeDefinitionsInterface, UserTypeFiltersInterface, Usersets};
use OpenFGA\Models\Enums\{Consistency, SchemaVersion};
use OpenFGA\Network\RequestManager;
use OpenFGA\Requests\{CheckRequest, CreateAuthorizationModelRequest, CreateStoreRequest, DeleteStoreRequest, ExpandRequest, GetAuthorizationModelRequest, GetStoreRequest, ListAuthorizationModelsRequest, ListObjectsRequest, ListStoresRequest, ListTupleChangesRequest, ListUsersRequest, ReadAssertionsRequest, ReadTuplesRequest, RequestInterface, WriteAssertionsRequest, WriteTuplesRequest};
use OpenFGA\Responses\{CheckResponse, CreateAuthorizationModelResponse, CreateStoreResponse, DeleteStoreResponse, ExpandResponse, GetAuthorizationModelResponse, GetStoreResponse, ListAuthorizationModelsResponse, ListObjectsResponse, ListStoresResponse, ListTupleChangesResponse, ListUsersResponse, ReadAssertionsResponse, ReadTuplesResponse, WriteAssertionsResponse, WriteTuplesResponse};
use OpenFGA\Results\{Failure, ResultInterface, Success};
use OpenFGA\Schema\SchemaValidator;

use Override;
use Throwable;

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
    /**
     * @inheritDoc
     */
    public function check(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        TupleKeyInterface $tupleKey,
        ?bool $trace = null,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): ResultInterface {
        $request = new CheckRequest(
            store: self::getStoreId($store),
            model: self::getModelId($model),
            tupleKey: $tupleKey,
            trace: $trace,
            context: $context,
            contextualTuples: $contextualTuples,
            consistency: $consistency,
        );

        try {
            return new Success(CheckResponse::fromResponse($this->sendRequest($request), $this->getValidator()));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function createAuthorizationModel(
        StoreInterface | string $store,
        TypeDefinitionsInterface $typeDefinitions,
        ConditionsInterface $conditions,
        SchemaVersion $schemaVersion = SchemaVersion::V1_1,
    ): ResultInterface {
        $request = new CreateAuthorizationModelRequest(
            typeDefinitions: $typeDefinitions,
            conditions: $conditions,
            schemaVersion: $schemaVersion,
            store: self::getStoreId($store),
        );

        try {
            return new Success(CreateAuthorizationModelResponse::fromResponse($this->sendRequest($request), $this->getValidator()));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function createStore(
        string $name,
    ): ResultInterface {
        $name = trim($name);

        $request = new CreateStoreRequest(
            name: $name,
        );

        try {
            return new Success(CreateStoreResponse::fromResponse($this->sendRequest($request), $this->getValidator()));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function deleteStore(
        StoreInterface | string $store,
    ): ResultInterface {
        $request = new DeleteStoreRequest(
            store: self::getStoreId($store),
        );

        try {
            return new Success(DeleteStoreResponse::fromResponse($this->sendRequest($request), $this->getValidator()));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function dsl(string $dsl): ResultInterface
    {
        try {
            $validator = $this->getValidator();

            $validator
                ->registerSchema(AuthorizationModel::schema())
                ->registerSchema(TypeDefinitions::schema())
                ->registerSchema(TypeDefinition::schema())
                ->registerSchema(TypeDefinitionRelations::schema())
                ->registerSchema(Userset::schema())
                ->registerSchema(Usersets::schema())
                ->registerSchema(ObjectRelation::schema());

            return new Success(DslTransformer::fromDsl($dsl, $validator));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function expand(
        StoreInterface | string $store,
        TupleKeyInterface $tupleKey,
        AuthorizationModelInterface | string | null $model = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): ResultInterface {
        $request = new ExpandRequest(
            tupleKey: $tupleKey,
            contextualTuples: $contextualTuples,
            store: self::getStoreId($store),
            model: (null !== $model) ? self::getModelId($model) : null,
            consistency: $consistency,
        );

        try {
            return new Success(ExpandResponse::fromResponse($this->sendRequest($request), $this->getValidator()));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getAuthorizationModel(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
    ): ResultInterface {
        $request = new GetAuthorizationModelRequest(
            store: self::getStoreId($store),
            model: self::getModelId($model),
        );

        try {
            return new Success(GetAuthorizationModelResponse::fromResponse($this->sendRequest($request), $this->getValidator()));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getLastRequest(): ?\Psr\Http\Message\RequestInterface
    {
        return $this->lastRequest;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getLastResponse(): ?\Psr\Http\Message\ResponseInterface
    {
        return $this->lastResponse;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getStore(
        StoreInterface | string $store,
    ): ResultInterface {
        $request = new GetStoreRequest(
            store: self::getStoreId($store),
        );

        try {
            return new Success(GetStoreResponse::fromResponse($this->sendRequest($request), $this->getValidator()));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function listAuthorizationModels(
        StoreInterface | string $store,
        ?string $continuationToken = null,
        ?int $pageSize = null,
    ): ResultInterface {
        $pageSize = null !== $pageSize ? max(1, min($pageSize, self::MAX_PAGE_SIZE)) : null;

        $request = new ListAuthorizationModelsRequest(
            store: self::getStoreId($store),
            continuationToken: $continuationToken,
            pageSize: $pageSize,
        );

        try {
            return new Success(ListAuthorizationModelsResponse::fromResponse($this->sendRequest($request), $this->getValidator()));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    #[Override]
    /**
     * @inheritDoc
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
    ): ResultInterface {
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

        try {
            return new Success(ListObjectsResponse::fromResponse($this->sendRequest($request), $this->getValidator()));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function listStores(
        ?string $continuationToken = null,
        ?int $pageSize = null,
    ): ResultInterface {
        $pageSize = null !== $pageSize ? max(1, min($pageSize, self::MAX_PAGE_SIZE)) : null;

        $request = new ListStoresRequest(
            continuationToken: $continuationToken,
            pageSize: $pageSize,
        );

        try {
            return new Success(ListStoresResponse::fromResponse($this->sendRequest($request), $this->getValidator()));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function listTupleChanges(
        StoreInterface | string $store,
        ?string $continuationToken = null,
        ?int $pageSize = null,
        ?string $type = null,
        ?DateTimeImmutable $startTime = null,
    ): ResultInterface {
        $pageSize = null !== $pageSize ? max(1, min($pageSize, self::MAX_PAGE_SIZE)) : null;

        $request = new ListTupleChangesRequest(
            store: self::getStoreId($store),
            continuationToken: $continuationToken,
            pageSize: $pageSize,
            type: $type,
            startTime: $startTime,
        );

        try {
            return new Success(ListTupleChangesResponse::fromResponse($this->sendRequest($request), $this->getValidator()));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    #[Override]
    /**
     * @inheritDoc
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
    ): ResultInterface {
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

        try {
            return new Success(ListUsersResponse::fromResponse($this->sendRequest($request), $this->getValidator()));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function readAssertions(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
    ): ResultInterface {
        $request = new ReadAssertionsRequest(
            store: self::getStoreId($store),
            model: self::getModelId($model),
        );

        try {
            return new Success(ReadAssertionsResponse::fromResponse($this->sendRequest($request), $this->getValidator()));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function readTuples(
        StoreInterface | string $store,
        TupleKeyInterface $tupleKey,
        ?string $continuationToken = null,
        ?int $pageSize = null,
        ?Consistency $consistency = null,
    ): ResultInterface {
        $pageSize = null !== $pageSize ? max(1, min($pageSize, self::MAX_PAGE_SIZE)) : null;

        $request = new ReadTuplesRequest(
            tupleKey: $tupleKey,
            store: self::getStoreId($store),
            continuationToken: $continuationToken,
            pageSize: $pageSize,
            consistency: $consistency,
        );

        try {
            return new Success(ReadTuplesResponse::fromResponse($this->sendRequest($request), $this->getValidator()));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function writeAssertions(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        AssertionsInterface $assertions,
    ): ResultInterface {
        $request = new WriteAssertionsRequest(
            assertions: $assertions,
            store: self::getStoreId($store),
            model: self::getModelId($model),
        );

        try {
            return new Success(WriteAssertionsResponse::fromResponse($this->sendRequest($request), $this->getValidator()));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function writeTuples(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        ?TupleKeysInterface $writes = null,
        ?TupleKeysInterface $deletes = null,
    ): ResultInterface {
        $request = new WriteTuplesRequest(
            writes: $writes,
            deletes: $deletes,
            store: self::getStoreId($store),
            model: self::getModelId($model),
        );

        try {
            return new Success(WriteTuplesResponse::fromResponse($this->sendRequest($request), $this->getValidator()));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
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
