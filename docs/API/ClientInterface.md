# ClientInterface

OpenFGA Client Interface for relationship-based access control operations. This interface defines the complete API for interacting with OpenFGA services, providing methods for managing stores, authorization models, relationship tuples, and performing authorization checks. The client implements the Result pattern, returning Success or Failure objects instead of throwing exceptions. All operations support OpenFGA&#039;s core concepts including stores for data isolation, authorization models for defining permission structures, and relationship tuples for expressing user-object relationships.

## Namespace
`OpenFGA`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php)




## Methods
### assertLastRequest


```php
public function assertLastRequest(): HttpRequestInterface
```

Retrieves the last HTTP request made by the client.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L42)


#### Returns
HttpRequestInterface
 The last request

### batchCheck


```php
public function batchCheck(StoreInterface|string $store, AuthorizationModelInterface|string $model, BatchCheckItemsInterface $checks): FailureInterface|SuccessInterface
```

Performs multiple authorization checks in a single batch request. This method allows checking multiple user-object relationships simultaneously for better performance when multiple authorization decisions are needed. Each check in the batch has a correlation ID to map results back to the original requests. The batch check operation supports the same features as individual checks: contextual tuples, custom contexts, and detailed error information for each check.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L66)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to check against |
| `$model` | AuthorizationModelInterface&#124;string | The authorization model to use |
| `$checks` | BatchCheckItemsInterface | The batch check items |

#### Returns
FailureInterface&#124;SuccessInterface
 The batch check results

### check


```php
public function check(StoreInterface|string $store, AuthorizationModelInterface|string $model, TupleKeyInterface $tupleKey, bool|null $trace = NULL, object|null $context = NULL, TupleKeysInterface<TupleKeyInterface>|null $contextualTuples = NULL, Consistency|null $consistency = NULL): FailureInterface|SuccessInterface
```

Checks if a user has a specific relationship with an object. Performs an authorization check to determine if a user has a particular relationship with an object based on the configured authorization model. This is the core operation for making authorization decisions in OpenFGA.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L90)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to check against |
| `$model` | AuthorizationModelInterface&#124;string | The authorization model to use |
| `$tupleKey` | TupleKeyInterface | The relationship to check |
| `$trace` | bool&#124;null | Whether to include a trace in the response |
| `$context` | object&#124;null | Additional context for the check |
| `$contextualTuples` | TupleKeysInterface&lt;TupleKeyInterface&gt;&#124;null | Additional tuples for contextual evaluation |
| `$consistency` | Consistency&#124;null | Override the default consistency level |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains CheckResponseInterface, Failure contains Throwable

### createAuthorizationModel


```php
public function createAuthorizationModel(StoreInterface|string $store, TypeDefinitionsInterface<TypeDefinitionInterface> $typeDefinitions, ConditionsInterface<ConditionInterface>|null $conditions = NULL, SchemaVersion $schemaVersion = OpenFGA\Models\Enums\SchemaVersion::V1_1): FailureInterface|SuccessInterface
```

Creates a new authorization model with the given type definitions and conditions. Authorization models define the permission structure for your application, including object types, relationships, and how permissions are computed. Models are immutable once created and identified by a unique ID.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L116)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to create the model in |
| `$typeDefinitions` | TypeDefinitionsInterface&lt;TypeDefinitionInterface&gt; | The type definitions for the model |
| `$conditions` | ConditionsInterface&lt;ConditionInterface&gt;&#124;null | The conditions for the model |
| `$schemaVersion` | SchemaVersion | The schema version to use (default: 1.1) |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains CreateAuthorizationModelResponseInterface, Failure contains Throwable

### createStore


```php
public function createStore(string $name): FailureInterface|SuccessInterface
```

Creates a new store with the given name. Stores provide data isolation for different applications or environments. Each store maintains its own authorization models, relationship tuples, and provides complete separation from other stores.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L135)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | string | The name for the new store |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains CreateStoreResponseInterface, Failure contains Throwable

### deleteStore


```php
public function deleteStore(StoreInterface|string $store): FailureInterface|SuccessInterface
```

Deletes a store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L145)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to delete |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains DeleteStoreResponseInterface, Failure contains Throwable

### dsl


```php
public function dsl(string $dsl): FailureInterface|SuccessInterface
```

