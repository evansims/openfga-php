# CreateAuthorizationModelResponse


## Namespace
`OpenFGA\Responses`

## Implements
* [CreateAuthorizationModelResponseInterface](Responses/CreateAuthorizationModelResponseInterface.md)
* [ResponseInterface](Responses/ResponseInterface.md)



## Methods
### fromResponse

*<small>Implements Responses\CreateAuthorizationModelResponseInterface</small>*  

```php
public function fromResponse(Psr\Http\Message\ResponseInterface $response, [SchemaValidator](Schema/SchemaValidator.md) $validator): self
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$response` | `Psr\Http\Message\ResponseInterface` |  |
| `$validator` | `[SchemaValidator](Schema/SchemaValidator.md)` |  |

#### Returns
`self`

### getModel


```php
public function getModel(): string
```



#### Returns
`string`

### schema

*<small>Implements Responses\CreateAuthorizationModelResponseInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)`

