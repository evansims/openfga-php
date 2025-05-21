# CreateAuthorizationModelRequest


## Namespace
`OpenFGA\Requests`

## Implements
* `OpenFGA\Requests\CreateAuthorizationModelRequestInterface`
* `OpenFGA\Requests\RequestInterface`

## Methods
### getConditions

```php
public function getConditions(): ?ConditionsInterface
```



#### Returns
`?ConditionsInterface` 

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
public function getTypeDefinitions(): TypeDefinitionsInterface
```



#### Returns
`TypeDefinitionsInterface` 

