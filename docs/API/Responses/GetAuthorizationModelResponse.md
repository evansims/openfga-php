# GetAuthorizationModelResponse


## Namespace
`OpenFGA\Responses`

## Implements
* [GetAuthorizationModelResponseInterface](Responses/GetAuthorizationModelResponseInterface.md)
* [ResponseInterface](Responses/ResponseInterface.md)

## Methods
### fromResponse

*<small>Implements Responses\GetAuthorizationModelResponseInterface</small>*  

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

### getModel


```php
public function getModel(): ?[AuthorizationModelInterface](Models/AuthorizationModelInterface.md)
```



#### Returns
`?[AuthorizationModelInterface](Models/AuthorizationModelInterface.md)` 

### schema

*<small>Implements Responses\GetAuthorizationModelResponseInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)` 

