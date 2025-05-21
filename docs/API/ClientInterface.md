# ClientInterface


## Namespace
`OpenFGA`


## Methods
### check


```php
public function check([StoreInterface](Models/StoreInterface.md)|string $store, [AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string $model, [TupleKeyInterface](Models/TupleKeyInterface.md) $tupleKey, ?bool $trace = NULL, ?object $context = NULL, ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) $contextualTuples = NULL, ?Consistency $consistency = NULL): [CheckResponseInterface](Responses/CheckResponseInterface.md)
```

Checks if a user has a specific relationship with an object.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` | The store to check against |
| `$model` | `[AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string` | The authorization model to use |
| `$tupleKey` | `[TupleKeyInterface](Models/TupleKeyInterface.md)` | The relationship to check |
| `$trace` | `?bool` | Whether to include a trace in the response |
| `$context` | `?object` | Additional context for the check |
| `$contextualTuples` | `?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)` | Additional tuples for contextual evaluation |
| `$consistency` | `?Consistency` | Override the default consistency level |

#### Returns
`[CheckResponseInterface](Responses/CheckResponseInterface.md)` The result of the check

### createAuthorizationModel


```php
public function createAuthorizationModel([StoreInterface](Models/StoreInterface.md)|string $store, [TypeDefinitionsInterface](Models/Collections/TypeDefinitionsInterface.md) $typeDefinitions, [ConditionsInterface](Models/Collections/ConditionsInterface.md) $conditions, SchemaVersion $schemaVersion = \OpenFGA\Models\Enums\SchemaVersion::V1_1): [CreateAuthorizationModelResponseInterface](Responses/CreateAuthorizationModelResponseInterface.md)
```

Creates a new authorization model with the given type definitions and conditions.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` | The store to create the model in |
| `$typeDefinitions` | `[TypeDefinitionsInterface](Models/Collections/TypeDefinitionsInterface.md)` | The type definitions for the model |
| `$conditions` | `[ConditionsInterface](Models/Collections/ConditionsInterface.md)` | The conditions for the model |
| `$schemaVersion` | `SchemaVersion` | The schema version to use (default: 1.1) |

#### Returns
`[CreateAuthorizationModelResponseInterface](Responses/CreateAuthorizationModelResponseInterface.md)` The created authorization model

### createStore


```php
public function createStore(string $name): [CreateStoreResponseInterface](Responses/CreateStoreResponseInterface.md)
```

Creates a new store with the given name.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | `string` | The name for the new store |

#### Returns
`[CreateStoreResponseInterface](Responses/CreateStoreResponseInterface.md)` The created store details

### deleteStore


```php
public function deleteStore([StoreInterface](Models/StoreInterface.md)|string $store): [DeleteStoreResponseInterface](Responses/DeleteStoreResponseInterface.md)
```

Deletes a store.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` | The store to delete |

#### Returns
`[DeleteStoreResponseInterface](Responses/DeleteStoreResponseInterface.md)` The deletion result

### dsl


```php
public function dsl(string $dsl): [AuthorizationModelInterface](Models/AuthorizationModelInterface.md)
```

Parses a DSL string and returns an AuthorizationModel.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$dsl` | `string` | The DSL string to parse |

#### Returns
`[AuthorizationModelInterface](Models/AuthorizationModelInterface.md)` The parsed authorization model

### expand


```php
public function expand([StoreInterface](Models/StoreInterface.md)|string $store, [TupleKeyInterface](Models/TupleKeyInterface.md) $tupleKey, ?[AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string|null $model = NULL, ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) $contextualTuples = NULL, ?Consistency $consistency = NULL): [ExpandResponseInterface](Responses/ExpandResponseInterface.md)
```

