# Client


## Namespace
`OpenFGA`

## Implements
* [ClientInterface](ClientInterface.md)

## Methods
### check

```php
public function check([StoreInterface](Models/StoreInterface.md)|string $store, [AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string $model, [TupleKeyInterface](Models/TupleKeyInterface.md) $tupleKey, ?bool $trace = null, ?object $context = null, ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) $contextualTuples = null, ?Consistency $consistency = null): [CheckResponseInterface](Responses/CheckResponseInterface.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` |  |
| `$model` | `[AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string` |  |
| `$tupleKey` | `[TupleKeyInterface](Models/TupleKeyInterface.md)` |  |
| `$trace` | `?bool` |  |
| `$context` | `?object` |  |
| `$contextualTuples` | `?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)` |  |
| `$consistency` | `?Consistency` |  |

#### Returns
`[CheckResponseInterface](Responses/CheckResponseInterface.md)` 

### createAuthorizationModel

```php
public function createAuthorizationModel([StoreInterface](Models/StoreInterface.md)|string $store, [TypeDefinitionsInterface](Models/Collections/TypeDefinitionsInterface.md) $typeDefinitions, [ConditionsInterface](Models/Collections/ConditionsInterface.md) $conditions, SchemaVersion $schemaVersion = &quot;1.1&quot;): [CreateAuthorizationModelResponseInterface](Responses/CreateAuthorizationModelResponseInterface.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` |  |
| `$typeDefinitions` | `[TypeDefinitionsInterface](Models/Collections/TypeDefinitionsInterface.md)` |  |
| `$conditions` | `[ConditionsInterface](Models/Collections/ConditionsInterface.md)` |  |
| `$schemaVersion` | `SchemaVersion` |  |

#### Returns
`[CreateAuthorizationModelResponseInterface](Responses/CreateAuthorizationModelResponseInterface.md)` 

### createStore

