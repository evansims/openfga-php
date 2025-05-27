# ReadAssertionsRequest


## Namespace
`OpenFGA\Requests`

## Implements
* [ReadAssertionsRequestInterface](Requests/ReadAssertionsRequestInterface.md)
* [RequestInterface](Requests/RequestInterface.md)



## Methods
### getModel


```php
public function getModel(): string
```



#### Returns
string

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

