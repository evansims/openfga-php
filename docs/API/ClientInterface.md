# ClientInterface


## Namespace
`OpenFGA`




## Methods
### check


```php
public function check([StoreInterface](Models/StoreInterface.md) | string $store, [AuthorizationModelInterface](Models/AuthorizationModelInterface.md) | string $model, [TupleKeyInterface](Models/TupleKeyInterface.md) $tupleKey, ?bool $trace = NULL, ?object $context = NULL, ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) $contextualTuples = NULL, ?Consistency $consistency = NULL): [ResultInterface](Results/ResultInterface.md)
```

Checks if a user has a specific relationship with an object.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md) | string` | The store to check against |
| `$model` | `[AuthorizationModelInterface](Models/AuthorizationModelInterface.md) | string` | The authorization model to use |
| `$tupleKey` | `[TupleKeyInterface](Models/TupleKeyInterface.md)` | The relationship to check |
| `$trace` | `?bool` | Whether to include a trace in the response |
| `$context` | `?object` | Additional context for the check |
| `$contextualTuples` | `?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)` | Additional tuples for contextual evaluation |
| `$consistency` | `?Consistency` | Override the default consistency level |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
 Throwable&gt; The result of the check request

### createAuthorizationModel


```php
public function createAuthorizationModel([StoreInterface](Models/StoreInterface.md) | string $store, [TypeDefinitionsInterface](Models/Collections/TypeDefinitionsInterface.md) $typeDefinitions, [ConditionsInterface](Models/Collections/ConditionsInterface.md) $conditions, SchemaVersion $schemaVersion = SchemaVersion::V1_1): [ResultInterface](Results/ResultInterface.md)
```

Creates a new authorization model with the given type definitions and conditions.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md) | string` | The store to create the model in |
| `$typeDefinitions` | `[TypeDefinitionsInterface](Models/Collections/TypeDefinitionsInterface.md)` | The type definitions for the model |
| `$conditions` | `[ConditionsInterface](Models/Collections/ConditionsInterface.md)` | The conditions for the model |
| `$schemaVersion` | `SchemaVersion` | The schema version to use (default: 1.1) |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
 Throwable&gt; The result of the authorization model creation request

### createStore


```php
public function createStore(string $name): [ResultInterface](Results/ResultInterface.md)
```

Creates a new store with the given name.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | `string` | The name for the new store |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
 Throwable&gt; The result of the store creation request

### deleteStore


```php
public function deleteStore([StoreInterface](Models/StoreInterface.md) | string $store): [ResultInterface](Results/ResultInterface.md)
```

Deletes a store.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md) | string` | The store to delete |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
 Throwable&gt; The result of the store deletion request

### dsl


```php
public function dsl(string $dsl): [ResultInterface](Results/ResultInterface.md)
```

Parses a DSL string and returns an AuthorizationModel.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$dsl` | `string` | The DSL string to parse |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
 Throwable&gt; The result of the DSL transformation request

### expand


```php
public function expand([StoreInterface](Models/StoreInterface.md) | string $store, [TupleKeyInterface](Models/TupleKeyInterface.md) $tupleKey, ?[AuthorizationModelInterface](Models/AuthorizationModelInterface.md) | string | null $model = NULL, ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) $contextualTuples = NULL, ?Consistency $consistency = NULL): [ResultInterface](Results/ResultInterface.md)
```

Expands a relationship tuple to show all users that have the relationship.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md) | string` | The store containing the tuple |
| `$tupleKey` | `[TupleKeyInterface](Models/TupleKeyInterface.md)` | The tuple to expand |
| `$model` | `?[AuthorizationModelInterface](Models/AuthorizationModelInterface.md) | string | null` | The authorization model to use |
| `$contextualTuples` | `?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)` | Additional tuples for contextual evaluation |
| `$consistency` | `?Consistency` | Override the default consistency level |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
 Throwable&gt; The result of the expansion request

### getAuthorizationModel


```php
public function getAuthorizationModel([StoreInterface](Models/StoreInterface.md) | string $store, [AuthorizationModelInterface](Models/AuthorizationModelInterface.md) | string $model): [ResultInterface](Results/ResultInterface.md)
```

Retrieves an authorization model by ID.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md) | string` | The store containing the model |
| `$model` | `[AuthorizationModelInterface](Models/AuthorizationModelInterface.md) | string` | The model to retrieve |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
 Throwable&gt; The result of the authorization model retrieval request

