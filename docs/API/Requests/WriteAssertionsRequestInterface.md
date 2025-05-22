# WriteAssertionsRequestInterface


## Namespace
`OpenFGA\Requests`

## Implements
* [RequestInterface](Requests/RequestInterface.md)



## Methods
### getAssertions


```php
public function getAssertions(): [AssertionsInterface](Models/Collections/AssertionsInterface.md)
```



#### Returns
`[AssertionsInterface](Models/Collections/AssertionsInterface.md)`

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

