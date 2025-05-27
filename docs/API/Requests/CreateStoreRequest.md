# CreateStoreRequest


## Namespace
`OpenFGA\Requests`

## Implements
* [CreateStoreRequestInterface](Requests/CreateStoreRequestInterface.md)
* [RequestInterface](Requests/RequestInterface.md)



## Methods
### getName


```php
public function getName(): string
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

