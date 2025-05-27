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
public function fromResponse(Psr\Http\Message\ResponseInterface $response, Psr\Http\Message\RequestInterface $request, OpenFGA\Schema\SchemaValidator $validator): self
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$response` | Psr\Http\Message\ResponseInterface |  |
| `$request` | Psr\Http\Message\RequestInterface |  |
| `$validator` | [SchemaValidator](Schema/SchemaValidator.md) |  |

#### Returns
self

### getModel


```php
public function getModel(): ?OpenFGA\Models\AuthorizationModelInterface
```



#### Returns
?[AuthorizationModelInterface](Models/AuthorizationModelInterface.md)

### schema

*<small>Implements Responses\GetAuthorizationModelResponseInterface</small>*  

```php
public function schema(): OpenFGA\Schema\SchemaInterface
```



#### Returns
[SchemaInterface](Schema/SchemaInterface.md)

