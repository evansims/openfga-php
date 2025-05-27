# NetworkException


## Namespace
`OpenFGA\Exceptions`

## Implements
* Stringable
* Throwable
* [ClientThrowable](Exceptions/ClientThrowable.md)



## Methods
### context


```php
public function context(): array<string, mixed>
```



#### Returns
`array<string, mixed>`

### getCode


```php
public function getCode()
```




### getFile


```php
public function getFile(): string
```



#### Returns
`string`

### getLine


```php
public function getLine(): int
```



#### Returns
`int`

### getMessage


```php
public function getMessage(): string
```



#### Returns
`string`

### getPrevious


```php
public function getPrevious(): ?Throwable
```



#### Returns
`?Throwable`

### getTrace


```php
public function getTrace(): array
```



#### Returns
`array`

### getTraceAsString


```php
public function getTraceAsString(): string
```



#### Returns
`string`

### kind


```php
public function kind(): ClientError | AuthenticationError | ConfigurationError | NetworkError | SerializationError
```



#### Returns
`ClientError | AuthenticationError | ConfigurationError | NetworkError | SerializationError`

### request


```php
public function request(): ?Psr\Http\Message\RequestInterface
```



#### Returns
`?Psr\Http\Message\RequestInterface`

### response


```php
public function response(): ?Psr\Http\Message\ResponseInterface
```



#### Returns
`?Psr\Http\Message\ResponseInterface`

