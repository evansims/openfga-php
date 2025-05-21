# ListAuthorizationModelsRequestInterface


## Namespace
`OpenFGA\Requests`

## Implements
* [RequestInterface](Requests/RequestInterface.md)

## Methods
### getContinuationToken

```php
public function getContinuationToken(): ?string
```



#### Returns
`?string` 

### getPageSize

```php
public function getPageSize(): ?int
```



#### Returns
`?int` 

### getStore

```php
public function getStore(): string
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

