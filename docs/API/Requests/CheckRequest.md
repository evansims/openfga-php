# CheckRequest


## Namespace
`OpenFGA\Requests`

## Implements
* `OpenFGA\Requests\CheckRequestInterface`
* `OpenFGA\Requests\RequestInterface`

## Methods
### getAuthorizationModel

```php
public function getAuthorizationModel(): string
```



#### Returns
`string` 

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

### getTrace

```php
public function getTrace(): ?bool
```



#### Returns
`?bool` 

### getTupleKey

```php
public function getTupleKey(): TupleKeyInterface
```



#### Returns
`TupleKeyInterface` 

