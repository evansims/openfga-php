# CreateAuthorizationModelRequestInterface


## Namespace
`OpenFGA\Requests`

## Implements
* [RequestInterface](Requests/RequestInterface.md)



## Methods
### getConditions


```php
public function getConditions(): ConditionsInterface<ConditionInterface>
```



#### Returns
ConditionsInterface&lt;ConditionInterface&gt;

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

### getSchemaVersion


```php
public function getSchemaVersion(): OpenFGA\Models\Enums\SchemaVersion
```



#### Returns
SchemaVersion

### getStore


```php
public function getStore(): string
```



#### Returns
string

### getTypeDefinitions


```php
public function getTypeDefinitions(): TypeDefinitionsInterface<TypeDefinitionInterface>
```



#### Returns
TypeDefinitionsInterface&lt;TypeDefinitionInterface&gt;

