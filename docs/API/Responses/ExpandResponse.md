# ExpandResponse


## Namespace
`OpenFGA\Responses`

## Implements
* [ExpandResponseInterface](Responses/ExpandResponseInterface.md)
* [ResponseInterface](Responses/ResponseInterface.md)

## Methods
### fromResponse

*<small>Implements Responses\ExpandResponseInterface</small>*  

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

### getTree


```php
public function getTree(): ?[UsersetTreeInterface](Models/UsersetTreeInterface.md)
```



#### Returns
`?[UsersetTreeInterface](Models/UsersetTreeInterface.md)` 

### schema

*<small>Implements Responses\ExpandResponseInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)` 

