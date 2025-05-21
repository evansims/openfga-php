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
public function fromResponse(Psr\Http\Message\ResponseInterface $response, [SchemaValidator](Schema/SchemaValidator.md) $validator): static
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$response` | `Psr\Http\Message\ResponseInterface` |  |
| `$validator` | `[SchemaValidator](Schema/SchemaValidator.md)` |  |

#### Returns
`static` 

### getAllowed


```php
public function getAllowed(): ?bool
```



#### Returns
`?bool` 

### getResolution


```php
public function getResolution(): ?string
```



#### Returns
`?string` 

### schema

*<small>Implements Responses\CheckResponseInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)` 

