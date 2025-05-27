# WriteAssertionsRequest


## Namespace
`OpenFGA\Requests`

## Implements
* [WriteAssertionsRequestInterface](Requests/WriteAssertionsRequestInterface.md)
* [RequestInterface](Requests/RequestInterface.md)



## Methods
### getAssertions


```php
public function getAssertions(): OpenFGA\Models\Collections\AssertionsInterface
```



#### Returns
[AssertionsInterface](Models/Collections/AssertionsInterface.md)

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

