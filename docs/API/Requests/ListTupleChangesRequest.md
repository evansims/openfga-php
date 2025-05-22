# ListTupleChangesRequest


## Namespace
`OpenFGA\Requests`

## Implements
* [ListTupleChangesRequestInterface](Requests/ListTupleChangesRequestInterface.md)
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

### getStartTime


```php
public function getStartTime(): ?DateTimeImmutable
```



#### Returns
`?DateTimeImmutable`

### getStore


```php
public function getStore(): string
```



#### Returns
`string`

### getType


```php
public function getType(): ?string
```



#### Returns
`?string`

