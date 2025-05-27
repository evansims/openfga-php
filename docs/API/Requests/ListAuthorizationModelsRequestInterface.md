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

### getStore


```php
public function getStore(): string
```



#### Returns
string

