<?php

declare(strict_types=1);

namespace OpenFGA;

use DateTimeImmutable;
use InvalidArgumentException;
use JsonException;
use OpenFGA\Authentication\{AuthenticationInterface, ClientCredentialAuthentication};
use OpenFGA\Exceptions\{ClientError, ConfigurationError, NetworkException};
use OpenFGA\Exceptions\ClientThrowable;
use OpenFGA\Models\{AuthorizationModel, AuthorizationModelInterface, DifferenceV1, Metadata, ObjectRelation, RelationMetadata, RelationReference, SourceInfo, StoreInterface, TupleKeyInterface, TupleToUsersetV1, TypeDefinition, Userset};
use OpenFGA\Models\Collections\{AssertionsInterface, Conditions, ConditionsInterface, RelationMetadataCollection, RelationReferences, TupleKeysInterface, TypeDefinitionRelations, TypeDefinitions, TypeDefinitionsInterface, UserTypeFiltersInterface, Usersets};
use OpenFGA\Models\Collections\BatchCheckItemsInterface;
use OpenFGA\Models\Enums\{Consistency, SchemaVersion};
use OpenFGA\Network\{RequestContext, RequestManager};
use OpenFGA\Observability\{NoOpTelemetryProvider, TelemetryInterface};
use OpenFGA\Requests\{BatchCheckRequest, CheckRequest, CreateAuthorizationModelRequest, CreateStoreRequest, DeleteStoreRequest, ExpandRequest, GetAuthorizationModelRequest, GetStoreRequest, ListAuthorizationModelsRequest, ListObjectsRequest, ListStoresRequest, ListTupleChangesRequest, ListUsersRequest, ReadAssertionsRequest, ReadTuplesRequest, RequestInterface, StreamedListObjectsRequest, WriteAssertionsRequest, WriteTuplesRequest};
use OpenFGA\Responses\{BatchCheckResponse, CheckResponse, CreateAuthorizationModelResponse, CreateStoreResponse, DeleteStoreResponse, ExpandResponse, GetAuthorizationModelResponse, GetStoreResponse, ListAuthorizationModelsResponse, ListObjectsResponse, ListStoresResponse, ListTupleChangesResponse, ListUsersResponse, ReadAssertionsResponse, ReadTuplesResponse, StreamedListObjectsResponse, WriteAssertionsResponse, WriteTuplesResponse};
use OpenFGA\Results\{Failure, FailureInterface, Success, SuccessInterface};
use OpenFGA\Schema\SchemaValidator;
use OpenFGA\Translation\Translator;
use Override;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\{RequestFactoryInterface, RequestInterface as HttpRequestInterface, ResponseFactoryInterface, ResponseInterface as HttpResponseInterface, StreamFactoryInterface};
use PsrDiscovery\Discover;
use ReflectionException;
use Throwable;

/**
 * OpenFGA Client implementation for relationship-based access control operations.
 *
 * This client provides a complete implementation of the OpenFGA API, supporting
 * all core operations including store management, authorization model configuration,
 * relationship tuple operations, and authorization checks. The client uses PSR-7,
 * PSR-17 and PSR-18 HTTP standards and implements the Result pattern for error handling.
 *
 * The client supports multiple authentication methods including OAuth 2.0 Client
 * Credentials flow and pre-shared key authentication, with automatic token management
 * and retry capabilities for reliable operation.
 *
 * @see ClientInterface For the complete API specification
 * @see https://openfga.dev/docs/getting-started/setup-sdk-client Setting up the client
 */
final class Client implements ClientInterface
{
    /**
     * The version of the OpenFGA PHP SDK.
     */
    public const string VERSION = '1.2.0';

    /**
     * Maximum page size for API responses.
     */
    private const int MAX_PAGE_SIZE = 1000;

    /**
     * The last HTTP request made by the client.
     */
    private ?HttpRequestInterface $lastRequest = null;

    /**
     * The last HTTP response received by the client.
     */
    private ?HttpResponseInterface $lastResponse = null;

    /**
     * The request manager used to send network requests.
     */
    private ?RequestManager $requestManager = null;

    /**
     * The JSON schema validator used to validate responses.
     */
    private ?SchemaValidator $validator = null;

