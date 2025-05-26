# ReadAssertionsResponse


## Namespace
`OpenFGA\Responses`

## Implements
* [ReadAssertionsResponseInterface](Responses/ReadAssertionsResponseInterface.md)
* [ResponseInterface](Responses/ResponseInterface.md)



## Methods
### fromResponse

*<small>Implements Responses\ReadAssertionsResponseInterface</small>*  

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

### getAssertions


```php
public function getAssertions(): ?[AssertionsInterface](Models/Collections/AssertionsInterface.md)
```



#### Returns
`?[AssertionsInterface](Models/Collections/AssertionsInterface.md)`

### getModel


```php
public function getModel(): string
```



#### Returns
`string`

### schema

*<small>Implements Responses\ReadAssertionsResponseInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)`

