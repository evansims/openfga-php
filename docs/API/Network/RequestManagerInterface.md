# RequestManagerInterface


## Namespace
`OpenFGA\Network`




## Methods
### getHttpClient


```php
public function getHttpClient(): Psr\Http\Client\ClientInterface
```



#### Returns
ClientInterface

### getHttpRequestFactory


```php
public function getHttpRequestFactory(): Psr\Http\Message\RequestFactoryInterface
```



#### Returns
RequestFactoryInterface

### getHttpResponseFactory


```php
public function getHttpResponseFactory(): Psr\Http\Message\ResponseFactoryInterface
```



#### Returns
ResponseFactoryInterface

### getHttpStreamFactory


```php
public function getHttpStreamFactory(): Psr\Http\Message\StreamFactoryInterface
```



#### Returns
StreamFactoryInterface

### request


```php
public function request(OpenFGA\Requests\RequestInterface $request): Psr\Http\Message\RequestInterface
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$request` | [RequestInterface](Requests/RequestInterface.md) |  |

#### Returns
Psr\Http\Message\RequestInterface

### send


```php
public function send(Psr\Http\Message\RequestInterface $request): Psr\Http\Message\ResponseInterface
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$request` | Psr\Http\Message\RequestInterface |  |

#### Returns
Psr\Http\Message\ResponseInterface

