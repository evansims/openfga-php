<?php

declare(strict_types=1);

namespace OpenFGA;

use DateTimeImmutable;
use InvalidArgumentException;
use LogicException;
use OpenFGA\Authentication\AuthenticationInterface;
use OpenFGA\DI\{ServiceProvider, ServiceProviderInterface};
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Language\Transformer;
use OpenFGA\Models\{AuthorizationModel, DifferenceV1, Metadata, ObjectRelation, RelationMetadata, RelationReference, SourceInfo, TupleToUsersetV1, TypeDefinition, Userset};
use OpenFGA\Models\{AuthorizationModelInterface, StoreInterface, TupleKeyInterface};
use OpenFGA\Models\Collections\{AssertionsInterface, BatchCheckItemsInterface, ConditionsInterface, TupleKeysInterface, TypeDefinitionsInterface, UserTypeFiltersInterface};
use OpenFGA\Models\Collections\{Conditions, RelationMetadataCollection, RelationReferences, TypeDefinitionRelations, TypeDefinitions, Usersets};
use OpenFGA\Models\Enums\{Consistency, SchemaVersion};
use OpenFGA\Network\RetryStrategyInterface;
use OpenFGA\Observability\TelemetryInterface;
use OpenFGA\Repositories\{HttpAssertionRepository, HttpModelRepository, HttpTupleRepository};
use OpenFGA\Results\{Failure, Success};
use OpenFGA\Results\{FailureInterface, SuccessInterface};
use OpenFGA\Schemas\SchemaValidator;
use OpenFGA\Services\{AssertionService, ModelService, TupleService};
use OpenFGA\Services\{AuthorizationService, HttpService, StoreService};
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
final readonly class Client implements ClientInterface
{
    /**
     * The version of the OpenFGA PHP SDK.
     */
    public const string VERSION = '1.3.0';

    /**
     * Service provider for dependency injection.
     */
    private ServiceProviderInterface $serviceProvider;

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
     * @param RetryStrategyInterface|null   $retryStrategy       Optional retry strategy for handling failed requests; defaults to exponential backoff
     *
     * @see https://openfga.dev/docs/getting-started/setup-sdk-client Client configuration guide
     */
    public function __construct(
        private string $url,
        private ?AuthenticationInterface $authentication = null,
        private string $language = 'en',
        private ?int $httpMaxRetries = 3,
        private ?HttpClientInterface $httpClient = null,
        private ?ResponseFactoryInterface $httpResponseFactory = null,
        private ?StreamFactoryInterface $httpStreamFactory = null,
        private ?RequestFactoryInterface $httpRequestFactory = null,
        private ?TelemetryInterface $telemetry = null,
        private ?RetryStrategyInterface $retryStrategy = null,
    ) {
        $this->serviceProvider = new ServiceProvider(
            url: $this->url,
            storeId: null,
            authentication: $this->authentication,
            telemetry: $this->telemetry,
            language: $this->language,
            maxRetries: $this->httpMaxRetries ?? 3,
            httpClient: $this->httpClient,
            requestFactory: $this->httpRequestFactory,
            responseFactory: $this->httpResponseFactory,
            streamFactory: $this->httpStreamFactory,
            retryStrategy: $this->retryStrategy,
        );
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
                store: $store,
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
            $storeId = self::getStoreId($store);
            $modelId = self::getModelId($model);

            return $modelService->findModel(
                store: $storeId,
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
        return $this->withLanguageContext(function () use ($store, $pageSize) {
            $modelService = $this->getModelServiceForStore($store);
            $storeId = self::getStoreId($store);

            return $modelService->listAllModels(
                store: $storeId,
                maxItems: $pageSize,
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
            $storeId = self::getStoreId($store);
            $modelId = self::getModelId($model);

            return $assertionService->readAssertions(
                store: $storeId,
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
            $storeId = self::getStoreId($store);
            $modelId = self::getModelId($model);

            return $assertionService->writeAssertions(
                store: $storeId,
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
        if ($this->serviceProvider->has(serviceId: $serviceId)) {
            /** @var AssertionService $service */
            $service = $this->serviceProvider->get(serviceId: $serviceId);

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

        $this->serviceProvider->set(
            serviceId: $serviceId,
            service: $service,
        );

        return $service;
    }

    /**
     * Get the authorization service from the service provider.
     *
     * @throws LogicException If the service is not available
     */
    private function getAuthorizationService(): AuthorizationService
    {
        /** @var AuthorizationService $service */
        $service = $this->serviceProvider->get(serviceId: 'service.authorization');

        if (! $service instanceof AuthorizationService) {
            throw new LogicException('Authorization service not available');
        }

        return $service;
    }

    /**
     * Get the HTTP service from the service provider.
     *
     * @throws LogicException If the service is not available
     */
    private function getHttpService(): HttpService
    {
        /** @var HttpService $service */
        $service = $this->serviceProvider->get(serviceId: 'http');

        if (! $service instanceof HttpService) {
            throw new LogicException('HTTP service not available');
        }

        return $service;
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
        if ($this->serviceProvider->has(serviceId: $serviceId)) {
            /** @var ModelService $service */
            $service = $this->serviceProvider->get(serviceId: $serviceId);

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
            httpService: $httpService,
            validator: $validator,
            language: $this->language,
        );

        $this->serviceProvider->set(
            serviceId: $serviceId,
            service: $service,
        );

        return $service;
    }

    /**
     * Get the schema validator from the service provider.
     *
     * @throws LogicException If the service is not available
     */
    private function getSchemaValidator(): SchemaValidator
    {
        /** @var SchemaValidator $validator */
        $validator = $this->serviceProvider->get(serviceId: 'schema.validator');

        if (! $validator instanceof SchemaValidator) {
            throw new LogicException('Schema validator not available');
        }

        return $validator;
    }

    /**
     * Get the store service from the service provider.
     *
     * @throws LogicException If the service is not available
     */
    private function getStoreService(): StoreService
    {
        /** @var StoreService $service */
        $service = $this->serviceProvider->get(serviceId: 'service.store');

        if (! $service instanceof StoreService) {
            throw new LogicException('Store service not available');
        }

        return $service;
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
        if ($this->serviceProvider->has(serviceId: $serviceId)) {
            /** @var TupleService $service */
            $service = $this->serviceProvider->get(serviceId: $serviceId);

            if (! $service instanceof TupleService) {
                throw new LogicException('Tuple service not available');
            }

            return $service;
        }

        // Create and register store-specific service
        $tupleRepository = $this->serviceProvider->get(serviceId: 'repository.tuple');

        if (! $tupleRepository instanceof HttpTupleRepository) {
            throw new LogicException('Tuple repository not available');
        }

        $service = new TupleService(
            tupleRepository: $tupleRepository,
        );
        $this->serviceProvider->set(
            serviceId: $serviceId,
            service: $service,
        );

        return $service;
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
