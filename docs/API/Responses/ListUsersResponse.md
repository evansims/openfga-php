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

### getUsers


```php
public function getUsers(): OpenFGA\Models\Collections\UsersInterface
```



#### Returns
[UsersInterface](Models/Collections/UsersInterface.md)

### schema

*<small>Implements Responses\ListUsersResponseInterface</small>*  

```php
public function schema(): OpenFGA\Schema\SchemaInterface
```



#### Returns
[SchemaInterface](Schema/SchemaInterface.md)

