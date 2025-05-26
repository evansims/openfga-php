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

