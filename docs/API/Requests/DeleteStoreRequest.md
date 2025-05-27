# DeleteStoreRequest


## Namespace
`OpenFGA\Requests`

## Implements
* [DeleteStoreRequestInterface](Requests/DeleteStoreRequestInterface.md)
* [RequestInterface](Requests/RequestInterface.md)



## Methods
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

