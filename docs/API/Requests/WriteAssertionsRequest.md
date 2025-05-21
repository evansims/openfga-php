# WriteAssertionsRequest


## Namespace
`OpenFGA\Requests`

## Implements
* `OpenFGA\Requests\WriteAssertionsRequestInterface`
* `OpenFGA\Requests\RequestInterface`

## Methods
### getAssertions

```php
public function getAssertions(): AssertionsInterface
```



#### Returns
`AssertionsInterface` 

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

