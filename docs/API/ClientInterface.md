# ClientInterface

OpenFGA Client Interface for relationship-based access control operations. This interface defines the complete API for interacting with OpenFGA services, providing methods for managing stores, authorization models, relationship tuples, and performing authorization checks. The client implements the Result pattern, returning Success or Failure objects instead of throwing exceptions. All operations support OpenFGA&#039;s core concepts including stores for data isolation, authorization models for defining permission structures, and relationship tuples for expressing user-object relationships.

## Namespace
`OpenFGA`




## Methods
### assertLastRequest


```php
public function assertLastRequest(): HttpRequestInterface
```

Retrieves the last HTTP request made by the client.


#### Returns
HttpRequestInterface
 The last request

### check


```php
public function check(StoreInterface|string $store, AuthorizationModelInterface|string $model, TupleKeyInterface $tupleKey, bool|null $trace = NULL, object|null $context = NULL, TupleKeysInterface<TupleKeyInterface>|null $contextualTuples = NULL, Consistency|null $consistency = NULL): Failure<Throwable>|Success<CheckResponse>
```

Checks if a user has a specific relationship with an object. Performs an authorization check to determine if a user has a particular relationship with an object based on the configured authorization model. This is the core operation for making authorization decisions in OpenFGA.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface | string | The store to check against |
| `$model` | AuthorizationModelInterface | string | The authorization model to use |
| `$tupleKey` | TupleKeyInterface | The relationship to check |
| `$trace` | bool | null | Whether to include a trace in the response |
| `$context` | object | null | Additional context for the check |
| `$contextualTuples` | TupleKeysInterface&lt;TupleKeyInterface&gt; | null | Additional tuples for contextual evaluation |
| `$consistency` | Consistency | null | Override the default consistency level |

#### Returns
Failure&lt;Throwable&gt; | Success&lt;CheckResponse&gt;
 The result of the check request

### createAuthorizationModel


```php
public function createAuthorizationModel(StoreInterface|string $store, TypeDefinitionsInterface<TypeDefinitionInterface> $typeDefinitions, ConditionsInterface<ConditionInterface>|null $conditions = NULL, SchemaVersion $schemaVersion = OpenFGA\Models\Enums\SchemaVersion::V1_1): Failure<Throwable>|Success<CreateAuthorizationModelResponse>
```

Creates a new authorization model with the given type definitions and conditions. Authorization models define the permission structure for your application, including object types, relationships, and how permissions are computed. Models are immutable once created and identified by a unique ID.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface | string | The store to create the model in |
| `$typeDefinitions` | TypeDefinitionsInterface&lt;TypeDefinitionInterface&gt; | The type definitions for the model |
| `$conditions` | ConditionsInterface&lt;ConditionInterface&gt; | null | The conditions for the model |
| `$schemaVersion` | SchemaVersion | The schema version to use (default: 1.1) |

#### Returns
Failure&lt;Throwable&gt; | Success&lt;CreateAuthorizationModelResponse&gt;
 The result of the authorization model creation request

### createStore


```php
public function createStore(string $name): Failure<Throwable>|Success<CreateStoreResponse>
```

Creates a new store with the given name. Stores provide data isolation for different applications or environments. Each store maintains its own authorization models, relationship tuples, and provides complete separation from other stores.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | string | The name for the new store |

#### Returns
Failure&lt;Throwable&gt; | Success&lt;CreateStoreResponse&gt;
 The result of the store creation request

### deleteStore


```php
public function deleteStore(StoreInterface|string $store): Failure<Throwable>|Success<DeleteStoreResponse>
```

Deletes a store.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface | string | The store to delete |

#### Returns
Failure&lt;Throwable&gt; | Success&lt;DeleteStoreResponse&gt;
 The result of the store deletion request

### dsl


```php
public function dsl(string $dsl): Failure<Throwable>|Success<AuthorizationModelInterface>
```

