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

