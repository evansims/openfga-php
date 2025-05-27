# WriteTuplesRequest


## Namespace
`OpenFGA\Requests`

## Implements
* [WriteTuplesRequestInterface](Requests/WriteTuplesRequestInterface.md)
* [RequestInterface](Requests/RequestInterface.md)



## Methods
### getDeletes


```php
public function getDeletes(): ?OpenFGA\Models\Collections\TupleKeysInterface
```



#### Returns
?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)

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

### getWrites


```php
public function getWrites(): ?OpenFGA\Models\Collections\TupleKeysInterface
```



#### Returns
?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)

