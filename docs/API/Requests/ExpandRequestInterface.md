# ExpandRequestInterface


## Namespace
`OpenFGA\Requests`

## Implements
* [RequestInterface](Requests/RequestInterface.md)



## Methods
### getConsistency


```php
public function getConsistency(): ?Consistency
```



#### Returns
`?Consistency`

### getContextualTuples


```php
public function getContextualTuples(): TupleKeysInterface<TupleKeyInterface>
```



#### Returns
`TupleKeysInterface<TupleKeyInterface>`

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
public function getTupleKey(): [TupleKeyInterface](Models/TupleKeyInterface.md)
```



#### Returns
`[TupleKeyInterface](Models/TupleKeyInterface.md)`

