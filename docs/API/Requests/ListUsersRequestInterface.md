# ListUsersRequestInterface


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
public function getModel(): string
```



#### Returns
string

### getObject


```php
public function getObject(): string
```



#### Returns
string

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

### getUserFilters


```php
public function getUserFilters(): UserTypeFiltersInterface<UserTypeFilterInterface>
```



#### Returns
UserTypeFiltersInterface&lt;UserTypeFilterInterface&gt;

