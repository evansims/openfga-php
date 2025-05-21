# RequestManager


## Namespace
`OpenFGA\Network`

## Implements
* [RequestManagerInterface](Network/RequestManagerInterface.md)

## Methods
### getHttpClient


```php
public function getHttpClient(): ClientInterface
```



#### Returns
`ClientInterface` 

### getHttpRequestFactory


```php
public function getHttpRequestFactory(): RequestFactoryInterface
```



#### Returns
`RequestFactoryInterface` 

### getHttpResponseFactory


```php
public function getHttpResponseFactory(): ResponseFactoryInterface
```



#### Returns
`ResponseFactoryInterface` 

### getHttpStreamFactory


```php
public function getHttpStreamFactory(): StreamFactoryInterface
```



#### Returns
`StreamFactoryInterface` 

### handleResponseException

*<small>Implements Network\RequestManagerInterface</small>*  

```php
public function handleResponseException(Psr\Http\Message\ResponseInterface $response): void
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$response` | `Psr\Http\Message\ResponseInterface` |  |

#### Returns
`void` 

### request


```php
public function request([RequestInterface](Requests/RequestInterface.md) $request): Psr\Http\Message\RequestInterface
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `request` | `Psr\Http\Message\RequestInterface` |  |

#### Returns
`Psr\Http\Message\RequestInterface` 

### send


```php
public function send(Psr\Http\Message\RequestInterface $request): Psr\Http\Message\ResponseInterface
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$request` | `Psr\Http\Message\RequestInterface` |  |

#### Returns
`Psr\Http\Message\ResponseInterface` 

