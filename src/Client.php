<?php

declare(strict_types=1);

namespace OpenFGA;

use DateTimeImmutable;
use InvalidArgumentException;
use LogicException;
use OpenFGA\Authentication\AuthenticationInterface;
use OpenFGA\Events\{EventDispatcher, EventDispatcherInterface, HttpRequestSentEvent, HttpResponseReceivedEvent, OperationCompletedEvent, OperationStartedEvent};
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Language\Transformer;
use OpenFGA\Models\{AuthorizationModel, AuthorizationModelInterface, DifferenceV1, Metadata, ObjectRelation, RelationMetadata, RelationReference, SourceInfo, StoreInterface, TupleKeyInterface, TupleToUsersetV1, TypeDefinition, Userset};
use OpenFGA\Models\Collections\{AssertionsInterface, BatchCheckItemsInterface, Conditions, ConditionsInterface, RelationMetadataCollection, RelationReferences, TupleKeysInterface, TypeDefinitionRelations, TypeDefinitions, TypeDefinitionsInterface, UserTypeFiltersInterface, Usersets};
use OpenFGA\Models\Enums\{Consistency, SchemaVersion};
use OpenFGA\Network\{RequestManager, RetryStrategyInterface};
use OpenFGA\Observability\{NoOpTelemetryProvider, TelemetryEventListener, TelemetryEventListenerInterface, TelemetryInterface};
use OpenFGA\Repositories\{HttpAssertionRepository, HttpModelRepository, HttpStoreRepository, HttpTupleRepository, StoreRepositoryInterface, TupleRepositoryInterface};
use OpenFGA\Results\{Failure, FailureInterface, Success, SuccessInterface};
use OpenFGA\Schemas\{SchemaValidator, SchemaValidatorInterface};
use OpenFGA\Services\{AssertionService, AuthenticationService, AuthenticationServiceInterface, AuthorizationService, AuthorizationServiceInterface, EventAwareTelemetryService, HttpService, HttpServiceInterface, ModelService, StoreService, StoreServiceInterface, TelemetryServiceInterface, TupleFilterService, TupleFilterServiceInterface, TupleService};
use OpenFGA\Translation\Translator;
use Override;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\{RequestFactoryInterface, RequestInterface as HttpRequestInterface, ResponseFactoryInterface, ResponseInterface as HttpResponseInterface, StreamFactoryInterface};
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
    public const string VERSION = '1.3.0';

    private ?AuthenticationServiceInterface $authenticationService = null;

    private ?AuthorizationServiceInterface $authorizationService = null;

    private ?EventDispatcherInterface $eventDispatcher = null;

    private ?HttpServiceInterface $httpService = null;

    private ?SchemaValidatorInterface $schemaValidator = null;

    private ?StoreRepositoryInterface $storeRepository = null;

    private ?StoreServiceInterface $storeService = null;

    /**
     * @var array<string, object> Store-specific service instances cache
     */
    private array $storeSpecificServices = [];

    private ?TelemetryEventListenerInterface $telemetryListener = null;

    private ?TelemetryServiceInterface $telemetryService = null;

    private ?TupleFilterServiceInterface $tupleFilterService = null;

    private ?TupleRepositoryInterface $tupleRepository = null;

    /**
     * Create a new OpenFGA client instance.
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
     * @param RetryStrategyInterface|null   $retryStrategy       Optional retry strategy for handling failed requests; defaults to exponential backoff
     *
     * @throws InvalidArgumentException If the URL or language parameter is empty
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
        private readonly ?RetryStrategyInterface $retryStrategy = null,
    ) {
        if ('' === $url) {
            throw new InvalidArgumentException('URL is required and cannot be empty');
        }

        if ('' === $language) {
            throw new InvalidArgumentException('Language is required and cannot be empty');
        }
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If no last request is found
     * @throws InvalidArgumentException If request parameters are invalid
     * @throws LogicException           If HTTP service is not available
     * @throws ReflectionException      If exception creation fails
     */
    #[Override]
    public function assertLastRequest(): HttpRequestInterface
    {
        $httpService = $this->getHttpService();
        $lastRequest = $httpService->getLastRequest();

        if (! $lastRequest instanceof HttpRequestInterface) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(message: Messages::NO_LAST_REQUEST_FOUND, parameters: [], locale: $this->language, )]);
        }

        return $lastRequest;
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
        return $this->withLanguageContext(function () use ($store, $model, $checks) {
            $authorizationService = $this->getAuthorizationService();

            return $authorizationService->batchCheck(
                store: $store,
                model: $model,
                checks: $checks,
            );
        });
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
        return $this->withLanguageContext(function () use ($store, $model, $tupleKey, $trace, $context, $contextualTuples, $consistency) {
            $authorizationService = $this->getAuthorizationService();

            return $authorizationService->check(
                store: $store,
                model: $model,
                tupleKey: $tupleKey,
                trace: $trace,
                context: $context,
                contextualTuples: $contextualTuples,
                consistency: $consistency,
            );
        });
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
            $modelService = $this->getModelServiceForStore($store);

            return $modelService->createModel(
                typeDefinitions: $typeDefinitions,
                conditions: $conditions,
                schemaVersion: $schemaVersion,
            );
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
            $storeService = $this->getStoreService();

            return $storeService->createStore(
                name: $name,
            );
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
            $storeService = $this->getStoreService();
            $storeId = self::getStoreId($store);

            return $storeService->deleteStore(
                storeId: $storeId,
            );
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
                $validator = $this->getSchemaValidator();

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

                return new Success(value: Transformer::fromDsl(
                    dsl: $dsl,
                    validator: $validator,
                ));
            } catch (Throwable $throwable) {
                return new Failure(error: $throwable);
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
            $authorizationService = $this->getAuthorizationService();

            return $authorizationService->expand(
                store: $store,
                tupleKey: $tupleKey,
                model: $model,
                contextualTuples: $contextualTuples,
                consistency: $consistency,
            );
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
            $modelService = $this->getModelServiceForStore($store);
            $modelId = self::getModelId($model);

            return $modelService->findModel(
                modelId: $modelId,
            );
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
     *
     * @throws LogicException If HTTP service is not available
     */
    #[Override]
    public function getLastRequest(): ?HttpRequestInterface
    {
        $httpService = $this->getHttpService();

        return $httpService->getLastRequest();
    }

    /**
     * @inheritDoc
     *
     * @throws LogicException If HTTP service is not available
     */
    #[Override]
    public function getLastResponse(): ?HttpResponseInterface
    {
        $httpService = $this->getHttpService();

        return $httpService->getLastResponse();
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
            $storeService = $this->getStoreService();
            $storeId = self::getStoreId($store);

            return $storeService->findStore(
                storeId: $storeId,
            );
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
            if (null !== $pageSize && 0 >= $pageSize) {
                throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_PAGE_SIZE_INVALID, ['className' => 'Client::listAuthorizationModels'])]);
            }

            $modelService = $this->getModelServiceForStore($store);

            return $modelService->listAllModels(
                continuationToken: $continuationToken,
                pageSize: $pageSize,
            );
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
            $authorizationService = $this->getAuthorizationService();

            return $authorizationService->listObjects(
                store: $store,
                model: $model,
                type: $type,
                relation: $relation,
                user: $user,
                context: $context,
                contextualTuples: $contextualTuples,
                consistency: $consistency,
            );
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
            $storeService = $this->getStoreService();

            // Always use the pagination-aware method that returns ListStoresResponse
            return $storeService->listStores(
                continuationToken: $continuationToken,
                pageSize: $pageSize,
            );
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
            $tupleService = $this->getTupleServiceForStore($store);

            return $tupleService->listChanges(
                store: $store,
                type: $type,
                startTime: $startTime,
                continuationToken: $continuationToken,
                pageSize: $pageSize,
            );
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
            $authorizationService = $this->getAuthorizationService();

            return $authorizationService->listUsers(
                store: $store,
                model: $model,
                object: $object,
                relation: $relation,
                userFilters: $userFilters,
                context: $context,
                contextualTuples: $contextualTuples,
                consistency: $consistency,
            );
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
            $assertionService = $this->getAssertionServiceForStore($store);
            $modelId = self::getModelId($model);

            return $assertionService->readAssertions(
                authorizationModelId: $modelId,
            );
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
            $tupleService = $this->getTupleServiceForStore($store);

            return $tupleService->read(
                store: $store,
                tupleKey: $tupleKey,
                continuationToken: $continuationToken,
                pageSize: $pageSize,
                consistency: $consistency,
            );
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
            $authorizationService = $this->getAuthorizationService();

            return $authorizationService->streamedListObjects(
                store: $store,
                model: $model,
                type: $type,
                relation: $relation,
                user: $user,
                context: $context,
                contextualTuples: $contextualTuples,
                consistency: $consistency,
            );
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
            $assertionService = $this->getAssertionServiceForStore($store);
            $modelId = self::getModelId($model);

            return $assertionService->writeAssertions(
                authorizationModelId: $modelId,
                assertions: $assertions,
            );
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
        bool $transactional = true,
        int $maxParallelRequests = 1,
        int $maxTuplesPerChunk = 100,
        int $maxRetries = 0,
        float $retryDelaySeconds = 1.0,
        bool $stopOnFirstError = false,
    ): FailureInterface | SuccessInterface {
        return $this->withLanguageContext(function () use ($store, $model, $writes, $deletes, $transactional, $maxParallelRequests, $maxTuplesPerChunk, $maxRetries, $retryDelaySeconds, $stopOnFirstError) {
            $tupleService = $this->getTupleServiceForStore($store);

            return $tupleService->writeBatch(
                store: $store,
                model: $model,
                writes: $writes,
                deletes: $deletes,
                transactional: $transactional,
                maxParallelRequests: $maxParallelRequests,
                maxTuplesPerChunk: $maxTuplesPerChunk,
                maxRetries: $maxRetries,
                retryDelaySeconds: $retryDelaySeconds,
                stopOnFirstError: $stopOnFirstError,
            );
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
     * Create authentication service.
     *
     * @throws LogicException If required services are not available
     */
    private function createAuthenticationService(): AuthenticationService
    {
        $telemetryService = $this->getTelemetryService();

        return new AuthenticationService(
            $this->authentication,
            $telemetryService,
        );
    }

    /**
     * Create authorization service.
     *
     * @throws LogicException If required services are not available
     */
    private function createAuthorizationService(): AuthorizationService
    {
        $httpService = $this->getHttpService();

        return new AuthorizationService($httpService);
    }

    /**
     * Create HTTP service.
     *
     * @throws LogicException If required services are not available
     */
    private function createHttpService(): HttpService
    {
        $eventDispatcher = $this->getEventDispatcher();
        $authenticationService = $this->getAuthenticationService();

        $requestManager = new RequestManager(
            url: $this->url,
            maxRetries: max(0, min($this->httpMaxRetries ?? 3, 15)),
            authorizationHeader: null, // Authorization header will be resolved dynamically via authenticationService
            httpClient: $this->httpClient,
            httpResponseFactory: $this->httpResponseFactory,
            httpStreamFactory: $this->httpStreamFactory,
            httpRequestFactory: $this->httpRequestFactory,
            telemetry: $this->telemetry ?? new NoOpTelemetryProvider,
            retryStrategy: $this->retryStrategy,
            authenticationService: $authenticationService,
        );

        return new HttpService(
            $requestManager,
            $eventDispatcher,
        );
    }

    /**
     * Create store repository.
     *
     * @throws LogicException If required services are not available
     */
    private function createStoreRepository(): HttpStoreRepository
    {
        $httpService = $this->getHttpService();
        $validator = $this->getSchemaValidator();

        return new HttpStoreRepository($httpService, $validator);
    }

    /**
     * Create store service.
     *
     * @throws LogicException If required services are not available
     */
    private function createStoreService(): StoreService
    {
        $storeRepository = $this->getStoreRepository();

        return new StoreService($storeRepository);
    }

    /**
     * Create telemetry service with event listeners.
     *
     * @throws LogicException If required services are not available
     */
    private function createTelemetryService(): TelemetryServiceInterface
    {
        $eventDispatcher = $this->getEventDispatcher();
        $telemetryListener = $this->getTelemetryListener();

        // Register telemetry event listeners
        $eventDispatcher->addListener(
            'OpenFGA\\Events\\HttpRequestSentEvent',
            static function (object $event) use ($telemetryListener): void {
                if ($event instanceof HttpRequestSentEvent) {
                    $telemetryListener->onHttpRequestSent($event);
                }
            },
        );
        $eventDispatcher->addListener(
            'OpenFGA\\Events\\HttpResponseReceivedEvent',
            static function (object $event) use ($telemetryListener): void {
                if ($event instanceof HttpResponseReceivedEvent) {
                    $telemetryListener->onHttpResponseReceived($event);
                }
            },
        );
        $eventDispatcher->addListener(
            'OpenFGA\\Events\\OperationStartedEvent',
            static function (object $event) use ($telemetryListener): void {
                if ($event instanceof OperationStartedEvent) {
                    $telemetryListener->onOperationStarted($event);
                }
            },
        );
        $eventDispatcher->addListener(
            'OpenFGA\\Events\\OperationCompletedEvent',
            static function (object $event) use ($telemetryListener): void {
                if ($event instanceof OperationCompletedEvent) {
                    $telemetryListener->onOperationCompleted($event);
                }
            },
        );

        return new EventAwareTelemetryService(
            $this->telemetry ?? new NoOpTelemetryProvider,
            $eventDispatcher,
        );
    }

    /**
     * Create tuple repository.
     *
     * @throws LogicException If required services are not available
     */
    private function createTupleRepository(): HttpTupleRepository
    {
        $httpService = $this->getHttpService();
        $tupleFilterService = $this->getTupleFilterService();
        $validator = $this->getSchemaValidator();

        return new HttpTupleRepository($httpService, $tupleFilterService, $validator);
    }

    /**
     * Get an assertion service for the specified store.
     *
     * @param StoreInterface|string $store The store to get the service for
     *
     * @throws LogicException If the service is not available
     */
    private function getAssertionServiceForStore(StoreInterface | string $store): AssertionService
    {
        $storeId = self::getStoreId($store);
        $serviceId = 'service.assertion.' . $storeId;

        // Check if store-specific service exists
        if (isset($this->storeSpecificServices[$serviceId])) {
            /** @var AssertionService $service */
            $service = $this->storeSpecificServices[$serviceId];

            if (! $service instanceof AssertionService) {
                throw new LogicException('Assertion service not available');
            }

            return $service;
        }

        // Create and register store-specific service
        $httpService = $this->getHttpService();
        $validator = $this->getSchemaValidator();
        $assertionRepository = new HttpAssertionRepository(
            httpService: $httpService,
            validator: $validator,
            storeId: $storeId,
        );
        $service = new AssertionService(
            assertionRepository: $assertionRepository,
            language: $this->language,
        );

        $this->setStoreSpecificService($serviceId, $service);

        return $service;
    }

    /**
     * Get the authentication service instance.
     *
     * @throws LogicException If required services are not available
     */
    private function getAuthenticationService(): AuthenticationServiceInterface
    {
        return $this->authenticationService ??= $this->createAuthenticationService();
    }

    /**
     * Get the authorization service instance.
     *
     * @throws LogicException If the service is not available
     */
    private function getAuthorizationService(): AuthorizationServiceInterface
    {
        return $this->authorizationService ??= $this->createAuthorizationService();
    }

    /**
     * Get the event dispatcher instance.
     */
    private function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher ??= new EventDispatcher;
    }

    /**
     * Get the HTTP service instance.
     *
     * @throws LogicException If the service is not available
     */
    private function getHttpService(): HttpServiceInterface
    {
        return $this->httpService ??= $this->createHttpService();
    }

    /**
     * Get a model service for the specified store.
     *
     * @param StoreInterface|string $store The store to get the service for
     *
     * @throws ClientThrowable     If repository creation fails
     * @throws LogicException      If the service is not available
     * @throws ReflectionException If repository creation fails
     */
    private function getModelServiceForStore(StoreInterface | string $store): ModelService
    {
        $storeId = self::getStoreId($store);
        $serviceId = 'service.model.' . $storeId;

        // Check if store-specific service exists
        if (isset($this->storeSpecificServices[$serviceId])) {
            /** @var ModelService $service */
            $service = $this->storeSpecificServices[$serviceId];

            if (! $service instanceof ModelService) {
                throw new LogicException('Model service not available');
            }

            return $service;
        }

        // Create and register store-specific service
        $httpService = $this->getHttpService();
        $validator = $this->getSchemaValidator();
        $modelRepository = new HttpModelRepository(
            httpService: $httpService,
            validator: $validator,
            storeId: $storeId,
        );
        $service = new ModelService(
            modelRepository: $modelRepository,
            language: $this->language,
        );

        $this->setStoreSpecificService($serviceId, $service);

        return $service;
    }

    /**
     * Get the schema validator instance.
     */
    private function getSchemaValidator(): SchemaValidatorInterface
    {
        return $this->schemaValidator ??= new SchemaValidator;
    }

    /**
     * Get the store repository instance.
     *
     * @throws LogicException If required services are not available
     */
    private function getStoreRepository(): StoreRepositoryInterface
    {
        return $this->storeRepository ??= $this->createStoreRepository();
    }

    /**
     * Get the store service instance.
     *
     * @throws LogicException If the service is not available
     */
    private function getStoreService(): StoreServiceInterface
    {
        return $this->storeService ??= $this->createStoreService();
    }

    /**
     * Get the telemetry listener instance.
     */
    private function getTelemetryListener(): TelemetryEventListenerInterface
    {
        return $this->telemetryListener ??= new TelemetryEventListener($this->telemetry ?? new NoOpTelemetryProvider);
    }

    /**
     * Get the telemetry service instance.
     *
     * @throws LogicException If required services are not available
     */
    private function getTelemetryService(): TelemetryServiceInterface
    {
        return $this->telemetryService ??= $this->createTelemetryService();
    }

    /**
     * Get the tuple filter service instance.
     */
    private function getTupleFilterService(): TupleFilterServiceInterface
    {
        return $this->tupleFilterService ??= new TupleFilterService;
    }

    /**
     * Get the tuple repository instance.
     *
     * @throws LogicException If required services are not available
     */
    private function getTupleRepository(): TupleRepositoryInterface
    {
        return $this->tupleRepository ??= $this->createTupleRepository();
    }

    /**
     * Get a tuple service for the specified store.
     *
     * @param StoreInterface|string $store The store to get the service for
     *
     * @throws LogicException If the service is not available
     */
    private function getTupleServiceForStore(StoreInterface | string $store): TupleService
    {
        $storeId = self::getStoreId($store);
        $serviceId = 'service.tuple.' . $storeId;

        // Check if store-specific service exists
        if (isset($this->storeSpecificServices[$serviceId])) {
            /** @var TupleService $service */
            $service = $this->storeSpecificServices[$serviceId];

            if (! $service instanceof TupleService) {
                throw new LogicException('Tuple service not available');
            }

            return $service;
        }

        // Create and register store-specific service
        $tupleRepository = $this->getTupleRepository();

        $service = new TupleService(
            tupleRepository: $tupleRepository,
        );
        $this->setStoreSpecificService($serviceId, $service);

        return $service;
    }

    /**
     * Set a store-specific service instance.
     *
     * @param string $serviceId
     * @param object $service
     */
    private function setStoreSpecificService(string $serviceId, object $service): void
    {
        $this->storeSpecificServices[$serviceId] = $service;
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
     * @param callable(): T $callback The callback to execute with the language context
     *
     * @throws Throwable Any exception thrown by the callback is re-thrown after cleanup
     *
     * @return T The result of the callback
     */
    private function withLanguageContext(callable $callback)
    {
        $originalLocale = Translator::getDefaultLocale();

        try {
            Translator::setDefaultLocale(locale: $this->language);

            return $callback();
        } finally {
            Translator::setDefaultLocale(locale: $originalLocale);
        }
    }
}