Parses a DSL string and returns an AuthorizationModel. The Domain Specific Language (DSL) provides a human-readable way to define authorization models using intuitive syntax for relationships and permissions. This method converts DSL text into a structured authorization model object.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L164)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$dsl` | string | The DSL string to parse |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains AuthorizationModelInterface, Failure contains Throwable

### expand


```php
public function expand(StoreInterface|string $store, TupleKeyInterface $tupleKey, AuthorizationModelInterface|string|null $model = NULL, TupleKeysInterface<TupleKeyInterface>|null $contextualTuples = NULL, Consistency|null $consistency = NULL): FailureInterface|SuccessInterface
```

Expands a relationship tuple to show all users that have the relationship.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L176)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store containing the tuple |
| `$tupleKey` | TupleKeyInterface | The tuple to expand |
| `$model` | AuthorizationModelInterface&#124;string&#124;null | The authorization model to use |
| `$contextualTuples` | TupleKeysInterface&lt;TupleKeyInterface&gt;&#124;null | Additional tuples for contextual evaluation |
| `$consistency` | Consistency&#124;null | Override the default consistency level |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains ExpandResponseInterface, Failure contains Throwable

### getAuthorizationModel


```php
public function getAuthorizationModel(StoreInterface|string $store, AuthorizationModelInterface|string $model): FailureInterface|SuccessInterface
```

Retrieves an authorization model by ID.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L191)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store containing the model |
| `$model` | AuthorizationModelInterface&#124;string | The model to retrieve |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains GetAuthorizationModelResponseInterface, Failure contains Throwable

### getLastRequest


```php
public function getLastRequest(): ?Psr\Http\Message\RequestInterface
```

Retrieves the last HTTP request made by the client.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L201)


#### Returns
?Psr\Http\Message\RequestInterface

### getLastResponse


```php
public function getLastResponse(): ?Psr\Http\Message\ResponseInterface
```

Retrieves the last HTTP response received by the client.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L208)


#### Returns
?Psr\Http\Message\ResponseInterface

### getStore


```php
public function getStore(StoreInterface|string $store): FailureInterface|SuccessInterface
```

Retrieves store details by ID.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L216)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to retrieve |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains GetStoreResponseInterface, Failure contains Throwable

### listAuthorizationModels


```php
public function listAuthorizationModels(StoreInterface|string $store, string|null $continuationToken = NULL, ?int $pageSize = NULL): FailureInterface|SuccessInterface
```

Lists authorization models in a store with pagination.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L231)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to list models from |
| `$continuationToken` | string&#124;null | Token for pagination |
| `$pageSize` | ?int |  |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains ListAuthorizationModelsResponseInterface, Failure contains Throwable

### listObjects


```php
public function listObjects(StoreInterface|string $store, AuthorizationModelInterface|string $model, string $type, string $relation, string $user, object|null $context = NULL, TupleKeysInterface<TupleKeyInterface>|null $contextualTuples = NULL, Consistency|null $consistency = NULL): FailureInterface|SuccessInterface
```

Lists objects that have a specific relationship with a user.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L250)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to query |
| `$model` | AuthorizationModelInterface&#124;string | The authorization model to use |
| `$type` | string | The type of objects to list |
| `$relation` | string | The relationship to check |
| `$user` | string | The user to check relationships for |
| `$context` | object&#124;null | Additional context for evaluation |
| `$contextualTuples` | TupleKeysInterface&lt;TupleKeyInterface&gt;&#124;null | Additional tuples for contextual evaluation |
| `$consistency` | Consistency&#124;null | Override the default consistency level |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains ListObjectsResponseInterface, Failure contains Throwable

### listStores


```php
public function listStores(string|null $continuationToken = NULL, ?int $pageSize = NULL): FailureInterface|SuccessInterface
```

Lists all stores with pagination.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L271)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$continuationToken` | string&#124;null | Token for pagination |
| `$pageSize` | ?int |  |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains ListStoresResponseInterface, Failure contains Throwable

### listTupleChanges


```php
public function listTupleChanges(StoreInterface|string $store, string|null $continuationToken = NULL, ?int $pageSize = NULL, string|null $type = NULL, DateTimeImmutable|null $startTime = NULL): FailureInterface|SuccessInterface
```

Lists changes to relationship tuples in a store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L289)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to list changes for |
| `$continuationToken` | string&#124;null | Token for pagination |
| `$pageSize` | ?int |  |
| `$type` | string&#124;null | Filter changes by type |
| `$startTime` | DateTimeImmutable&#124;null | Only include changes at or after this time (inclusive) |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains ListTupleChangesResponseInterface, Failure contains Throwable

### listUsers


