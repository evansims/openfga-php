# CreateAuthorizationModelRequestInterface


## Namespace
`OpenFGA\Requests`

## Implements
* [RequestInterface](Requests/RequestInterface.md)

## Methods
### getConditions


```php
public function getConditions(): ?[ConditionsInterface](Models/Collections/ConditionsInterface.md)
```



#### Returns
`?[ConditionsInterface](Models/Collections/ConditionsInterface.md)` 

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

### getSchemaVersion


```php
public function getSchemaVersion(): SchemaVersion
```



#### Returns
`SchemaVersion` 

### getStore


```php
public function getStore(): string
```



#### Returns
`string` 

### getTypeDefinitions


```php
public function getTypeDefinitions(): [TypeDefinitionsInterface](Models/Collections/TypeDefinitionsInterface.md)
```



#### Returns
`[TypeDefinitionsInterface](Models/Collections/TypeDefinitionsInterface.md)` 

