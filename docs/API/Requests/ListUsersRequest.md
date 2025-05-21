# ListUsersRequest


## Namespace
`OpenFGA\Requests`

## Implements
* `OpenFGA\Requests\ListUsersRequestInterface`
* `OpenFGA\Requests\RequestInterface`

## Methods
### getConsistency

```php
public function getConsistency(): ?Consistency
```



#### Returns
`?Consistency` 

### getContext

```php
public function getContext(): ?object
```



#### Returns
`?object` 

### getContextualTuples

```php
public function getContextualTuples(): ?TupleKeysInterface
```



#### Returns
`?TupleKeysInterface` 

### getModel

```php
public function getModel(): string
```



#### Returns
`string` 

### getObject

```php
public function getObject(): string
```



#### Returns
`string` 

### getRelation

```php
public function getRelation(): string
```



#### Returns
`string` 

### getRequest

```php
public function getRequest(StreamFactoryInterface $streamFactory): [RequestContext](Network/RequestContext.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$streamFactory` | `StreamFactoryInterface` |  |

#### Returns
`[RequestContext](Network/RequestContext.md)` 

### getStore

```php
public function getStore(): string
```



#### Returns
`string` 

### getUserFilters

```php
public function getUserFilters(): UserTypeFiltersInterface
```



#### Returns
`UserTypeFiltersInterface` 

