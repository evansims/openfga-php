# ListObjectsResponse


## Namespace
`OpenFGA\Responses`

## Implements
* [ListObjectsResponseInterface](Responses/ListObjectsResponseInterface.md)
* [ResponseInterface](Responses/ResponseInterface.md)



## Methods
### fromResponse

*<small>Implements Responses\ListObjectsResponseInterface</small>*  

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

### getObjects


```php
public function getObjects(): array
```



#### Returns
array

### schema

*<small>Implements Responses\ListObjectsResponseInterface</small>*  

```php
public function schema(): OpenFGA\Schema\SchemaInterface
```



#### Returns
[SchemaInterface](Schema/SchemaInterface.md)