    /**
     * Create a new OpenFGA client instance.
     *
     * Configures the client with the specified OpenFGA API endpoint and authentication
     * settings. The client supports multiple authentication methods and uses PSR HTTP
     * factories for maximum compatibility with existing applications.
     *
     * @param string                        $url                 The OpenFGA API URL to connect to
     * @param AuthenticationInterface|null  $authentication      The authentication strategy to use for API requests
     * @param string                        $language            The language code for i18n translations; defaults to 'en' (English)
     * @param positive-int|null             $httpMaxRetries      Number of times to retry a request before giving up; defaults to 3, disabled if null
     * @param HttpClientInterface|null      $httpClient          Optional PSR-18 HTTP client to use for requests; will use autodiscovery and use the first available if not specified
     * @param ResponseFactoryInterface|null $httpResponseFactory Optional PSR-17 HTTP response factory to use for requests; will use autodiscovery and use the first available if not specified
     * @param StreamFactoryInterface|null   $httpStreamFactory   Optional PSR-17 HTTP stream factory to use for requests; will use autodiscovery and use the first available if not specified
     * @param RequestFactoryInterface|null  $httpRequestFactory  Optional PSR-17 HTTP request factory to use for requests; will use autodiscovery and use the first available if not specified
     * @param TelemetryInterface|null       $telemetry           Optional telemetry provider for observability; defaults to no-op implementation
     *
     * @see https://openfga.dev/docs/getting-started/setup-sdk-client Client configuration guide
     */
    public function __construct(
        private readonly string $url,
        private readonly ?AuthenticationInterface $authentication = null,
        private readonly string $language = 'en',
        private readonly ?int $httpMaxRetries = 3,
        private readonly ?HttpClientInterface $httpClient = null,
        private readonly ?ResponseFactoryInterface $httpResponseFactory = null,
        private readonly ?StreamFactoryInterface $httpStreamFactory = null,
        private readonly ?RequestFactoryInterface $httpRequestFactory = null,
        private readonly ?TelemetryInterface $telemetry = null,
    ) {
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException If request parameters are invalid
     * @throws ReflectionException      If exception creation fails
     */
    #[Override]
    public function assertLastRequest(): HttpRequestInterface
    {
        if (! $this->lastRequest instanceof HttpRequestInterface) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::NO_LAST_REQUEST_FOUND, [], $this->language)]);
        }

        return $this->lastRequest;
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable
     */
    #[Override]
    public function batchCheck(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        BatchCheckItemsInterface $checks,
    ): FailureInterface | SuccessInterface {
        return $this->withLanguageContext(
            function () use ($store, $model, $checks) {
                try {
                    $request = new BatchCheckRequest(
                        store: self::getStoreId($store),
                        model: self::getModelId($model),
                        checks: $checks,
                    );

                    return new Success(BatchCheckResponse::fromResponse($this->sendRequest($request), $this->assertLastRequest(), $this->getValidator()));
                } catch (Throwable $throwable) {
                    return new Failure($throwable);
                }
            },
            'batchCheck',
            $store,
            $model,
        );
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable
     */
    #[Override]
    public function check(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        TupleKeyInterface $tupleKey,
        ?bool $trace = null,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): FailureInterface | SuccessInterface {
        return $this->withLanguageContext(
            function () use ($store, $model, $tupleKey, $trace, $context, $contextualTuples, $consistency) {
                try {
                    $request = new CheckRequest(
                        store: self::getStoreId($store),
                        model: self::getModelId($model),
                        tupleKey: $tupleKey,
                        trace: $trace,
                        context: $context,
                        contextualTuples: $contextualTuples,
                        consistency: $consistency,
                    );

                    return new Success(CheckResponse::fromResponse($this->sendRequest($request), $this->assertLastRequest(), $this->getValidator()));
                } catch (Throwable $throwable) {
                    return new Failure($throwable);
                }
            },
            'check',
            $store,
            $model,
        );
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable
     */
    #[Override]
    public function createAuthorizationModel(
        StoreInterface | string $store,
        TypeDefinitionsInterface $typeDefinitions,
        ?ConditionsInterface $conditions = null,
        SchemaVersion $schemaVersion = SchemaVersion::V1_1,
    ): FailureInterface | SuccessInterface {
        return $this->withLanguageContext(function () use ($store, $typeDefinitions, $conditions, $schemaVersion) {
            try {
                $request = new CreateAuthorizationModelRequest(
                    typeDefinitions: $typeDefinitions,
                    conditions: $conditions,
                    schemaVersion: $schemaVersion,
                    store: self::getStoreId($store),
                );

                return new Success(CreateAuthorizationModelResponse::fromResponse($this->sendRequest($request), $this->assertLastRequest(), $this->getValidator()));
            } catch (Throwable $throwable) {
                return new Failure($throwable);
            }
        });
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable
     */
    #[Override]
    public function createStore(
        string $name,
    ): FailureInterface | SuccessInterface {
        return $this->withLanguageContext(function () use ($name) {
            try {
                $name = trim($name);

                $request = new CreateStoreRequest(
                    name: $name,
                );

                return new Success(CreateStoreResponse::fromResponse($this->sendRequest($request), $this->assertLastRequest(), $this->getValidator()));
            } catch (Throwable $throwable) {
                return new Failure($throwable);
            }
        });
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable
     */
    #[Override]
    public function deleteStore(
        StoreInterface | string $store,
    ): FailureInterface | SuccessInterface {
        return $this->withLanguageContext(function () use ($store) {
            try {
                $request = new DeleteStoreRequest(
                    store: self::getStoreId($store),
                );

                return new Success(DeleteStoreResponse::fromResponse($this->sendRequest($request), $this->assertLastRequest(), $this->getValidator()));
            } catch (Throwable $throwable) {
                return new Failure($throwable);
            }
        });
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable
     */
    #[Override]
    public function dsl(string $dsl): FailureInterface | SuccessInterface
    {
        return $this->withLanguageContext(function () use ($dsl) {
            try {
                $validator = $this->getValidator();

                $validator
                    ->registerSchema(AuthorizationModel::schema())
                    ->registerSchema(TypeDefinitions::schema())
                    ->registerSchema(TypeDefinition::schema())
                    ->registerSchema(TypeDefinitionRelations::schema())
                    ->registerSchema(Userset::schema())
                    ->registerSchema(Usersets::schema())
                    ->registerSchema(ObjectRelation::schema())
                    ->registerSchema(TupleToUsersetV1::schema())
                    ->registerSchema(DifferenceV1::schema())
                    ->registerSchema(Metadata::schema())
                    ->registerSchema(RelationMetadata::schema())
                    ->registerSchema(RelationReferences::schema())
                    ->registerSchema(RelationReference::schema())
                    ->registerSchema(SourceInfo::schema())
                    ->registerSchema(Conditions::schema())
                    ->registerSchema(RelationMetadataCollection::schema());

                return new Success(Transformer::fromDsl($dsl, $validator));
            } catch (Throwable $throwable) {
                return new Failure($throwable);
            }
        });
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable
     */
    #[Override]
    public function expand(
        StoreInterface | string $store,
        TupleKeyInterface $tupleKey,
        AuthorizationModelInterface | string | null $model = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): FailureInterface | SuccessInterface {
        return $this->withLanguageContext(function () use ($store, $tupleKey, $model, $contextualTuples, $consistency) {
            try {
                $request = new ExpandRequest(
                    tupleKey: $tupleKey,
                    contextualTuples: $contextualTuples,
                    store: self::getStoreId($store),
                    model: (null !== $model) ? self::getModelId($model) : null,
                    consistency: $consistency,
                );

                return new Success(ExpandResponse::fromResponse($this->sendRequest($request), $this->assertLastRequest(), $this->getValidator()));
            } catch (Throwable $throwable) {
                return new Failure($throwable);
            }
        });
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable
     */
    #[Override]
    public function getAuthorizationModel(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
    ): FailureInterface | SuccessInterface {
        return $this->withLanguageContext(function () use ($store, $model) {
            try {
                $request = new GetAuthorizationModelRequest(
                    store: self::getStoreId($store),
                    model: self::getModelId($model),
                );

                return new Success(GetAuthorizationModelResponse::fromResponse($this->sendRequest($request), $this->assertLastRequest(), $this->getValidator()));
            } catch (Throwable $throwable) {
                return new Failure($throwable);
            }
        });
    }

    /**
     * Get the configured language for i18n translations.
     *
     * @return string The configured language code
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getLastRequest(): ?HttpRequestInterface
    {
        return $this->lastRequest;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getLastResponse(): ?HttpResponseInterface
    {
        return $this->lastResponse;
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable
     */
    #[Override]
    public function getStore(
        StoreInterface | string $store,
    ): FailureInterface | SuccessInterface {
        return $this->withLanguageContext(function () use ($store) {
            try {
                $request = new GetStoreRequest(
                    store: self::getStoreId($store),
                );

                $response = $this->sendRequest($request);

                return new Success(GetStoreResponse::fromResponse($response, $this->assertLastRequest(), $this->getValidator()));
            } catch (Throwable $throwable) {
                return new Failure($throwable);
            }
        });
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable
     */
    #[Override]
    public function listAuthorizationModels(
        StoreInterface | string $store,
        ?string $continuationToken = null,
        ?int $pageSize = null,
    ): FailureInterface | SuccessInterface {
        return $this->withLanguageContext(function () use ($store, $continuationToken, $pageSize) {
            try {
                $pageSize = null !== $pageSize ? max(1, min($pageSize, self::MAX_PAGE_SIZE)) : null;

                $request = new ListAuthorizationModelsRequest(
                    store: self::getStoreId($store),
                    continuationToken: $continuationToken,
                    pageSize: $pageSize,
                );

                return new Success(ListAuthorizationModelsResponse::fromResponse($this->sendRequest($request), $this->assertLastRequest(), $this->getValidator()));
            } catch (Throwable $throwable) {
                return new Failure($throwable);
            }
        });
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable
     */
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
    ): FailureInterface | SuccessInterface {
        return $this->withLanguageContext(function () use ($store, $model, $type, $relation, $user, $context, $contextualTuples, $consistency) {
            try {
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

                return new Success(ListObjectsResponse::fromResponse($this->sendRequest($request), $this->assertLastRequest(), $this->getValidator()));
            } catch (Throwable $throwable) {
                return new Failure($throwable);
            }
        });
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable
     */
    #[Override]
    public function listStores(
        ?string $continuationToken = null,
        ?int $pageSize = null,
    ): FailureInterface | SuccessInterface {
        return $this->withLanguageContext(function () use ($continuationToken, $pageSize) {
            try {
                $pageSize = null !== $pageSize ? max(1, min($pageSize, self::MAX_PAGE_SIZE)) : null;

                $request = new ListStoresRequest(
                    continuationToken: $continuationToken,
                    pageSize: $pageSize,
                );

                return new Success(ListStoresResponse::fromResponse($this->sendRequest($request), $this->assertLastRequest(), $this->getValidator()));
            } catch (Throwable $throwable) {
                return new Failure($throwable);
            }
        });
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable
     */
    #[Override]
    public function listTupleChanges(
        StoreInterface | string $store,
        ?string $continuationToken = null,
        ?int $pageSize = null,
        ?string $type = null,
        ?DateTimeImmutable $startTime = null,
    ): FailureInterface | SuccessInterface {
        return $this->withLanguageContext(function () use ($store, $continuationToken, $pageSize, $type, $startTime) {
            try {
                $pageSize = null !== $pageSize ? max(1, min($pageSize, self::MAX_PAGE_SIZE)) : null;

                $request = new ListTupleChangesRequest(
                    store: self::getStoreId($store),
                    continuationToken: $continuationToken,
                    pageSize: $pageSize,
                    type: $type,
                    startTime: $startTime,
                );

                return new Success(ListTupleChangesResponse::fromResponse($this->sendRequest($request), $this->assertLastRequest(), $this->getValidator()));
            } catch (Throwable $throwable) {
                return new Failure($throwable);
            }
        });
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable
     */
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
    ): FailureInterface | SuccessInterface {
        return $this->withLanguageContext(function () use ($store, $model, $object, $relation, $userFilters, $context, $contextualTuples, $consistency) {
            try {
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

                return new Success(ListUsersResponse::fromResponse($this->sendRequest($request), $this->assertLastRequest(), $this->getValidator()));
            } catch (Throwable $throwable) {
                return new Failure($throwable);
            }
        });
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable
     */
    #[Override]
    public function readAssertions(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
    ): FailureInterface | SuccessInterface {
        return $this->withLanguageContext(function () use ($store, $model) {
            try {
                $request = new ReadAssertionsRequest(
                    store: self::getStoreId($store),
                    model: self::getModelId($model),
                );

                return new Success(ReadAssertionsResponse::fromResponse($this->sendRequest($request), $this->assertLastRequest(), $this->getValidator()));
            } catch (Throwable $throwable) {
                return new Failure($throwable);
            }
        });
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable
     */
    #[Override]
    public function readTuples(
        StoreInterface | string $store,
        TupleKeyInterface $tupleKey,
        ?string $continuationToken = null,
        ?int $pageSize = null,
        ?Consistency $consistency = null,
    ): FailureInterface | SuccessInterface {
        return $this->withLanguageContext(function () use ($store, $tupleKey, $continuationToken, $pageSize, $consistency) {
            try {
                $pageSize = null !== $pageSize ? max(1, min($pageSize, self::MAX_PAGE_SIZE)) : null;

                $request = new ReadTuplesRequest(
                    tupleKey: $tupleKey,
                    store: self::getStoreId($store),
                    continuationToken: $continuationToken,
                    pageSize: $pageSize,
                    consistency: $consistency,
                );

                return new Success(ReadTuplesResponse::fromResponse($this->sendRequest($request), $this->assertLastRequest(), $this->getValidator()));
            } catch (Throwable $throwable) {
                return new Failure($throwable);
            }
        });
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable If request processing fails
     */
    #[Override]
    public function streamedListObjects(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        string $type,
        string $relation,
        string $user,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): FailureInterface | SuccessInterface {
        return $this->withLanguageContext(function () use ($store, $model, $type, $relation, $user, $context, $contextualTuples, $consistency) {
            try {
                $request = new StreamedListObjectsRequest(
                    type: $type,
                    relation: $relation,
                    user: $user,
                    context: $context,
                    contextualTuples: $contextualTuples,
                    store: self::getStoreId($store),
                    model: self::getModelId($model),
                    consistency: $consistency,
                );

                return new Success(StreamedListObjectsResponse::fromResponse($this->sendRequest($request), $this->assertLastRequest(), $this->getValidator()));
            } catch (Throwable $throwable) {
                return new Failure($throwable);
            }
        });
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable
     */
    #[Override]
    public function writeAssertions(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        AssertionsInterface $assertions,
    ): FailureInterface | SuccessInterface {
        return $this->withLanguageContext(function () use ($store, $model, $assertions) {
            try {
                $request = new WriteAssertionsRequest(
                    assertions: $assertions,
                    store: self::getStoreId($store),
                    model: self::getModelId($model),
                );

                return new Success(WriteAssertionsResponse::fromResponse($this->sendRequest($request), $this->assertLastRequest(), $this->getValidator()));
            } catch (Throwable $throwable) {
                return new Failure($throwable);
            }
        });
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable
     */
    #[Override]
    public function writeTuples(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        ?TupleKeysInterface $writes = null,
        ?TupleKeysInterface $deletes = null,
    ): FailureInterface | SuccessInterface {
        return $this->withLanguageContext(function () use ($store, $model, $writes, $deletes) {
            try {
                $request = new WriteTuplesRequest(
                    writes: $writes,
                    deletes: $deletes,
                    store: self::getStoreId($store),
                    model: self::getModelId($model),
                );

                return new Success(WriteTuplesResponse::fromResponse($this->sendRequest($request), $this->assertLastRequest(), $this->getValidator()));
            } catch (Throwable $throwable) {
                return new Failure($throwable);
            }
        });
    }

    /**
     * Get the authorization model ID from a given authorization model.
     *
     * If an instance of AuthorizationModelInterface is provided, the ID will be
     * retrieved from the object using the getId() method. Otherwise, the value
     * will be used as the authorization model ID.
     *
     * @param  AuthorizationModelInterface|string $model the authorization model to get the ID from
     * @return string                             the authorization model ID
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
     * @param  StoreInterface|string $store the store to get the ID from
     * @return string                the store ID
     */
    private static function getStoreId(StoreInterface | string $store): string
    {
        if ($store instanceof StoreInterface) {
            return $store->getId();
        }

        return $store;
    }

    /**
     * Return the authentication header string.
     *
     * Delegates to the configured authentication strategy to get the
     * authorization header value. If the strategy needs to perform an
     * authentication request, this method will handle that flow.
     *
     * @throws ClientThrowable          If stream factory configuration fails
     * @throws InvalidArgumentException If request parameters are invalid
     * @throws ReflectionException      If exception creation fails
     */
    private function getAuthenticationHeader(): ?string
    {
        if (! $this->authentication instanceof AuthenticationInterface) {
            return null;
        }

        $authentication = $this->authentication;
        $header = $authentication->getAuthorizationHeader();
        if (null !== $header) {
            return $header;
        }

        $authRequest = $authentication->getAuthenticationRequest($this->getStreamFactory());
        if (! $authRequest instanceof RequestContext) {
            return null;
        }

        try {
            $authWrapper = new class($authRequest) implements RequestInterface {
                public function __construct(private readonly RequestContext $context)
                {
                }

                #[Override]
                public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
                {
                    return $this->context;
                }
            };

            $response = $this->sendAuthenticationRequest($authWrapper);

            if ($authentication instanceof ClientCredentialAuthentication) {
                $authentication->handleAuthenticationResponse($response);

                return $authentication->getAuthorizationHeader();
            }
        } catch (Throwable) {
            return null;
        }

        return null;
    }

    /**
     * Get or create a stream factory for HTTP requests.
     *
     * @throws ClientThrowable          If no stream factory is configured and auto-discovery fails
     * @throws InvalidArgumentException If request parameters are invalid
     * @throws ReflectionException      If exception creation fails
     */
    private function getStreamFactory(): StreamFactoryInterface
    {
        if ($this->httpStreamFactory instanceof StreamFactoryInterface) {
            return $this->httpStreamFactory;
        }

        $httpStreamFactory = Discover::httpStreamFactory();

        if (null === $httpStreamFactory) {
            throw ConfigurationError::HttpStreamFactoryMissing->exception();
        }

        return $httpStreamFactory;
    }

    /**
     * Get the telemetry provider for observability instrumentation.
     *
     * Returns the configured telemetry provider, or a no-op implementation
     * if no telemetry was configured. This ensures telemetry calls are
     * always safe to make without null checks.
     *
     * @return TelemetryInterface The telemetry provider
     */
    private function getTelemetry(): TelemetryInterface
    {
        return $this->telemetry ?? new NoOpTelemetryProvider;
    }

    /**
     * Gets the SchemaValidator singleton used to validate response data.
     */
    private function getValidator(): SchemaValidator
    {
        if (! $this->validator instanceof SchemaValidator) {
            $this->validator = new SchemaValidator;
        }

        return $this->validator;
    }

    /**
     * Send an authentication request without authorization header.
     *
     * @param RequestInterface $request
     *
     * @throws ClientThrowable          If request conversion or sending fails
     * @throws InvalidArgumentException If request parameters are invalid
     * @throws JsonException            If request data serialization fails
     * @throws ReflectionException      If exception creation fails
     */
    private function sendAuthenticationRequest(RequestInterface $request): HttpResponseInterface
    {
        $httpMaxRetries = max(1, min($this->httpMaxRetries ?? 3, 10));

        $requestManager = new RequestManager(
            url: $this->url,
            maxRetries: $httpMaxRetries,
            authorizationHeader: null,
            httpClient: $this->httpClient,
            httpStreamFactory: $this->httpStreamFactory,
            httpRequestFactory: $this->httpRequestFactory,
            httpResponseFactory: $this->httpResponseFactory,
        );

        $httpRequest = $requestManager->request($request);

        return $requestManager->send($httpRequest);
    }

    /**
     * Sends a request to the OpenFGA API using the configured HTTP client and authentication.
     *
     * @param RequestInterface $request the request to send
     *
     * @throws ClientThrowable          If request conversion or sending fails due to configuration, authentication, or network issues
     * @throws InvalidArgumentException If request parameters are invalid
     * @throws JsonException            If request data serialization fails
     * @throws ReflectionException      If exception creation fails
     * @throws Throwable                Any exception that might occur during the request
     *
     * @return HttpResponseInterface the response from the API
     */
    private function sendRequest(RequestInterface $request): HttpResponseInterface
    {
        // Validate and normalize maxRetries parameter (0-15 range, default 3)
        $httpMaxRetries = $this->httpMaxRetries ?? 3;
        $httpMaxRetries = max(0, min($httpMaxRetries, 15));

        $this->requestManager ??= new RequestManager(
            url: $this->url,
            maxRetries: $httpMaxRetries,
            authorizationHeader: $this->getAuthenticationHeader(),
            httpClient: $this->httpClient,
            httpStreamFactory: $this->httpStreamFactory,
            httpRequestFactory: $this->httpRequestFactory,
            httpResponseFactory: $this->httpResponseFactory,
            telemetry: $this->telemetry,
        );

        $requestManager = $this->requestManager;
        $lastRequest = $requestManager->request($request);
        $this->lastRequest = $lastRequest;

        $telemetry = $this->getTelemetry();

        /** @var mixed $httpSpan */
        $httpSpan = $telemetry->startHttpRequest($lastRequest);

        try {
            $lastResponse = $requestManager->send($lastRequest);
            $this->lastResponse = $lastResponse;
            $telemetry->endHttpRequest($httpSpan, $lastResponse);

            return $lastResponse;
        } catch (NetworkException $networkException) {
            // Capture the response from the NetworkException if available
            if ($networkException->response() instanceof HttpResponseInterface) {
                $this->lastResponse = $networkException->response();
            }

            $telemetry->endHttpRequest($httpSpan, $this->lastResponse, $networkException);

            throw $networkException;
        } catch (Throwable $throwable) {
            // Other exceptions don't carry response information
            $telemetry->endHttpRequest($httpSpan, null, $throwable);

            throw $throwable;
        }
    }

    /**
     * Execute a callback with the client's language set as the default locale.
     *
     * This method temporarily sets the Translator's default locale to the client's
     * configured language, executes the callback, and then restores the original
     * default locale. This ensures that any translations performed during the
     * callback will use the client's language preference.
     *
     * @template T
     *
     * @param callable(): T                           $callback  The callback to execute with the language context
     * @param string|null                             $operation Optional operation name for telemetry
     * @param StoreInterface|string|null              $store     Optional store for telemetry
     * @param AuthorizationModelInterface|string|null $model     Optional model for telemetry
     *
     * @throws Throwable Any exception thrown by the callback is re-thrown after cleanup
     *
     * @return T The result of the callback
     */
    private function withLanguageContext(
        callable $callback,
        ?string $operation = null,
        StoreInterface | string | null $store = null,
        AuthorizationModelInterface | string | null $model = null,
    ) {
        $originalLocale = Translator::getDefaultLocale();
        $telemetry = $this->getTelemetry();

        /** @var mixed $span */
        $span = null;

        // Start telemetry span if operation details provided
        if (null !== $operation && null !== $store) {
            /** @var mixed $span */
            $span = $telemetry->startOperation($operation, $store, $model);
        }

        $startTime = microtime(true);

        try {
            Translator::setDefaultLocale($this->language);

            $result = $callback();

            // Record successful operation
            if (null !== $span && null !== $operation && null !== $store) {
                $duration = microtime(true) - $startTime;
                $telemetry->endOperation($span, true);
                $telemetry->recordOperationMetrics($operation, $duration, $store, $model);
            }

            return $result;
        } catch (Throwable $throwable) {
            // Record failed operation
            if (null !== $span && null !== $operation && null !== $store) {
                $duration = microtime(true) - $startTime;
                $telemetry->endOperation($span, false, $throwable);
                $telemetry->recordOperationMetrics($operation, $duration, $store, $model, ['error' => true]);
            }

            throw $throwable;
        } finally {
            Translator::setDefaultLocale($originalLocale);
        }
    }
}