### getLastRequest


```php
public function getLastRequest(): ?Psr\Http\Message\RequestInterface
```

Retrieves the last HTTP request made by the client.


#### Returns
`?Psr\Http\Message\RequestInterface`
 The last request, or null if no request has been made

### getLastResponse


```php
public function getLastResponse(): ?Psr\Http\Message\ResponseInterface
```

Retrieves the last HTTP response received by the client.


#### Returns
`?Psr\Http\Message\ResponseInterface`
 The last response, or null if no response has been received

### getStore


```php
public function getStore([StoreInterface](Models/StoreInterface.md) | string $store): [ResultInterface](Results/ResultInterface.md)
```

Retrieves store details by ID.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md) | string` | The store to retrieve |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
 Throwable&gt; The result of the store retrieval request

### listAuthorizationModels


```php
public function listAuthorizationModels([StoreInterface](Models/StoreInterface.md) | string $store, ?string $continuationToken = NULL, ?int $pageSize = NULL): [ResultInterface](Results/ResultInterface.md)
```

Lists authorization models in a store with pagination.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md) | string` | The store to list models from |
| `$continuationToken` | `?string` | Token for pagination |
| `$pageSize` | `?int` | Maximum number of models to return |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
 Throwable&gt; The result of the authorization model listing request

### listObjects


```php
public function listObjects([StoreInterface](Models/StoreInterface.md) | string $store, [AuthorizationModelInterface](Models/AuthorizationModelInterface.md) | string $model, string $type, string $relation, string $user, ?object $context = NULL, ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) $contextualTuples = NULL, ?Consistency $consistency = NULL): [ResultInterface](Results/ResultInterface.md)
```

Lists objects that have a specific relationship with a user.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md) | string` | The store to query |
| `$model` | `[AuthorizationModelInterface](Models/AuthorizationModelInterface.md) | string` | The authorization model to use |
| `$type` | `string` | The type of objects to list |
| `$relation` | `string` | The relationship to check |
| `$user` | `string` | The user to check relationships for |
| `$context` | `?object` | Additional context for evaluation |
| `$contextualTuples` | `?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)` | Additional tuples for contextual evaluation |
| `$consistency` | `?Consistency` | Override the default consistency level |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
 Throwable&gt; The result of the object listing request

### listStores


```php
public function listStores(?string $continuationToken = NULL, ?int $pageSize = NULL): [ResultInterface](Results/ResultInterface.md)
```

Lists all stores with pagination.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$continuationToken` | `?string` | Token for pagination |
| `$pageSize` | `?int` | Maximum number of stores to return |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
 Throwable&gt; The result of the store listing request

### listTupleChanges


```php
public function listTupleChanges([StoreInterface](Models/StoreInterface.md) | string $store, ?string $continuationToken = NULL, ?int $pageSize = NULL, ?string $type = NULL, ?DateTimeImmutable $startTime = NULL): [ResultInterface](Results/ResultInterface.md)
```

