# CreateStoreResponse


## Namespace
`OpenFGA\Responses`

## Implements
* [CreateStoreResponseInterface](Responses/CreateStoreResponseInterface.md)
* [ResponseInterface](Responses/ResponseInterface.md)



## Methods
### fromResponse

*<small>Implements Responses\CreateStoreResponseInterface</small>*  

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

### getCreatedAt


```php
public function getCreatedAt(): DateTimeImmutable
```



#### Returns
`DateTimeImmutable`

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

### getUpdatedAt


```php
public function getUpdatedAt(): DateTimeImmutable
```



#### Returns
`DateTimeImmutable`

### schema

*<small>Implements Responses\CreateStoreResponseInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)`

