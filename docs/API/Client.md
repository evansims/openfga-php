# Client


## Namespace
`OpenFGA`

## Implements
* `OpenFGA\ClientInterface`

## Methods
### check

```php
public function check(StoreInterface|string $store, AuthorizationModelInterface|string $model, TupleKeyInterface $tupleKey, ?bool $trace = null, ?object $context = null, ?TupleKeysInterface $contextualTuples = null, ?Consistency $consistency = null): CheckResponseInterface
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface|string` |  |
| `$model` | `AuthorizationModelInterface|string` |  |
| `$tupleKey` | `TupleKeyInterface` |  |
| `$trace` | `?bool` |  |
| `$context` | `?object` |  |
| `$contextualTuples` | `?TupleKeysInterface` |  |
| `$consistency` | `?Consistency` |  |

#### Returns
`CheckResponseInterface` 

### createAuthorizationModel

```php
public function createAuthorizationModel(StoreInterface|string $store, TypeDefinitionsInterface $typeDefinitions, ConditionsInterface $conditions, SchemaVersion $schemaVersion = &quot;1.1&quot;): CreateAuthorizationModelResponseInterface
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface|string` |  |
| `$typeDefinitions` | `TypeDefinitionsInterface` |  |
| `$conditions` | `ConditionsInterface` |  |
| `$schemaVersion` | `SchemaVersion` |  |

#### Returns
`CreateAuthorizationModelResponseInterface` 

### createStore

```php
public function createStore(string $name): CreateStoreResponseInterface
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | `string` |  |

#### Returns
`CreateStoreResponseInterface` 

### deleteStore

```php
public function deleteStore(StoreInterface|string $store): DeleteStoreResponseInterface
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface|string` |  |

#### Returns
`DeleteStoreResponseInterface` 

### expand

```php
public function expand(StoreInterface|string $store, TupleKeyInterface $tupleKey, ?AuthorizationModelInterface|string|null $model = null, ?TupleKeysInterface $contextualTuples = null, ?Consistency $consistency = null): ExpandResponseInterface
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface|string` |  |
| `$tupleKey` | `TupleKeyInterface` |  |
| `$model` | `?AuthorizationModelInterface|string|null` |  |
| `$contextualTuples` | `?TupleKeysInterface` |  |
| `$consistency` | `?Consistency` |  |

#### Returns
`ExpandResponseInterface` 

### getAuthorizationModel

```php
public function getAuthorizationModel(StoreInterface|string $store, AuthorizationModelInterface|string $model): GetAuthorizationModelResponseInterface
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface|string` |  |
| `$model` | `AuthorizationModelInterface|string` |  |

#### Returns
`GetAuthorizationModelResponseInterface` 

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
public function getStore(StoreInterface|string $store): GetStoreResponseInterface
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface|string` |  |

#### Returns
`GetStoreResponseInterface` 

### listAuthorizationModels

```php
public function listAuthorizationModels(StoreInterface|string $store, ?string $continuationToken = null, ?int $pageSize = null): ListAuthorizationModelsResponseInterface
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface|string` |  |
| `$continuationToken` | `?string` |  |
| `$pageSize` | `?int` |  |

#### Returns
`ListAuthorizationModelsResponseInterface` 

### listObjects

```php
public function listObjects(StoreInterface|string $store, AuthorizationModelInterface|string $model, string $type, string $relation, string $user, ?object $context = null, ?TupleKeysInterface $contextualTuples = null, ?Consistency $consistency = null): ListObjectsResponseInterface
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface|string` |  |
| `$model` | `AuthorizationModelInterface|string` |  |
| `$type` | `string` |  |
| `$relation` | `string` |  |
| `$user` | `string` |  |
| `$context` | `?object` |  |
| `$contextualTuples` | `?TupleKeysInterface` |  |
| `$consistency` | `?Consistency` |  |

#### Returns
`ListObjectsResponseInterface` 

### listStores

```php
public function listStores(?string $continuationToken = null, ?int $pageSize = null): ListStoresResponseInterface
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$continuationToken` | `?string` |  |
| `$pageSize` | `?int` |  |

#### Returns
`ListStoresResponseInterface` 

### listTupleChanges

```php
public function listTupleChanges(StoreInterface|string $store, ?string $continuationToken = null, ?int $pageSize = null, ?string $type = null, ?DateTimeImmutable $startTime = null): ListTupleChangesResponseInterface
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface|string` |  |
| `$continuationToken` | `?string` |  |
| `$pageSize` | `?int` |  |
| `$type` | `?string` |  |
| `$startTime` | `?DateTimeImmutable` |  |

#### Returns
`ListTupleChangesResponseInterface` 

### listUsers

```php
public function listUsers(StoreInterface|string $store, AuthorizationModelInterface|string $model, string $object, string $relation, UserTypeFiltersInterface $userFilters, ?object $context = null, ?TupleKeysInterface $contextualTuples = null, ?Consistency $consistency = null): ListUsersResponseInterface
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface|string` |  |
| `$model` | `AuthorizationModelInterface|string` |  |
| `$object` | `string` |  |
| `$relation` | `string` |  |
| `$userFilters` | `UserTypeFiltersInterface` |  |
| `$context` | `?object` |  |
| `$contextualTuples` | `?TupleKeysInterface` |  |
| `$consistency` | `?Consistency` |  |

#### Returns
`ListUsersResponseInterface` 

### readAssertions

```php
public function readAssertions(StoreInterface|string $store, AuthorizationModelInterface|string $model): ReadAssertionsResponseInterface
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface|string` |  |
| `$model` | `AuthorizationModelInterface|string` |  |

#### Returns
`ReadAssertionsResponseInterface` 

### readTuples

```php
public function readTuples(StoreInterface|string $store, TupleKeyInterface $tupleKey, ?string $continuationToken = null, ?int $pageSize = null, ?Consistency $consistency = null): ReadTuplesResponseInterface
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface|string` |  |
| `$tupleKey` | `TupleKeyInterface` |  |
| `$continuationToken` | `?string` |  |
| `$pageSize` | `?int` |  |
| `$consistency` | `?Consistency` |  |

#### Returns
`ReadTuplesResponseInterface` 

### writeAssertions

```php
public function writeAssertions(StoreInterface|string $store, AuthorizationModelInterface|string $model, AssertionsInterface $assertions): WriteAssertionsResponseInterface
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface|string` |  |
| `$model` | `AuthorizationModelInterface|string` |  |
| `$assertions` | `AssertionsInterface` |  |

#### Returns
`WriteAssertionsResponseInterface` 

### writeTuples

```php
public function writeTuples(StoreInterface|string $store, AuthorizationModelInterface|string $model, ?TupleKeysInterface $writes = null, ?TupleKeysInterface $deletes = null): WriteTuplesResponseInterface
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$store` | `StoreInterface|string` |  |
| `$model` | `AuthorizationModelInterface|string` |  |
| `$writes` | `?TupleKeysInterface` |  |
| `$deletes` | `?TupleKeysInterface` |  |

#### Returns
`WriteTuplesResponseInterface` 

