# ListStoresResponse


## Namespace
`OpenFGA\Responses`

## Implements
* [ListStoresResponseInterface](Responses/ListStoresResponseInterface.md)
* [ResponseInterface](Responses/ResponseInterface.md)

## Methods
### fromResponse

*<small>Implements Responses\ListStoresResponseInterface</small>*  

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

### getContinuationToken


```php
public function getContinuationToken(): ?string
```



#### Returns
`?string` 

### getStores


```php
public function getStores(): [StoresInterface](Models/Collections/StoresInterface.md)
```



#### Returns
`[StoresInterface](Models/Collections/StoresInterface.md)` 

### schema

*<small>Implements Responses\ListStoresResponseInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)` 

