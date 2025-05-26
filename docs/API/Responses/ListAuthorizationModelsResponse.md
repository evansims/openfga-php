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
public function fromResponse(Psr\Http\Message\ResponseInterface $response, Psr\Http\Message\RequestInterface $request, [SchemaValidator](Schema/SchemaValidator.md) $validator): self
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$response` | `Psr\Http\Message\ResponseInterface` |  |
| `$request` | `Psr\Http\Message\RequestInterface` |  |
| `$validator` | `[SchemaValidator](Schema/SchemaValidator.md)` |  |

#### Returns
`self`

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