Lists changes to relationship tuples in a store.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md) | string` | The store to list changes for |
| `$continuationToken` | `?string` | Token for pagination |
| `$pageSize` | `?int` | Maximum number of changes to return |
| `$type` | `?string` | Filter changes by type |
| `$startTime` | `?DateTimeImmutable` | Only include changes at or after this time (inclusive) |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
 Throwable&gt; The result of the tuple change listing request

### listUsers


```php
public function listUsers([StoreInterface](Models/StoreInterface.md) | string $store, [AuthorizationModelInterface](Models/AuthorizationModelInterface.md) | string $model, string $object, string $relation, [UserTypeFiltersInterface](Models/Collections/UserTypeFiltersInterface.md) $userFilters, ?object $context = NULL, ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) $contextualTuples = NULL, ?Consistency $consistency = NULL): [ResultInterface](Results/ResultInterface.md)
```

Lists users that have a specific relationship with an object.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md) | string` | The store to query |
| `$model` | `[AuthorizationModelInterface](Models/AuthorizationModelInterface.md) | string` | The authorization model to use |
| `$object` | `string` | The object to check relationships for |
| `$relation` | `string` | The relationship to check |
| `$userFilters` | `[UserTypeFiltersInterface](Models/Collections/UserTypeFiltersInterface.md)` | Filters for user types to include |
| `$context` | `?object` | Additional context for evaluation |
| `$contextualTuples` | `?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)` | Additional tuples for contextual evaluation |
| `$consistency` | `?Consistency` | Override the default consistency level |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
 Throwable&gt; The result of the user listing request

### readAssertions


```php
public function readAssertions([StoreInterface](Models/StoreInterface.md) | string $store, [AuthorizationModelInterface](Models/AuthorizationModelInterface.md) | string $model): [ResultInterface](Results/ResultInterface.md)
```

Retrieves assertions for an authorization model.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md) | string` | The store containing the model |
| `$model` | `[AuthorizationModelInterface](Models/AuthorizationModelInterface.md) | string` | The model to get assertions for |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
 Throwable&gt; The result of the assertions read request

### readTuples


```php
public function readTuples([StoreInterface](Models/StoreInterface.md) | string $store, [TupleKeyInterface](Models/TupleKeyInterface.md) $tupleKey, ?string $continuationToken = NULL, ?int $pageSize = NULL, ?Consistency $consistency = NULL): [ResultInterface](Results/ResultInterface.md)
```

Reads relationship tuples from a store with optional filtering and pagination.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md) | string` | The store to read from |
| `$tupleKey` | `[TupleKeyInterface](Models/TupleKeyInterface.md)` | Filter tuples by this key (return all if null) |
| `$continuationToken` | `?string` | Token for pagination |
| `$pageSize` | `?int` | Maximum number of tuples to return |
| `$consistency` | `?Consistency` | Override the default consistency level |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
 Throwable&gt; The result of the tuple read request

### writeAssertions


```php
public function writeAssertions([StoreInterface](Models/StoreInterface.md) | string $store, [AuthorizationModelInterface](Models/AuthorizationModelInterface.md) | string $model, [AssertionsInterface](Models/Collections/AssertionsInterface.md) $assertions): [ResultInterface](Results/ResultInterface.md)
```

Creates or updates assertions for an authorization model.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md) | string` | The store containing the model |
| `$model` | `[AuthorizationModelInterface](Models/AuthorizationModelInterface.md) | string` | The model to update assertions for |
| `$assertions` | `[AssertionsInterface](Models/Collections/AssertionsInterface.md)` | The assertions to upsert |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
 Throwable&gt; The result of the assertion write request

### writeTuples


```php
public function writeTuples([StoreInterface](Models/StoreInterface.md) | string $store, [AuthorizationModelInterface](Models/AuthorizationModelInterface.md) | string $model, ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) $writes = NULL, ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) $deletes = NULL): [ResultInterface](Results/ResultInterface.md)
```

Writes or deletes relationship tuples in a store.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md) | string` | The store to modify |
| `$model` | `[AuthorizationModelInterface](Models/AuthorizationModelInterface.md) | string` | The authorization model to use |
| `$writes` | `?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)` | Tuples to write (create or update) |
| `$deletes` | `?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)` | Tuples to delete |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
 Throwable&gt; The result of the tuple write request