Parses a DSL string and returns an AuthorizationModel. The Domain Specific Language (DSL) provides a human-readable way to define authorization models using intuitive syntax for relationships and permissions. This method converts DSL text into a structured authorization model object.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$dsl` | string | The DSL string to parse |

#### Returns
Failure&lt;Throwable&gt; | Success&lt;AuthorizationModelInterface&gt;
 The result of the DSL transformation request

### expand


```php
public function expand(StoreInterface|string $store, TupleKeyInterface $tupleKey, AuthorizationModelInterface|string|null $model = NULL, TupleKeysInterface<TupleKeyInterface>|null $contextualTuples = NULL, Consistency|null $consistency = NULL): Failure<Throwable>|Success<ExpandResponse>
```

Expands a relationship tuple to show all users that have the relationship.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface | string | The store containing the tuple |
| `$tupleKey` | TupleKeyInterface | The tuple to expand |
| `$model` | AuthorizationModelInterface | string | null | The authorization model to use |
| `$contextualTuples` | TupleKeysInterface&lt;TupleKeyInterface&gt; | null | Additional tuples for contextual evaluation |
| `$consistency` | Consistency | null | Override the default consistency level |

#### Returns
Failure&lt;Throwable&gt; | Success&lt;ExpandResponse&gt;
 The result of the expansion request

### getAuthorizationModel


```php
public function getAuthorizationModel(StoreInterface|string $store, AuthorizationModelInterface|string $model): Failure<Throwable>|Success<GetAuthorizationModelResponse>
```

Retrieves an authorization model by ID.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface | string | The store containing the model |
| `$model` | AuthorizationModelInterface | string | The model to retrieve |

#### Returns
Failure&lt;Throwable&gt; | Success&lt;GetAuthorizationModelResponse&gt;
 The result of the authorization model retrieval request

### getLastRequest


```php
public function getLastRequest(): ?Psr\Http\Message\RequestInterface
```

Retrieves the last HTTP request made by the client.


#### Returns
?Psr\Http\Message\RequestInterface

### getLastResponse


```php
public function getLastResponse(): ?Psr\Http\Message\ResponseInterface
```

Retrieves the last HTTP response received by the client.


#### Returns
?Psr\Http\Message\ResponseInterface

### getStore


```php
public function getStore(StoreInterface|string $store): Failure<Throwable>|Success<GetStoreResponse>
```

Retrieves store details by ID.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface | string | The store to retrieve |

#### Returns
Failure&lt;Throwable&gt; | Success&lt;GetStoreResponse&gt;
 The result of the store retrieval request

### listAuthorizationModels


```php
public function listAuthorizationModels(StoreInterface|string $store, string|null $continuationToken = NULL, ?int $pageSize = NULL): Failure<Throwable>|Success<ListAuthorizationModelsResponse>
```

Lists authorization models in a store with pagination.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface | string | The store to list models from |
| `$continuationToken` | string | null | Token for pagination |
| `$pageSize` | ?int |  |

#### Returns
Failure&lt;Throwable&gt; | Success&lt;ListAuthorizationModelsResponse&gt;
 The result of the authorization model listing request

### listObjects


```php
public function listObjects(StoreInterface|string $store, AuthorizationModelInterface|string $model, string $type, string $relation, string $user, object|null $context = NULL, TupleKeysInterface<TupleKeyInterface>|null $contextualTuples = NULL, Consistency|null $consistency = NULL): Failure<Throwable>|Success<ListObjectsResponse>
```

Lists objects that have a specific relationship with a user.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface | string | The store to query |
| `$model` | AuthorizationModelInterface | string | The authorization model to use |
| `$type` | string | The type of objects to list |
| `$relation` | string | The relationship to check |
| `$user` | string | The user to check relationships for |
| `$context` | object | null | Additional context for evaluation |
| `$contextualTuples` | TupleKeysInterface&lt;TupleKeyInterface&gt; | null | Additional tuples for contextual evaluation |
| `$consistency` | Consistency | null | Override the default consistency level |

#### Returns
Failure&lt;Throwable&gt; | Success&lt;ListObjectsResponse&gt;
 The result of the object listing request

### listStores


```php
public function listStores(string|null $continuationToken = NULL, ?int $pageSize = NULL): Failure<Throwable>|Success<ListStoresResponse>
```

Lists all stores with pagination.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$continuationToken` | string | null | Token for pagination |
| `$pageSize` | ?int |  |

#### Returns
Failure&lt;Throwable&gt; | Success&lt;ListStoresResponse&gt;
 The result of the store listing request

### listTupleChanges


```php
public function listTupleChanges(StoreInterface|string $store, string|null $continuationToken = NULL, ?int $pageSize = NULL, string|null $type = NULL, DateTimeImmutable|null $startTime = NULL): Failure<Throwable>|Success<ListTupleChangesResponse>
```

