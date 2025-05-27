# ClientCredentialAuthentication


## Namespace
`OpenFGA\Authentication`

## Implements
* [AuthenticationInterface](Authentication/AuthenticationInterface.md)
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

