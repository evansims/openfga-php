# WriteTuplesRequestInterface


## Namespace
`OpenFGA\Requests`

## Implements
* [RequestInterface](Requests/RequestInterface.md)



## Methods
### getDeletes


```php
public function getDeletes(): null|TupleKeysInterface<TupleKeyInterface>
```



#### Returns
null | TupleKeysInterface&lt;TupleKeyInterface&gt;

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
public function getWrites(): null|TupleKeysInterface<TupleKeyInterface>
```



#### Returns
null | TupleKeysInterface&lt;TupleKeyInterface&gt;

