# WriteTuplesRequest


## Namespace
`OpenFGA\Requests`

## Implements
* `OpenFGA\Requests\WriteTuplesRequestInterface`
* `OpenFGA\Requests\RequestInterface`

## Methods
### getDeletes

```php
public function getDeletes(): ?TupleKeysInterface
```



#### Returns
`?TupleKeysInterface` 

### getModel

```php
public function getModel(): string
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

### getWrites

```php
public function getWrites(): ?TupleKeysInterface
```



#### Returns
`?TupleKeysInterface` 

