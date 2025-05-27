# ClientInterface


## Namespace
`OpenFGA`




## Methods
### assertLastRequest


```php
public function assertLastRequest(): RequestInterface
```

Retrieves the last HTTP request made by the client.


#### Returns
`RequestInterface`
 The last request

### check


```php
public function check(StoreInterface | string $store, AuthorizationModelInterface | string $model, TupleKeyInterface $tupleKey, null | bool $trace = NULL, null | object $context = NULL, null | TupleKeysInterface<TupleKeyInterface> $contextualTuples = NULL, null | Consistency $consistency = NULL): ResultInterface<CheckResponseInterface, Throwable>
```

Checks if a user has a specific relationship with an object.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface | string` | The store to check against |
| `$model` | `AuthorizationModelInterface | string` | The authorization model to use |
| `$tupleKey` | `TupleKeyInterface` | The relationship to check |
| `$trace` | `null | bool` | Whether to include a trace in the response |
| `$context` | `null | object` | Additional context for the check |
| `$contextualTuples` | `null | TupleKeysInterface<TupleKeyInterface>` | Additional tuples for contextual evaluation |
| `$consistency` | `null | Consistency` | Override the default consistency level |

#### Returns
`ResultInterface<CheckResponseInterface, Throwable>`
 The result of the check request

### createAuthorizationModel


```php
public function createAuthorizationModel(StoreInterface | string $store, TypeDefinitionsInterface<TypeDefinitionInterface> $typeDefinitions, ConditionsInterface<ConditionInterface> $conditions, SchemaVersion $schemaVersion = SchemaVersion::V1_1): ResultInterface<CreateAuthorizationModelResponseInterface, Throwable>
```

Creates a new authorization model with the given type definitions and conditions.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface | string` | The store to create the model in |
| `$typeDefinitions` | `TypeDefinitionsInterface<TypeDefinitionInterface>` | The type definitions for the model |
| `$conditions` | `ConditionsInterface<ConditionInterface>` | The conditions for the model |
| `$schemaVersion` | `SchemaVersion` | The schema version to use (default: 1.1) |

#### Returns
`ResultInterface<CreateAuthorizationModelResponseInterface, Throwable>`
 The result of the authorization model creation request

### createStore


```php
public function createStore(string $name): ResultInterface<CreateStoreResponseInterface, Throwable>
```

Creates a new store with the given name.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | `string` | The name for the new store |

#### Returns
`ResultInterface<CreateStoreResponseInterface, Throwable>`
 The result of the store creation request

### deleteStore


```php
public function deleteStore(StoreInterface | string $store): ResultInterface<DeleteStoreResponseInterface, Throwable>
```

Deletes a store.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface | string` | The store to delete |

#### Returns
`ResultInterface<DeleteStoreResponseInterface, Throwable>`
 The result of the store deletion request

### dsl


```php
public function dsl(string $dsl): ResultInterface<AuthorizationModelInterface, Throwable>
```

Parses a DSL string and returns an AuthorizationModel.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$dsl` | `string` | The DSL string to parse |

#### Returns
`ResultInterface<AuthorizationModelInterface, Throwable>`
 The result of the DSL transformation request

### expand


```php
public function expand(StoreInterface | string $store, TupleKeyInterface $tupleKey, null | AuthorizationModelInterface | string $model = NULL, null | TupleKeysInterface<TupleKeyInterface> $contextualTuples = NULL, null | Consistency $consistency = NULL): ResultInterface<ExpandResponseInterface, Throwable>
```