Expands a relationship tuple to show all users that have the relationship.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` | The store containing the tuple |
| `$tupleKey` | `[TupleKeyInterface](Models/TupleKeyInterface.md)` | The tuple to expand |
| `$model` | `?[AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string|null` | The authorization model to use |
| `$contextualTuples` | `?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)` | Additional tuples for contextual evaluation |
| `$consistency` | `?Consistency` | Override the default consistency level |

#### Returns
`[ExpandResponseInterface](Responses/ExpandResponseInterface.md)` The expanded relationship information

### getAuthorizationModel


```php
public function getAuthorizationModel([StoreInterface](Models/StoreInterface.md)|string $store, [AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string $model): [GetAuthorizationModelResponseInterface](Responses/GetAuthorizationModelResponseInterface.md)
```

Retrieves an authorization model by ID.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` | The store containing the model |
| `$model` | `[AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string` | The model to retrieve |

#### Returns
`[GetAuthorizationModelResponseInterface](Responses/GetAuthorizationModelResponseInterface.md)` The authorization model

### getLastRequest


```php
public function getLastRequest(): ?Psr\Http\Message\RequestInterface
```

Retrieves the last HTTP request made by the client.


#### Returns
`?Psr\Http\Message\RequestInterface` The last request, or null if no request has been made

### getLastResponse


```php
public function getLastResponse(): ?Psr\Http\Message\ResponseInterface
```

Retrieves the last HTTP response received by the client.


#### Returns
`?Psr\Http\Message\ResponseInterface` The last response, or null if no response has been received

### getStore


```php
public function getStore([StoreInterface](Models/StoreInterface.md)|string $store): [GetStoreResponseInterface](Responses/GetStoreResponseInterface.md)
```

Retrieves store details by ID.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` | The store to retrieve |

#### Returns
`[GetStoreResponseInterface](Responses/GetStoreResponseInterface.md)` The store details

### listAuthorizationModels


```php
public function listAuthorizationModels([StoreInterface](Models/StoreInterface.md)|string $store, ?string $continuationToken = NULL, ?int $pageSize = NULL): [ListAuthorizationModelsResponseInterface](Responses/ListAuthorizationModelsResponseInterface.md)
```

Lists authorization models in a store with pagination.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` | The store to list models from |
| `$continuationToken` | `?string` | Token for pagination |
| `$pageSize` | `?int` | Maximum number of models to return |

#### Returns
`[ListAuthorizationModelsResponseInterface](Responses/ListAuthorizationModelsResponseInterface.md)` The list of authorization models

### listObjects


```php
public function listObjects([StoreInterface](Models/StoreInterface.md)|string $store, [AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string $model, string $type, string $relation, string $user, ?object $context = NULL, ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) $contextualTuples = NULL, ?Consistency $consistency = NULL): [ListObjectsResponseInterface](Responses/ListObjectsResponseInterface.md)
```

Lists objects that have a specific relationship with a user.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` | The store to query |
| `$model` | `[AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string` | The authorization model to use |
| `$type` | `string` | The type of objects to list |
| `$relation` | `string` | The relationship to check |
| `$user` | `string` | The user to check relationships for |
| `$context` | `?object` | Additional context for evaluation |
| `$contextualTuples` | `?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)` | Additional tuples for contextual evaluation |
| `$consistency` | `?Consistency` | Override the default consistency level |

#### Returns
`[ListObjectsResponseInterface](Responses/ListObjectsResponseInterface.md)` The list of related objects

### listStores


```php
public function listStores(?string $continuationToken = NULL, ?int $pageSize = NULL): [ListStoresResponseInterface](Responses/ListStoresResponseInterface.md)
```

Lists all stores with pagination.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$continuationToken` | `?string` | Token for pagination |
| `$pageSize` | `?int` | Maximum number of stores to return |

#### Returns
`[ListStoresResponseInterface](Responses/ListStoresResponseInterface.md)` The list of stores

### listTupleChanges


```php
public function listTupleChanges([StoreInterface](Models/StoreInterface.md)|string $store, ?string $continuationToken = NULL, ?int $pageSize = NULL, ?string $type = NULL, ?DateTimeImmutable $startTime = NULL): [ListTupleChangesResponseInterface](Responses/ListTupleChangesResponseInterface.md)
```

Lists changes to relationship tuples in a store.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` | The store to list changes for |
| `$continuationToken` | `?string` | Token for pagination |
| `$pageSize` | `?int` | Maximum number of changes to return |
| `$type` | `?string` | Filter changes by type |
| `$startTime` | `?DateTimeImmutable` | Only include changes at or after this time (inclusive) |

#### Returns
`[ListTupleChangesResponseInterface](Responses/ListTupleChangesResponseInterface.md)` The list of tuple changes

### listUsers


```php
public function listUsers([StoreInterface](Models/StoreInterface.md)|string $store, [AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string $model, string $object, string $relation, [UserTypeFiltersInterface](Models/Collections/UserTypeFiltersInterface.md) $userFilters, ?object $context = NULL, ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) $contextualTuples = NULL, ?Consistency $consistency = NULL): [ListUsersResponseInterface](Responses/ListUsersResponseInterface.md)
```

Lists users that have a specific relationship with an object.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` | The store to query |
| `$model` | `[AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string` | The authorization model to use |
| `$object` | `string` | The object to check relationships for |
| `$relation` | `string` | The relationship to check |
| `$userFilters` | `[UserTypeFiltersInterface](Models/Collections/UserTypeFiltersInterface.md)` | Filters for user types to include |
| `$context` | `?object` | Additional context for evaluation |
| `$contextualTuples` | `?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)` | Additional tuples for contextual evaluation |
| `$consistency` | `?Consistency` | Override the default consistency level |

#### Returns
`[ListUsersResponseInterface](Responses/ListUsersResponseInterface.md)` The list of related users

### readAssertions


```php
public function readAssertions([StoreInterface](Models/StoreInterface.md)|string $store, [AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string $model): [ReadAssertionsResponseInterface](Responses/ReadAssertionsResponseInterface.md)
```

Retrieves assertions for an authorization model.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` | The store containing the model |
| `$model` | `[AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string` | The model to get assertions for |

#### Returns
`[ReadAssertionsResponseInterface](Responses/ReadAssertionsResponseInterface.md)` The model&#039;s assertions

### readTuples


```php
public function readTuples([StoreInterface](Models/StoreInterface.md)|string $store, [TupleKeyInterface](Models/TupleKeyInterface.md) $tupleKey, ?string $continuationToken = NULL, ?int $pageSize = NULL, ?Consistency $consistency = NULL): [ReadTuplesResponseInterface](Responses/ReadTuplesResponseInterface.md)
```

Reads relationship tuples from a store with optional filtering and pagination.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` | The store to read from |
| `$tupleKey` | `[TupleKeyInterface](Models/TupleKeyInterface.md)` | Filter tuples by this key (return all if null) |
| `$continuationToken` | `?string` | Token for pagination |
| `$pageSize` | `?int` | Maximum number of tuples to return |
| `$consistency` | `?Consistency` | Override the default consistency level |

#### Returns
`[ReadTuplesResponseInterface](Responses/ReadTuplesResponseInterface.md)` The matching relationship tuples

### writeAssertions


```php
public function writeAssertions([StoreInterface](Models/StoreInterface.md)|string $store, [AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string $model, [AssertionsInterface](Models/Collections/AssertionsInterface.md) $assertions): [WriteAssertionsResponseInterface](Responses/WriteAssertionsResponseInterface.md)
```

Creates or updates assertions for an authorization model.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` | The store containing the model |
| `$model` | `[AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string` | The model to update assertions for |
| `$assertions` | `[AssertionsInterface](Models/Collections/AssertionsInterface.md)` | The assertions to upsert |

#### Returns
`[WriteAssertionsResponseInterface](Responses/WriteAssertionsResponseInterface.md)` The result of the operation

### writeTuples


```php
public function writeTuples([StoreInterface](Models/StoreInterface.md)|string $store, [AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string $model, ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) $writes = NULL, ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) $deletes = NULL): [WriteTuplesResponseInterface](Responses/WriteTuplesResponseInterface.md)
```

Writes or deletes relationship tuples in a store.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` | The store to modify |
| `$model` | `[AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string` | The authorization model to use |
| `$writes` | `?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)` | Tuples to write (create or update) |
| `$deletes` | `?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)` | Tuples to delete |

#### Returns
`[WriteTuplesResponseInterface](Responses/WriteTuplesResponseInterface.md)` The result of the operation

