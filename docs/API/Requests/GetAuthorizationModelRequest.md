# GetAuthorizationModelRequest


## Namespace
`OpenFGA\Requests`

## Implements
* [GetAuthorizationModelRequestInterface](Requests/GetAuthorizationModelRequestInterface.md)
* [RequestInterface](Requests/RequestInterface.md)



## Methods
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

