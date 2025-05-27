# CheckRequest


## Namespace
`OpenFGA\Requests`

## Implements
* [CheckRequestInterface](Requests/CheckRequestInterface.md)
* [RequestInterface](Requests/RequestInterface.md)



## Methods
### getAuthorizationModel


```php
public function getAuthorizationModel(): string
```



#### Returns
string

### getConsistency


```php
public function getConsistency(): ?OpenFGA\Models\Enums\Consistency
```



#### Returns
?Consistency

### getContext


```php
public function getContext(): ?object
```



#### Returns
?object

### getContextualTuples


```php
public function getContextualTuples(): ?OpenFGA\Models\Collections\TupleKeysInterface
```



#### Returns
?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)

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

### getTrace


```php
public function getTrace(): ?bool
```



#### Returns
?bool

### getTupleKey


```php
public function getTupleKey(): OpenFGA\Models\TupleKeyInterface
```



#### Returns
[TupleKeyInterface](Models/TupleKeyInterface.md)