Expands a relationship tuple to show all users that have the relationship.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface | string` | The store containing the tuple |
| `$tupleKey` | `TupleKeyInterface` | The tuple to expand |
| `$model` | `null | AuthorizationModelInterface | string` | The authorization model to use |
| `$contextualTuples` | `null | TupleKeysInterface<TupleKeyInterface>` | Additional tuples for contextual evaluation |
| `$consistency` | `null | Consistency` | Override the default consistency level |

#### Returns
`ResultInterface<ExpandResponseInterface, Throwable>`
 The result of the expansion request

### getAuthorizationModel


```php
public function getAuthorizationModel(StoreInterface | string $store, AuthorizationModelInterface | string $model): ResultInterface<GetAuthorizationModelResponseInterface, Throwable>
```

Retrieves an authorization model by ID.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface | string` | The store containing the model |
| `$model` | `AuthorizationModelInterface | string` | The model to retrieve |

#### Returns
`ResultInterface<GetAuthorizationModelResponseInterface, Throwable>`
 The result of the authorization model retrieval request

### getLastRequest


```php
public function getLastRequest(): ?Psr\Http\Message\RequestInterface
```

Retrieves the last HTTP request made by the client.


#### Returns
`?Psr\Http\Message\RequestInterface`

### getLastResponse


```php
public function getLastResponse(): ?Psr\Http\Message\ResponseInterface
```

Retrieves the last HTTP response received by the client.


#### Returns
`?Psr\Http\Message\ResponseInterface`

### getStore


```php
public function getStore(StoreInterface | string $store): ResultInterface<GetStoreResponseInterface, Throwable>
```

Retrieves store details by ID.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface | string` | The store to retrieve |

#### Returns
`ResultInterface<GetStoreResponseInterface, Throwable>`
 The result of the store retrieval request

### listAuthorizationModels


```php
public function listAuthorizationModels(StoreInterface | string $store, null | string $continuationToken = NULL, ?int $pageSize = NULL): ResultInterface<ListAuthorizationModelsResponseInterface, Throwable>
```

Lists authorization models in a store with pagination.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface | string` | The store to list models from |
| `$continuationToken` | `null | string` | Token for pagination |
| `$pageSize` | `?int` |  |

#### Returns
`ResultInterface<ListAuthorizationModelsResponseInterface, Throwable>`
 The result of the authorization model listing request

### listObjects


```php
public function listObjects(StoreInterface | string $store, AuthorizationModelInterface | string $model, string $type, string $relation, string $user, null | object $context = NULL, null | TupleKeysInterface<TupleKeyInterface> $contextualTuples = NULL, null | Consistency $consistency = NULL): ResultInterface<ListObjectsResponseInterface, Throwable>
```

Lists objects that have a specific relationship with a user.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface | string` | The store to query |
| `$model` | `AuthorizationModelInterface | string` | The authorization model to use |
| `$type` | `string` | The type of objects to list |
| `$relation` | `string` | The relationship to check |
| `$user` | `string` | The user to check relationships for |
| `$context` | `null | object` | Additional context for evaluation |
| `$contextualTuples` | `null | TupleKeysInterface<TupleKeyInterface>` | Additional tuples for contextual evaluation |
| `$consistency` | `null | Consistency` | Override the default consistency level |

#### Returns
`ResultInterface<ListObjectsResponseInterface, Throwable>`
 The result of the object listing request

### listStores


```php
public function listStores(null | string $continuationToken = NULL, ?int $pageSize = NULL): ResultInterface<ListStoresResponseInterface, Throwable>
```

Lists all stores with pagination.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$continuationToken` | `null | string` | Token for pagination |
| `$pageSize` | `?int` |  |

#### Returns
`ResultInterface<ListStoresResponseInterface, Throwable>`
 The result of the store listing request

### listTupleChanges


```php
public function listTupleChanges(StoreInterface | string $store, null | string $continuationToken = NULL, ?int $pageSize = NULL, null | string $type = NULL, null | DateTimeImmutable $startTime = NULL): ResultInterface<ListTupleChangesResponseInterface, Throwable>
```

