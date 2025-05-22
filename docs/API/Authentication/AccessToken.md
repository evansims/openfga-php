# AccessToken


## Namespace
`OpenFGA\Authentication`

## Implements
* [AccessTokenInterface](Authentication/AccessTokenInterface.md)
* Stringable



## Methods
### fromResponse

*<small>Implements Authentication\AccessTokenInterface</small>*  

```php
public function fromResponse(Psr\Http\Message\ResponseInterface $response): self
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$response` | `Psr\Http\Message\ResponseInterface` |  |

#### Returns
`self`

### getExpires


```php
public function getExpires(): int
```



#### Returns
`int`

### getScope


```php
public function getScope(): ?string
```



#### Returns
`?string`

### getToken


```php
public function getToken(): string
```



#### Returns
`string`

### isExpired


```php
public function isExpired(): bool
```



#### Returns
`bool`

