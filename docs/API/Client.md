# Client


## Namespace
`OpenFGA`

## Implements
* [ClientInterface](ClientInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `VERSION` | `&#039;0.2.0&#039;` | The version of the OpenFGA PHP SDK. |


## Methods
### assertLastRequest


```php
public function assertLastRequest(): Psr\Http\Message\RequestInterface
```

Retrieves the last HTTP request made by the client.


#### Returns
Psr\Http\Message\RequestInterface
 The last request

### check


```php
public function check(OpenFGA\Models\StoreInterface|string $store, OpenFGA\Models\AuthorizationModelInterface|string $model, OpenFGA\Models\TupleKeyInterface $tupleKey, ?bool $trace = NULL, ?object $context = NULL, ?OpenFGA\Models\Collections\TupleKeysInterface $contextualTuples = NULL, ?OpenFGA\Models\Enums\Consistency $consistency = NULL): OpenFGA\Results\ResultInterface
```

Checks if a user has a specific relationship with an object.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | [StoreInterface](Models/StoreInterface.md) | string | The store to check against |
| `$model` | [AuthorizationModelInterface](Models/AuthorizationModelInterface.md) | string | The authorization model to use |
| `$tupleKey` | [TupleKeyInterface](Models/TupleKeyInterface.md) | The relationship to check |
| `$trace` | ?bool | Whether to include a trace in the response |
| `$context` | ?object | Additional context for the check |
| `$contextualTuples` | ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) | Additional tuples for contextual evaluation |
| `schemaVersion` | OpenFGA\Models\Enums\SchemaVersion |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)
 The result of the check request

### createAuthorizationModel


```php
public function createAuthorizationModel(OpenFGA\Models\StoreInterface|string $store, OpenFGA\Models\Collections\TypeDefinitionsInterface $typeDefinitions, ?OpenFGA\Models\Collections\ConditionsInterface $conditions = NULL, OpenFGA\Models\Enums\SchemaVersion $schemaVersion = OpenFGA\Models\Enums\SchemaVersion::V1_1): OpenFGA\Results\ResultInterface
```

Creates a new authorization model with the given type definitions and conditions.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | [StoreInterface](Models/StoreInterface.md) | string | The store to create the model in |
| `$typeDefinitions` | [TypeDefinitionsInterface](Models/Collections/TypeDefinitionsInterface.md) | The type definitions for the model |
| `$conditions` | ?[ConditionsInterface](Models/Collections/ConditionsInterface.md) | The conditions for the model |
| `name` | string |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)
 The result of the authorization model creation request

### createStore


```php
public function createStore(string $name): OpenFGA\Results\ResultInterface
```

Creates a new store with the given name.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `store` | OpenFGA\Models\StoreInterface|string |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)
 The result of the store creation request

### deleteStore


```php
public function deleteStore(OpenFGA\Models\StoreInterface|string $store): OpenFGA\Results\ResultInterface
```

Deletes a store.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `dsl` | string |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)
 The result of the store deletion request

### dsl


```php
public function dsl(string $dsl): OpenFGA\Results\ResultInterface
```

Parses a DSL string and returns an AuthorizationModel.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `consistency` | ?OpenFGA\Models\Enums\Consistency |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)
 The result of the DSL transformation request

### expand


```php
public function expand(OpenFGA\Models\StoreInterface|string $store, OpenFGA\Models\TupleKeyInterface $tupleKey, ?OpenFGA\Models\AuthorizationModelInterface|string|null $model = NULL, ?OpenFGA\Models\Collections\TupleKeysInterface $contextualTuples = NULL, ?OpenFGA\Models\Enums\Consistency $consistency = NULL): OpenFGA\Results\ResultInterface
```

Expands a relationship tuple to show all users that have the relationship.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | [StoreInterface](Models/StoreInterface.md) | string | The store containing the tuple |
| `$tupleKey` | [TupleKeyInterface](Models/TupleKeyInterface.md) | The tuple to expand |
| `$model` | ?[AuthorizationModelInterface](Models/AuthorizationModelInterface.md) | string | null | The authorization model to use |
| `$contextualTuples` | ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) | Additional tuples for contextual evaluation |
| `model` | OpenFGA\Models\AuthorizationModelInterface|string |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)
 The result of the expansion request

### getAuthorizationModel


```php
public function getAuthorizationModel(OpenFGA\Models\StoreInterface|string $store, OpenFGA\Models\AuthorizationModelInterface|string $model): OpenFGA\Results\ResultInterface
```

Retrieves an authorization model by ID.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | [StoreInterface](Models/StoreInterface.md) | string | The store containing the model |
| `store` | OpenFGA\Models\StoreInterface|string |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)
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
public function getStore(OpenFGA\Models\StoreInterface|string $store): OpenFGA\Results\ResultInterface
```

Retrieves store details by ID.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `pageSize` | ?int |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)
 The result of the store retrieval request

### listAuthorizationModels


```php
public function listAuthorizationModels(OpenFGA\Models\StoreInterface|string $store, ?string $continuationToken = NULL, ?int $pageSize = NULL): OpenFGA\Results\ResultInterface
```

Lists authorization models in a store with pagination.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | [StoreInterface](Models/StoreInterface.md) | string | The store to list models from |
| `$continuationToken` | ?string | Token for pagination |
| `consistency` | ?OpenFGA\Models\Enums\Consistency |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)
 The result of the authorization model listing request

### listObjects


```php
public function listObjects(OpenFGA\Models\StoreInterface|string $store, OpenFGA\Models\AuthorizationModelInterface|string $model, string $type, string $relation, string $user, ?object $context = NULL, ?OpenFGA\Models\Collections\TupleKeysInterface $contextualTuples = NULL, ?OpenFGA\Models\Enums\Consistency $consistency = NULL): OpenFGA\Results\ResultInterface
```

Lists objects that have a specific relationship with a user.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | [StoreInterface](Models/StoreInterface.md) | string | The store to query |
| `$model` | [AuthorizationModelInterface](Models/AuthorizationModelInterface.md) | string | The authorization model to use |
| `$type` | string | The type of objects to list |
| `$relation` | string | The relationship to check |
| `$user` | string | The user to check relationships for |
| `$context` | ?object | Additional context for evaluation |
| `$contextualTuples` | ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) | Additional tuples for contextual evaluation |
| `pageSize` | ?int |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)
 The result of the object listing request

### listStores


```php
public function listStores(?string $continuationToken = NULL, ?int $pageSize = NULL): OpenFGA\Results\ResultInterface
```

Lists all stores with pagination.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$continuationToken` | ?string | Token for pagination |
| `startTime` | ?DateTimeImmutable |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)
 The result of the store listing request

