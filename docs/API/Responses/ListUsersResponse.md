# ListUsersResponse


## Namespace
`OpenFGA\Responses`

## Implements
* [ListUsersResponseInterface](Responses/ListUsersResponseInterface.md)
* [ResponseInterface](Responses/ResponseInterface.md)

## Methods
### fromResponse

*<small>Implements Responses\ListUsersResponseInterface</small>*  

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

### getUsers


```php
public function getUsers(): [UsersInterface](Models/Collections/UsersInterface.md)
```



#### Returns
`[UsersInterface](Models/Collections/UsersInterface.md)` 

### schema

*<small>Implements Responses\ListUsersResponseInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)` 

