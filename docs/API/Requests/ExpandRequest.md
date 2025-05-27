# ExpandRequest


## Namespace
`OpenFGA\Requests`

## Implements
* [ExpandRequestInterface](Requests/ExpandRequestInterface.md)
* [RequestInterface](Requests/RequestInterface.md)



## Methods
### getConsistency


```php
public function getConsistency(): ?OpenFGA\Models\Enums\Consistency
```



#### Returns
?Consistency

### getContextualTuples


```php
public function getContextualTuples(): ?OpenFGA\Models\Collections\TupleKeysInterface
```



#### Returns
?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)

### getModel


```php
public function getModel(): ?string
```



#### Returns
?string

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