Lists changes to relationship tuples in a store.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface | string` | The store to list changes for |
| `$continuationToken` | `null | string` | Token for pagination |
| `$pageSize` | `?int` |  |
| `$type` | `null | string` | Filter changes by type |
| `$startTime` | `null | DateTimeImmutable` | Only include changes at or after this time (inclusive) |

#### Returns
`ResultInterface<ListTupleChangesResponseInterface, Throwable>`
 The result of the tuple change listing request

### listUsers


```php
public function listUsers(StoreInterface | string $store, AuthorizationModelInterface | string $model, string $object, string $relation, UserTypeFiltersInterface<UserTypeFilterInterface> $userFilters, null | object $context = NULL, null | TupleKeysInterface<TupleKeyInterface> $contextualTuples = NULL, null | Consistency $consistency = NULL): ResultInterface<ListUsersResponseInterface, Throwable>
```

Lists users that have a specific relationship with an object.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface | string` | The store to query |
| `$model` | `AuthorizationModelInterface | string` | The authorization model to use |
| `$object` | `string` | The object to check relationships for |
| `$relation` | `string` | The relationship to check |
| `$userFilters` | `UserTypeFiltersInterface<UserTypeFilterInterface>` | Filters for user types to include |
| `$context` | `null | object` | Additional context for evaluation |
| `$contextualTuples` | `null | TupleKeysInterface<TupleKeyInterface>` | Additional tuples for contextual evaluation |
| `$consistency` | `null | Consistency` | Override the default consistency level |

#### Returns
`ResultInterface<ListUsersResponseInterface, Throwable>`
 The result of the user listing request

### readAssertions


```php
public function readAssertions(StoreInterface | string $store, AuthorizationModelInterface | string $model): ResultInterface<ReadAssertionsResponseInterface, Throwable>
```

Retrieves assertions for an authorization model.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface | string` | The store containing the model |
| `$model` | `AuthorizationModelInterface | string` | The model to get assertions for |

#### Returns
`ResultInterface<ReadAssertionsResponseInterface, Throwable>`
 The result of the assertions read request

### readTuples


```php
public function readTuples(StoreInterface | string $store, TupleKeyInterface $tupleKey, null | string $continuationToken = NULL, ?int $pageSize = NULL, null | Consistency $consistency = NULL): ResultInterface<ReadTuplesResponseInterface, Throwable>
```

Reads relationship tuples from a store with optional filtering and pagination.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface | string` | The store to read from |
| `$tupleKey` | `TupleKeyInterface` | Filter tuples by this key (return all if null) |
| `$continuationToken` | `null | string` | Token for pagination |
| `$pageSize` | `?int` |  |
| `$consistency` | `null | Consistency` | Override the default consistency level |

#### Returns
`ResultInterface<ReadTuplesResponseInterface, Throwable>`
 The result of the tuple read request

### writeAssertions


```php
public function writeAssertions(StoreInterface | string $store, AuthorizationModelInterface | string $model, AssertionsInterface<AssertionInterface> $assertions): ResultInterface<WriteAssertionsResponseInterface, Throwable>
```

Creates or updates assertions for an authorization model.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface | string` | The store containing the model |
| `$model` | `AuthorizationModelInterface | string` | The model to update assertions for |
| `$assertions` | `AssertionsInterface<AssertionInterface>` | The assertions to upsert |

#### Returns
`ResultInterface<WriteAssertionsResponseInterface, Throwable>`
 The result of the assertion write request

### writeTuples


```php
public function writeTuples(StoreInterface | string $store, AuthorizationModelInterface | string $model, null | TupleKeysInterface<TupleKeyInterface> $writes = NULL, null | TupleKeysInterface<TupleKeyInterface> $deletes = NULL): ResultInterface<WriteTuplesResponseInterface, Throwable>
```

Writes or deletes relationship tuples in a store.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface | string` | The store to modify |
| `$model` | `AuthorizationModelInterface | string` | The authorization model to use |
| `$writes` | `null | TupleKeysInterface<TupleKeyInterface>` | Tuples to write (create or update) |
| `$deletes` | `null | TupleKeysInterface<TupleKeyInterface>` | Tuples to delete |

#### Returns
`ResultInterface<WriteTuplesResponseInterface, Throwable>`
 The result of the tuple write request

