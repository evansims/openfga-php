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
public function fromResponse(Psr\Http\Message\ResponseInterface $response, [SchemaValidator](Schema/SchemaValidator.md) $validator): static
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$response` | `Psr\Http\Message\ResponseInterface` |  |
| `$validator` | `[SchemaValidator](Schema/SchemaValidator.md)` |  |

#### Returns
`static` 

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