```php
public function listUsers(StoreInterface|string $store, AuthorizationModelInterface|string $model, string $object, string $relation, UserTypeFiltersInterface<UserTypeFilterInterface> $userFilters, object|null $context = NULL, TupleKeysInterface<TupleKeyInterface>|null $contextualTuples = NULL, Consistency|null $consistency = NULL): FailureInterface|SuccessInterface
```

Lists users that have a specific relationship with an object.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L310)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to query |
| `$model` | AuthorizationModelInterface&#124;string | The authorization model to use |
| `$object` | string | The object to check relationships for |
| `$relation` | string | The relationship to check |
| `$userFilters` | UserTypeFiltersInterface&lt;UserTypeFilterInterface&gt; | Filters for user types to include |
| `$context` | object&#124;null | Additional context for evaluation |
| `$contextualTuples` | TupleKeysInterface&lt;TupleKeyInterface&gt;&#124;null | Additional tuples for contextual evaluation |
| `$consistency` | Consistency&#124;null | Override the default consistency level |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains ListUsersResponseInterface, Failure contains Throwable

### readAssertions


```php
public function readAssertions(StoreInterface|string $store, AuthorizationModelInterface|string $model): FailureInterface|SuccessInterface
```

Retrieves assertions for an authorization model.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L328)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store containing the model |
| `$model` | AuthorizationModelInterface&#124;string | The model to get assertions for |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains ReadAssertionsResponseInterface, Failure contains Throwable

### readTuples


```php
public function readTuples(StoreInterface|string $store, TupleKeyInterface $tupleKey, string|null $continuationToken = NULL, ?int $pageSize = NULL, Consistency|null $consistency = NULL): FailureInterface|SuccessInterface
```

Reads relationship tuples from a store with optional filtering and pagination.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L346)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to read from |
| `$tupleKey` | TupleKeyInterface | Filter tuples by this key (return all if null) |
| `$continuationToken` | string&#124;null | Token for pagination |
| `$pageSize` | ?int |  |
| `$consistency` | Consistency&#124;null | Override the default consistency level |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains ReadTuplesResponseInterface, Failure contains Throwable

### streamedListObjects


```php
public function streamedListObjects(StoreInterface|string $store, AuthorizationModelInterface|string $model, string $type, string $relation, string $user, object|null $context = NULL, TupleKeysInterface<TupleKeyInterface>|null $contextualTuples = NULL, Consistency|null $consistency = NULL): FailureInterface|SuccessInterface
```

Streams objects that a user has a specific relationship with. Returns all objects of a given type that the specified user has a relationship with, using a streaming response for memory-efficient processing of large result sets. This is ideal for handling thousands of objects without loading them all into memory.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L371)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to query |
| `$model` | AuthorizationModelInterface&#124;string | The authorization model to use |
| `$type` | string | The object type to find |
| `$relation` | string | The relationship to check |
| `$user` | string | The user to check relationships for |
| `$context` | object&#124;null | Additional context for evaluation |
| `$contextualTuples` | TupleKeysInterface&lt;TupleKeyInterface&gt;&#124;null | Additional tuples for contextual evaluation |
| `$consistency` | Consistency&#124;null | Override the default consistency level |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains Generator&lt;StreamedListObjectsResponseInterface&gt;, Failure contains Throwable

### writeAssertions


```php
public function writeAssertions(StoreInterface|string $store, AuthorizationModelInterface|string $model, AssertionsInterface<AssertionInterface> $assertions): FailureInterface|SuccessInterface
```

Creates or updates assertions for an authorization model.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L390)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store containing the model |
| `$model` | AuthorizationModelInterface&#124;string | The model to update assertions for |
| `$assertions` | AssertionsInterface&lt;AssertionInterface&gt; | The assertions to upsert |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains WriteAssertionsResponseInterface, Failure contains Throwable

### writeTuples


```php
public function writeTuples(StoreInterface|string $store, AuthorizationModelInterface|string $model, TupleKeysInterface<TupleKeyInterface>|null $writes = NULL, TupleKeysInterface<TupleKeyInterface>|null $deletes = NULL): FailureInterface|SuccessInterface
```

Writes or deletes relationship tuples in a store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/ClientInterface.php#L405)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | StoreInterface&#124;string | The store to modify |
| `$model` | AuthorizationModelInterface&#124;string | The authorization model to use |
| `$writes` | TupleKeysInterface&lt;TupleKeyInterface&gt;&#124;null | Tuples to write (create or update) |
| `$deletes` | TupleKeysInterface&lt;TupleKeyInterface&gt;&#124;null | Tuples to delete |

#### Returns
FailureInterface&#124;SuccessInterface
 Success contains WriteTuplesResponseInterface, Failure contains Throwable

