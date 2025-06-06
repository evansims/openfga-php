# ServiceNotFoundException

Exception thrown when a requested service is not found in the service provider. This exception is thrown when attempting to retrieve a service that has not been registered with the service provider. It helps identify configuration issues and missing service registrations during development.

## Namespace

`OpenFGA\DI`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/DI/ServiceNotFoundException.php)

## Implements

* `Throwable`
* `Stringable`

## Methods

#### getCode

```php
public function getCode()

```

#### getFile

```php
public function getFile(): string

```

#### Returns

`string`

#### getLine

```php
public function getLine(): int

```

#### Returns

`int`

#### getMessage

```php
public function getMessage(): string

```

#### Returns

`string`

#### getPrevious

```php
public function getPrevious(): ?Throwable

```

#### Returns

`Throwable` &#124; `null`

#### getTrace

```php
public function getTrace(): array

```

#### Returns

`array`

#### getTraceAsString

```php
public function getTraceAsString(): string

```

#### Returns

`string`
