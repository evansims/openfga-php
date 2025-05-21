# ListAuthorizationModelsResponse


## Namespace
`OpenFGA\Responses`

## Implements
* [ListAuthorizationModelsResponseInterface](Responses/ListAuthorizationModelsResponseInterface.md)
* [ResponseInterface](Responses/ResponseInterface.md)

## Methods
### fromResponse

*<small>Implements Responses\ListAuthorizationModelsResponseInterface</small>*  

```php
public function fromResponse(Psr\Http\Message\ResponseInterface $response, [SchemaValidator](Schema/SchemaValidator.md) $validator): static
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$response` | `Psr\Http\Message\ResponseInterface` |  |
| `$validator` | `[SchemaValidator](Schema/SchemaValidator.md)` |  |

#### Returns
`static` 

### getContinuationToken


```php
public function getContinuationToken(): ?string
```



#### Returns
`?string` 

### getModels


```php
public function getModels(): [AuthorizationModelsInterface](Models/Collections/AuthorizationModelsInterface.md)
```



#### Returns
`[AuthorizationModelsInterface](Models/Collections/AuthorizationModelsInterface.md)` 

### schema

*<small>Implements Responses\ListAuthorizationModelsResponseInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)` 

