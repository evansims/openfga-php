# CreateAuthorizationModelRequest


## Namespace
`OpenFGA\Requests`

## Implements
* [CreateAuthorizationModelRequestInterface](Requests/CreateAuthorizationModelRequestInterface.md)
* [RequestInterface](Requests/RequestInterface.md)



## Methods
### getConditions


```php
public function getConditions(): ?OpenFGA\Models\Collections\ConditionsInterface
```



#### Returns
?[ConditionsInterface](Models/Collections/ConditionsInterface.md)

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
public function getTypeDefinitions(): OpenFGA\Models\Collections\TypeDefinitionsInterface
```



#### Returns
[TypeDefinitionsInterface](Models/Collections/TypeDefinitionsInterface.md)

