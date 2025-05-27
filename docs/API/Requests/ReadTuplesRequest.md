# ReadTuplesRequest


## Namespace
`OpenFGA\Requests`

## Implements
* [ReadTuplesRequestInterface](Requests/ReadTuplesRequestInterface.md)
* [RequestInterface](Requests/RequestInterface.md)



## Methods
### getConsistency


```php
public function getConsistency(): ?OpenFGA\Models\Enums\Consistency
```



#### Returns
?Consistency

### getContinuationToken


```php
public function getContinuationToken(): ?string
```



#### Returns
?string

### getPageSize


```php
public function getPageSize(): ?int
```



#### Returns
?int

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

### getTupleKey


```php
public function getTupleKey(): OpenFGA\Models\TupleKeyInterface
```



#### Returns
[TupleKeyInterface](Models/TupleKeyInterface.md)

