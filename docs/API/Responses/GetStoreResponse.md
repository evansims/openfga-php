# GetStoreResponse


## Namespace
`OpenFGA\Responses`

## Implements
* [GetStoreResponseInterface](Responses/GetStoreResponseInterface.md)
* [ResponseInterface](Responses/ResponseInterface.md)

## Methods
### fromResponse

*<small>Implements Responses\GetStoreResponseInterface</small>*  

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

### getCreatedAt


```php
public function getCreatedAt(): DateTimeImmutable
```



#### Returns
`DateTimeImmutable` 

### getDeletedAt


```php
public function getDeletedAt(): ?DateTimeImmutable
```



#### Returns
`?DateTimeImmutable` 

### getId


```php
public function getId(): string
```



#### Returns
`string` 

### getName


```php
public function getName(): string
```



#### Returns
`string` 

### getStore


```php
public function getStore(): [StoreInterface](Models/StoreInterface.md)
```



#### Returns
`[StoreInterface](Models/StoreInterface.md)` 

### getUpdatedAt


```php
public function getUpdatedAt(): DateTimeImmutable
```



#### Returns
`DateTimeImmutable` 

### schema

*<small>Implements Responses\GetStoreResponseInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)` 

