# ListObjectsRequestInterface


## Namespace
`OpenFGA\Requests`

## Implements
* [RequestInterface](Requests/RequestInterface.md)



## Methods
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
public function getContextualTuples(): TupleKeysInterface<TupleKeyInterface>
```



#### Returns
TupleKeysInterface&lt;TupleKeyInterface&gt;

### getModel


```php
public function getModel(): ?string
```



#### Returns
?string

### getRelation


```php
public function getRelation(): string
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

### getType


```php
public function getType(): string
```



#### Returns
string

### getUser


```php
public function getUser(): string
```



#### Returns
string