Lists changes to relationship tuples in a store.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface | string | The store to list changes for |
| `$continuationToken` | string | null | Token for pagination |
| `$pageSize` | ?int |  |
| `$type` | string | null | Filter changes by type |
| `$startTime` | DateTimeImmutable | null | Only include changes at or after this time (inclusive) |

#### Returns
Failure&lt;Throwable&gt; | Success&lt;ListTupleChangesResponse&gt;
 The result of the tuple change listing request

### listUsers


```php
public function listUsers(StoreInterface|string $store, AuthorizationModelInterface|string $model, string $object, string $relation, UserTypeFiltersInterface<UserTypeFilterInterface> $userFilters, object|null $context = NULL, TupleKeysInterface<TupleKeyInterface>|null $contextualTuples = NULL, Consistency|null $consistency = NULL): Failure<Throwable>|Success<ListUsersResponse>
```

Lists users that have a specific relationship with an object.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface | string | The store to query |
| `$model` | AuthorizationModelInterface | string | The authorization model to use |
| `$object` | string | The object to check relationships for |
| `$relation` | string | The relationship to check |
| `$userFilters` | UserTypeFiltersInterface&lt;UserTypeFilterInterface&gt; | Filters for user types to include |
| `$context` | object | null | Additional context for evaluation |
| `$contextualTuples` | TupleKeysInterface&lt;TupleKeyInterface&gt; | null | Additional tuples for contextual evaluation |
| `$consistency` | Consistency | null | Override the default consistency level |

#### Returns
Failure&lt;Throwable&gt; | Success&lt;ListUsersResponse&gt;
 The result of the user listing request

### readAssertions


```php
public function readAssertions(StoreInterface|string $store, AuthorizationModelInterface|string $model): Failure<Throwable>|Success<ReadAssertionsResponse>
```

Retrieves assertions for an authorization model.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface | string | The store containing the model |
| `$model` | AuthorizationModelInterface | string | The model to get assertions for |

#### Returns
Failure&lt;Throwable&gt; | Success&lt;ReadAssertionsResponse&gt;
 The result of the assertions read request

### readTuples


```php
public function readTuples(StoreInterface|string $store, TupleKeyInterface $tupleKey, string|null $continuationToken = NULL, ?int $pageSize = NULL, Consistency|null $consistency = NULL): Failure<Throwable>|Success<ReadTuplesResponse>
```

Reads relationship tuples from a store with optional filtering and pagination.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface | string | The store to read from |
| `$tupleKey` | TupleKeyInterface | Filter tuples by this key (return all if null) |
| `$continuationToken` | string | null | Token for pagination |
| `$pageSize` | ?int |  |
| `$consistency` | Consistency | null | Override the default consistency level |

#### Returns
Failure&lt;Throwable&gt; | Success&lt;ReadTuplesResponse&gt;
 The result of the tuple read request

### writeAssertions


```php
public function writeAssertions(StoreInterface|string $store, AuthorizationModelInterface|string $model, AssertionsInterface<AssertionInterface> $assertions): Failure<Throwable>|Success<WriteAssertionsResponse>
```

Creates or updates assertions for an authorization model.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface | string | The store containing the model |
| `$model` | AuthorizationModelInterface | string | The model to update assertions for |
| `$assertions` | AssertionsInterface&lt;AssertionInterface&gt; | The assertions to upsert |

#### Returns
Failure&lt;Throwable&gt; | Success&lt;WriteAssertionsResponse&gt;
 The result of the assertion write request

### writeTuples


```php
public function writeTuples(StoreInterface|string $store, AuthorizationModelInterface|string $model, TupleKeysInterface<TupleKeyInterface>|null $writes = NULL, TupleKeysInterface<TupleKeyInterface>|null $deletes = NULL): Failure<Throwable>|Success<WriteTuplesResponse>
```

Writes or deletes relationship tuples in a store.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface | string | The store to modify |
| `$model` | AuthorizationModelInterface | string | The authorization model to use |
| `$writes` | TupleKeysInterface&lt;TupleKeyInterface&gt; | null | Tuples to write (create or update) |
| `$deletes` | TupleKeysInterface&lt;TupleKeyInterface&gt; | null | Tuples to delete |

#### Returns
Failure&lt;Throwable&gt; | Success&lt;WriteTuplesResponse&gt;
 The result of the tuple write request