```php
public function createStore(string $name): [CreateStoreResponseInterface](Responses/CreateStoreResponseInterface.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | `string` |  |

#### Returns
`[CreateStoreResponseInterface](Responses/CreateStoreResponseInterface.md)` 

### deleteStore

```php
public function deleteStore([StoreInterface](Models/StoreInterface.md)|string $store): [DeleteStoreResponseInterface](Responses/DeleteStoreResponseInterface.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` |  |

#### Returns
`[DeleteStoreResponseInterface](Responses/DeleteStoreResponseInterface.md)` 

### expand

```php
public function expand([StoreInterface](Models/StoreInterface.md)|string $store, [TupleKeyInterface](Models/TupleKeyInterface.md) $tupleKey, ?[AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string|null $model = null, ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) $contextualTuples = null, ?Consistency $consistency = null): [ExpandResponseInterface](Responses/ExpandResponseInterface.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` |  |
| `$tupleKey` | `[TupleKeyInterface](Models/TupleKeyInterface.md)` |  |
| `$model` | `?[AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string|null` |  |
| `$contextualTuples` | `?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)` |  |
| `$consistency` | `?Consistency` |  |

#### Returns
`[ExpandResponseInterface](Responses/ExpandResponseInterface.md)` 

### getAuthorizationModel

```php
public function getAuthorizationModel([StoreInterface](Models/StoreInterface.md)|string $store, [AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string $model): [GetAuthorizationModelResponseInterface](Responses/GetAuthorizationModelResponseInterface.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` |  |
| `$model` | `[AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string` |  |

#### Returns
`[GetAuthorizationModelResponseInterface](Responses/GetAuthorizationModelResponseInterface.md)` 

### getLastRequest

```php
public function getLastRequest(): ?Psr\Http\Message\RequestInterface
```



#### Returns
`?Psr\Http\Message\RequestInterface` 

### getLastResponse

```php
public function getLastResponse(): ?Psr\Http\Message\ResponseInterface
```



#### Returns
`?Psr\Http\Message\ResponseInterface` 

### getStore

```php
public function getStore([StoreInterface](Models/StoreInterface.md)|string $store): [GetStoreResponseInterface](Responses/GetStoreResponseInterface.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` |  |

#### Returns
`[GetStoreResponseInterface](Responses/GetStoreResponseInterface.md)` 

### listAuthorizationModels

```php
public function listAuthorizationModels([StoreInterface](Models/StoreInterface.md)|string $store, ?string $continuationToken = null, ?int $pageSize = null): [ListAuthorizationModelsResponseInterface](Responses/ListAuthorizationModelsResponseInterface.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` |  |
| `$continuationToken` | `?string` |  |
| `$pageSize` | `?int` |  |

#### Returns
`[ListAuthorizationModelsResponseInterface](Responses/ListAuthorizationModelsResponseInterface.md)` 

### listObjects

```php
public function listObjects([StoreInterface](Models/StoreInterface.md)|string $store, [AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string $model, string $type, string $relation, string $user, ?object $context = null, ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) $contextualTuples = null, ?Consistency $consistency = null): [ListObjectsResponseInterface](Responses/ListObjectsResponseInterface.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` |  |
| `$model` | `[AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string` |  |
| `$type` | `string` |  |
| `$relation` | `string` |  |
| `$user` | `string` |  |
| `$context` | `?object` |  |
| `$contextualTuples` | `?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)` |  |
| `$consistency` | `?Consistency` |  |

#### Returns
`[ListObjectsResponseInterface](Responses/ListObjectsResponseInterface.md)` 

### listStores

```php
public function listStores(?string $continuationToken = null, ?int $pageSize = null): [ListStoresResponseInterface](Responses/ListStoresResponseInterface.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$continuationToken` | `?string` |  |
| `$pageSize` | `?int` |  |

#### Returns
`[ListStoresResponseInterface](Responses/ListStoresResponseInterface.md)` 

### listTupleChanges

```php
public function listTupleChanges([StoreInterface](Models/StoreInterface.md)|string $store, ?string $continuationToken = null, ?int $pageSize = null, ?string $type = null, ?DateTimeImmutable $startTime = null): [ListTupleChangesResponseInterface](Responses/ListTupleChangesResponseInterface.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` |  |
| `$continuationToken` | `?string` |  |
| `$pageSize` | `?int` |  |
| `$type` | `?string` |  |
| `$startTime` | `?DateTimeImmutable` |  |

#### Returns
`[ListTupleChangesResponseInterface](Responses/ListTupleChangesResponseInterface.md)` 

### listUsers

```php
public function listUsers([StoreInterface](Models/StoreInterface.md)|string $store, [AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string $model, string $object, string $relation, [UserTypeFiltersInterface](Models/Collections/UserTypeFiltersInterface.md) $userFilters, ?object $context = null, ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) $contextualTuples = null, ?Consistency $consistency = null): [ListUsersResponseInterface](Responses/ListUsersResponseInterface.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` |  |
| `$model` | `[AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string` |  |
| `$object` | `string` |  |
| `$relation` | `string` |  |
| `$userFilters` | `[UserTypeFiltersInterface](Models/Collections/UserTypeFiltersInterface.md)` |  |
| `$context` | `?object` |  |
| `$contextualTuples` | `?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)` |  |
| `$consistency` | `?Consistency` |  |

#### Returns
`[ListUsersResponseInterface](Responses/ListUsersResponseInterface.md)` 

### readAssertions

```php
public function readAssertions([StoreInterface](Models/StoreInterface.md)|string $store, [AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string $model): [ReadAssertionsResponseInterface](Responses/ReadAssertionsResponseInterface.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` |  |
| `$model` | `[AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string` |  |

#### Returns
`[ReadAssertionsResponseInterface](Responses/ReadAssertionsResponseInterface.md)` 

### readTuples

```php
public function readTuples([StoreInterface](Models/StoreInterface.md)|string $store, [TupleKeyInterface](Models/TupleKeyInterface.md) $tupleKey, ?string $continuationToken = null, ?int $pageSize = null, ?Consistency $consistency = null): [ReadTuplesResponseInterface](Responses/ReadTuplesResponseInterface.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` |  |
| `$tupleKey` | `[TupleKeyInterface](Models/TupleKeyInterface.md)` |  |
| `$continuationToken` | `?string` |  |
| `$pageSize` | `?int` |  |
| `$consistency` | `?Consistency` |  |

#### Returns
`[ReadTuplesResponseInterface](Responses/ReadTuplesResponseInterface.md)` 

### writeAssertions

```php
public function writeAssertions([StoreInterface](Models/StoreInterface.md)|string $store, [AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string $model, [AssertionsInterface](Models/Collections/AssertionsInterface.md) $assertions): [WriteAssertionsResponseInterface](Responses/WriteAssertionsResponseInterface.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` |  |
| `$model` | `[AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string` |  |
| `$assertions` | `[AssertionsInterface](Models/Collections/AssertionsInterface.md)` |  |

#### Returns
`[WriteAssertionsResponseInterface](Responses/WriteAssertionsResponseInterface.md)` 

### writeTuples

```php
public function writeTuples([StoreInterface](Models/StoreInterface.md)|string $store, [AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string $model, ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) $writes = null, ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) $deletes = null): [WriteTuplesResponseInterface](Responses/WriteTuplesResponseInterface.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `[StoreInterface](Models/StoreInterface.md)|string` |  |
| `$model` | `[AuthorizationModelInterface](Models/AuthorizationModelInterface.md)|string` |  |
| `$writes` | `?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)` |  |
| `$deletes` | `?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)` |  |

#### Returns
`[WriteTuplesResponseInterface](Responses/WriteTuplesResponseInterface.md)` 

