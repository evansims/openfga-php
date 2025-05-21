# ExpandRequest


## Namespace
`OpenFGA\Requests`

## Implements
* `OpenFGA\Requests\ExpandRequestInterface`
* `OpenFGA\Requests\RequestInterface`

## Methods
### getConsistency

```php
public function getConsistency(): ?Consistency
```



#### Returns
`?Consistency` 

### getContextualTuples

```php
public function getContextualTuples(): ?TupleKeysInterface
```



#### Returns
`?TupleKeysInterface` 

### getModel

```php
public function getModel(): ?string
```



#### Returns
`?string` 

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

### getTupleKey

```php
public function getTupleKey(): TupleKeyInterface
```



#### Returns
`TupleKeyInterface` 

