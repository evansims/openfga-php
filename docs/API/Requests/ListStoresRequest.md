# ListStoresRequest


## Namespace
`OpenFGA\Requests`

## Implements
* [ListStoresRequestInterface](Requests/ListStoresRequestInterface.md)
* [RequestInterface](Requests/RequestInterface.md)



## Methods
### getContinuationToken


```php
public function getContinuationToken(): ?string
```



#### Returns
?string

### getPageSize


```php
public function getPageSize(): ?int
```



#### Returns
?int

### getRequest


```php
public function getRequest(Psr\Http\Message\StreamFactoryInterface $streamFactory): OpenFGA\Network\RequestContext
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$streamFactory` | StreamFactoryInterface |  |

#### Returns
[RequestContext](Network/RequestContext.md)

