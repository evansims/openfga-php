# CheckResponse


## Namespace
`OpenFGA\Responses`

## Implements
* [CheckResponseInterface](Responses/CheckResponseInterface.md)
* [ResponseInterface](Responses/ResponseInterface.md)



## Methods
### fromResponse

*<small>Implements Responses\CheckResponseInterface</small>*  

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

### getAllowed


```php
public function getAllowed(): ?bool
```



#### Returns
?bool

### getResolution


```php
public function getResolution(): ?string
```



#### Returns
?string

### schema

*<small>Implements Responses\CheckResponseInterface</small>*  

```php
public function schema(): OpenFGA\Schema\SchemaInterface
```



#### Returns
[SchemaInterface](Schema/SchemaInterface.md)