### listTupleChanges


```php
public function listTupleChanges(OpenFGA\Models\StoreInterface|string $store, ?string $continuationToken = NULL, ?int $pageSize = NULL, ?string $type = NULL, ?DateTimeImmutable $startTime = NULL): OpenFGA\Results\ResultInterface
```

Lists changes to relationship tuples in a store.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | [StoreInterface](Models/StoreInterface.md) | string | The store to list changes for |
| `$continuationToken` | ?string | Token for pagination |
| `$pageSize` | ?int |  |
| `$type` | ?string | Filter changes by type |
| `consistency` | ?OpenFGA\Models\Enums\Consistency |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)
 The result of the tuple change listing request

### listUsers


```php
public function listUsers(OpenFGA\Models\StoreInterface|string $store, OpenFGA\Models\AuthorizationModelInterface|string $model, string $object, string $relation, OpenFGA\Models\Collections\UserTypeFiltersInterface $userFilters, ?object $context = NULL, ?OpenFGA\Models\Collections\TupleKeysInterface $contextualTuples = NULL, ?OpenFGA\Models\Enums\Consistency $consistency = NULL): OpenFGA\Results\ResultInterface
```

Lists users that have a specific relationship with an object.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | [StoreInterface](Models/StoreInterface.md) | string | The store to query |
| `$model` | [AuthorizationModelInterface](Models/AuthorizationModelInterface.md) | string | The authorization model to use |
| `$object` | string | The object to check relationships for |
| `$relation` | string | The relationship to check |
| `$userFilters` | [UserTypeFiltersInterface](Models/Collections/UserTypeFiltersInterface.md) | Filters for user types to include |
| `$context` | ?object | Additional context for evaluation |
| `$contextualTuples` | ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) | Additional tuples for contextual evaluation |
| `model` | OpenFGA\Models\AuthorizationModelInterface|string |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)
 The result of the user listing request

### readAssertions


```php
public function readAssertions(OpenFGA\Models\StoreInterface|string $store, OpenFGA\Models\AuthorizationModelInterface|string $model): OpenFGA\Results\ResultInterface
```

Retrieves assertions for an authorization model.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | [StoreInterface](Models/StoreInterface.md) | string | The store containing the model |
| `consistency` | ?OpenFGA\Models\Enums\Consistency |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)
 The result of the assertions read request

### readTuples


```php
public function readTuples(OpenFGA\Models\StoreInterface|string $store, OpenFGA\Models\TupleKeyInterface $tupleKey, ?string $continuationToken = NULL, ?int $pageSize = NULL, ?OpenFGA\Models\Enums\Consistency $consistency = NULL): OpenFGA\Results\ResultInterface
```

Reads relationship tuples from a store with optional filtering and pagination.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | [StoreInterface](Models/StoreInterface.md) | string | The store to read from |
| `$tupleKey` | [TupleKeyInterface](Models/TupleKeyInterface.md) | Filter tuples by this key (return all if null) |
| `$continuationToken` | ?string | Token for pagination |
| `$pageSize` | ?int |  |
| `assertions` | OpenFGA\Models\Collections\AssertionsInterface |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)
 The result of the tuple read request

### writeAssertions


```php
public function writeAssertions(OpenFGA\Models\StoreInterface|string $store, OpenFGA\Models\AuthorizationModelInterface|string $model, OpenFGA\Models\Collections\AssertionsInterface $assertions): OpenFGA\Results\ResultInterface
```

Creates or updates assertions for an authorization model.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | [StoreInterface](Models/StoreInterface.md) | string | The store containing the model |
| `$model` | [AuthorizationModelInterface](Models/AuthorizationModelInterface.md) | string | The model to update assertions for |
| `deletes` | ?OpenFGA\Models\Collections\TupleKeysInterface |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)
 The result of the assertion write request

### writeTuples


```php
public function writeTuples(OpenFGA\Models\StoreInterface|string $store, OpenFGA\Models\AuthorizationModelInterface|string $model, ?OpenFGA\Models\Collections\TupleKeysInterface $writes = NULL, ?OpenFGA\Models\Collections\TupleKeysInterface $deletes = NULL): OpenFGA\Results\ResultInterface
```

Writes or deletes relationship tuples in a store.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | [StoreInterface](Models/StoreInterface.md) | string | The store to modify |
| `$model` | [AuthorizationModelInterface](Models/AuthorizationModelInterface.md) | string | The authorization model to use |
| `$writes` | ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) | Tuples to write (create or update) |
| `$deletes` | ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) | Tuples to delete |

#### Returns
[ResultInterface](Results/ResultInterface.md)
 The result of the tuple write request

